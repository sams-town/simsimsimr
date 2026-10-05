
using Serilog;
using Serilog.Events;
using Microsoft.OpenApi.Models;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.DbContext;
using RSSMBackend.Helpers;
using RSSMBackend.Services;
using Quartz;

var builder = WebApplication.CreateBuilder(args);

// Configure logger
Log.Logger = new LoggerConfiguration()
    .WriteTo.Console()
    //.WriteTo.File("logs/error.log", restrictedToMinimumLevel: LogEventLevel.Warning)
    //.WriteTo.File("logs/all-daily_.log", rollingInterval: RollingInterval.Day)
    .MinimumLevel.Override("Microsoft.EntityFrameworkCore.Database.Command", LogEventLevel.Warning)
    .MinimumLevel.Override("Microsoft.EntityFrameworkCore.Query", LogEventLevel.Warning)
    .MinimumLevel.Override("Microsoft.EntityFrameworkCore.Database.Connection", LogEventLevel.Warning)
    .MinimumLevel.Debug()
    .CreateBootstrapLogger();
builder.Host.UseSerilog();

// Configure database
builder.Services.AddDbContext<RSSMDbContext>(options =>
{
    options.UseNpgsql(builder.Configuration.GetConnectionString("MyConnection"));
});
builder.Services.AddDbContext<ZeemarDbContext>(options =>
{
    var cs = builder.Configuration.GetConnectionString("ZeemarConnection");
    options.UseMySql(cs, ServerVersion.AutoDetect(cs));
});

builder.Services.AddControllers().AddJsonOptions( c => {
    c.JsonSerializerOptions.Converters.Add(new System.Text.Json.Serialization.JsonStringEnumConverter());
    c.JsonSerializerOptions.NumberHandling = System.Text.Json.Serialization.JsonNumberHandling.AllowNamedFloatingPointLiterals;
});
builder.Services.AddEndpointsApiExplorer();

// Configure swagger
builder.Services.AddSwaggerGen(c =>
{
    c.SwaggerDoc("v1", new OpenApiInfo { Title = "Backoffice API", Version = "v1" });
    c.AddSecurityDefinition("bearer", new OpenApiSecurityScheme
    {
        Type = SecuritySchemeType.Http,
        BearerFormat = "JWT",
        In = ParameterLocation.Header,
        Scheme = "bearer"
    });
    c.OperationFilter<HeaderFilter>();
});

builder.WebHost.ConfigureKestrel(options =>
{
    options.Limits.KeepAliveTimeout = TimeSpan.FromMinutes(10);
    options.Limits.RequestHeadersTimeout = TimeSpan.FromMinutes(10);
});

// Configure Quartz
builder.Services.Configure<QuartzOptions>(builder.Configuration.GetSection("Quartz"));
builder.Services.Configure<QuartzOptions>(options =>
{
    options.Scheduling.IgnoreDuplicates = true; // default: false
    options.Scheduling.OverWriteExistingData = true; // default: true
});

builder.Services.AddQuartz(q =>
{
    q.UseMicrosoftDependencyInjectionJobFactory();
    var jobKey = new JobKey("AlwaysOn");
    q.AddJob<AlwaysOn>(opts => opts.WithIdentity(jobKey));

    q.AddTrigger(opts => opts
        .ForJob(jobKey)
        .WithIdentity("AlwaysOn-trigger")
        .WithCronSchedule(builder.Configuration["AppSettings:AlwaysOn"])
    );

    // [DINONAKTIFKAN] Checkpoint saldo bulanan otomatis.
    // Kebijakan: trx_balance HANYA boleh diisi manual lewat halaman Saldo Awal (/saldo-awal)
    // agar data saldo tidak "ngaco" oleh penulisan otomatis. Job ini dimatikan sengaja.
    // var checkpointKey = new JobKey("MonthlyCheckpoint");
    // q.AddJob<MonthlyCheckpointJob>(opts => opts.WithIdentity(checkpointKey));
    // q.AddTrigger(opts => opts
    //     .ForJob(checkpointKey)
    //     .WithIdentity("MonthlyCheckpoint-trigger")
    //     .WithCronSchedule(builder.Configuration["AppSettings:MonthlyCheckpoint"] ?? "0 0 1 1 * ?")
    // );

    // Antrian import Jurnal Umum: ambil job 'pending' tiap 10 detik.
    // Fallback "??" wajib -- key ini tidak ada di appsettings.Development/Local, dan
    // WithCronSchedule(null) melempar saat startup.
    var journalImportKey = new JobKey("JournalImport");
    q.AddJob<JournalImportWorker>(opts => opts.WithIdentity(journalImportKey));
    q.AddTrigger(opts => opts
        .ForJob(journalImportKey)
        .WithIdentity("JournalImport-trigger")
        .WithCronSchedule(builder.Configuration["AppSettings:JournalImport"] ?? "0/10 * * * * ?")
    );
});
builder.Services.AddQuartzHostedService(q => q.WaitForJobsToComplete = true);



builder.Services.Configure<AppSettings>(builder.Configuration.GetSection("AppSettings"));
builder.Services.AddHttpContextAccessor();

// Configure Services
ServiceInjector.Inject(builder.Services);

var app = builder.Build();

// Configure the HTTP request pipeline.
if (app.Environment.IsDevelopment()) 
{
    app.UseSwagger();
    app.UseSwaggerUI();
}

// CORS must be first for static files to work cross-origin
app.UseCors(x => x
        .AllowAnyOrigin()
        .AllowAnyMethod()
        .AllowAnyHeader());

// Enable static file serving for uploaded images
app.UseStaticFiles();

app.UseHttpsRedirection();
app.UseAuthentication();
app.UseAuthorization();
app.MapControllers();

app.UseMiddleware<JwtMiddleware>();


app.Run();
