namespace RSSMBackend.DbContext;

using Microsoft.EntityFrameworkCore;
using RSSMBackend.Entities;

public class ZeemarDbContext : DbContext
{
    public ZeemarDbContext(DbContextOptions<ZeemarDbContext> context) : base(context) { }
    public DbSet<JobJournalResult> JobJournalResults { get; set; }
    public DbSet<JobTransactionResult> JobTransactionResults { get; set; }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder.Entity<JobJournalResult>().HasNoKey();
        modelBuilder.Entity<JobTransactionResult>().HasNoKey();
        base.OnModelCreating(modelBuilder);
    }
}
