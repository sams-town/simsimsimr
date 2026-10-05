using MySql.Data.MySqlClient;

namespace RSSMBackend.Services.KhanzaSync;

public interface IKhanzaConnectionFactory
{
    /// <summary>Buka koneksi ke database Khanza. Sesi dibuat READ ONLY - modul ini tidak pernah menulis ke Khanza.</summary>
    Task<MySqlConnection> OpenReadOnlyAsync(int branchId);
}

public class KhanzaConnectionFactory : IKhanzaConnectionFactory
{
    private readonly IOptionService _optionService;
    private readonly ILogger<KhanzaConnectionFactory> _logger;

    public KhanzaConnectionFactory(IOptionService optionService, ILogger<KhanzaConnectionFactory> logger)
    {
        _optionService = optionService;
        _logger = logger;
    }

    public async Task<MySqlConnection> OpenReadOnlyAsync(int branchId)
    {
        var host = await _optionService.GetByName("KHANZA_DB_HOST", branchId);
        var port = await _optionService.GetByName("KHANZA_DB_PORT", branchId);
        var name = await _optionService.GetByName("KHANZA_DB_NAME", branchId);
        var user = await _optionService.GetByName("KHANZA_DB_USER", branchId);
        var pass = await _optionService.GetByName("KHANZA_DB_PASS", branchId);

        if (string.IsNullOrWhiteSpace(host?.OptionValue) || string.IsNullOrWhiteSpace(name?.OptionValue)
            || string.IsNullOrWhiteSpace(user?.OptionValue))
            throw new ApplicationException("Konfigurasi database Khanza belum diatur. Silakan atur di menu Pengaturan.");

        // Builder, bukan string interpolasi, supaya karakter seperti ';' di password tidak merusak
        // atau menyisipkan opsi koneksi.
        var csb = new MySqlConnectionStringBuilder
        {
            Server = host!.OptionValue,
            Port = uint.TryParse(port?.OptionValue, out var p) ? p : 3306,
            Database = name!.OptionValue,
            UserID = user!.OptionValue,
            Password = pass?.OptionValue ?? "",
            ConnectionTimeout = 15,
            DefaultCommandTimeout = 120,
            // Khanza banyak menyimpan "0000-00-00"; tanpa ini pembacaan baris itu melempar exception.
            ConvertZeroDateTime = true
        };

        var conn = new MySqlConnection(csb.ConnectionString);
        try
        {
            await conn.OpenAsync();
        }
        catch (Exception e)
        {
            await conn.DisposeAsync();
            _logger.LogError(e, "Gagal membuka koneksi Khanza {Host}", csb.Server);
            throw new ApplicationException($"Tidak dapat terhubung ke database Khanza ({csb.Server}): {e.Message}");
        }

        try
        {
            await using var cmd = new MySqlCommand("SET SESSION TRANSACTION READ ONLY", conn);
            await cmd.ExecuteNonQueryAsync();
        }
        catch (Exception e)
        {
            // Semua query modul ini SELECT, jadi kegagalan mengunci sesi tidak menghalangi pembacaan.
            _logger.LogWarning(e, "Tidak bisa mengunci sesi Khanza menjadi READ ONLY");
        }

        return conn;
    }
}
