
using System.Security.Cryptography;
using System.Text;
using Microsoft.IdentityModel.Tokens;
using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using RSSMBackend.Entities;
using System.Globalization;
namespace RSSMBackend.Helpers;

public class Utils
{
    public static string HashPassword(string password, byte[] salt)
    {
        using (var sha256 = new SHA256Managed())
        {
            byte[] combinedBytes = Encoding.UTF8.GetBytes(password).Concat(salt).ToArray();
            byte[] hashedBytes = sha256.ComputeHash(combinedBytes);
            return Convert.ToBase64String(hashedBytes);
        }
    }
    public static bool VerifyPassword(string enteredPassword, string storedHash, byte[] salt)
    {

        string enteredPasswordHash = HashPassword(enteredPassword, salt);
        return enteredPasswordHash == storedHash;
    }

    public static byte[] GenerateSalt()
    {
        byte[] salt = new byte[32];
        using (var rng = new RNGCryptoServiceProvider())
        {
            rng.GetBytes(salt);
        }
        return salt;
    }
    public static string generateJwtToken(User user, String secret)
    {
        var tokenHandler = new JwtSecurityTokenHandler();
        var key = Encoding.ASCII.GetBytes(secret);
        var claims = new List<Claim>
        {
            new Claim("id", user.UserID.ToString()),
            new Claim("branchId", (user.BranchId ?? 1).ToString())
        };
        var tokenDescriptor = new SecurityTokenDescriptor
        {
            Subject = new ClaimsIdentity(claims),
            Expires = DateTime.UtcNow.AddHours(24),
            SigningCredentials = new SigningCredentials(new SymmetricSecurityKey(key), SecurityAlgorithms.HmacSha256Signature)
        };
        var token = tokenHandler.CreateToken(tokenDescriptor);
        return tokenHandler.WriteToken(token);
    }

    private static readonly object _lock = new object();
    private static readonly Dictionary<DateTime, int> _sequenceNumbers = new Dictionary<DateTime, int>();

    public static string getCOAByDepartmentName(string deparment)
    {
        Dictionary<string, string> MAPPING_COA = new Dictionary<string, string>
        {

            { "FARMASI", "401201003" },
            { "HPPFARMASI", "501007" },
            { "PERSEDIAANFARMASI", "105001" },
            { "RADIOLOGI", "401201010" },   // jasamedis 70 : 30
            { "FISIOTERAPI", "401201014" }, // jasamedis
            { "LABORATORIUM", "401201011" },// jasamedis
            { "POLIKLINIK", "401201007" },  // jasamedis
            { "ADMINISTRASI", "401201001" },
            { "PHYSIOTHERAPY", "401201014" },
            { "HPPDOKTER", "503001001" },
            { "BANK MANDIRI", "101003002" },
            { "BANK BCA", "101003005" },
            { "BANK BRI", "101003006" },
            { "BANK BRI CUT MUTIAH", "101003007" },
            { "BANK BRI USD", "101003008" },
            { "MAYBANK", "101003009" },
            { "BANK DKI", "101003010" },
            { "BANK BTN", "101003011" }

        };

        return MAPPING_COA.GetValueOrDefault(deparment, "722099"); // Pendapatan Lainnya
    }

    public static Coa getCOAInvoice()
    {
        return new Coa
        {
            CoaCode = "103005",
            CoaName = "PIUTANG PASIEN DALAM PELAYANAN",
            ParentCode = "103",
            ParentName = "PIUTANG USAHA"
        };
    }

    public static string GenerateTransactionCode(string prefix, string lastGeneratedCode = null)
    {
        if (string.IsNullOrEmpty(prefix))
        {
            throw new ArgumentException("Prefix cannot be null or empty.", nameof(prefix));
        }

        lock (_lock)
        {
            var now = DateTime.UtcNow.Date;
            if (lastGeneratedCode != null)
            {
                var lastGeneratedDate = DateTime.ParseExact(lastGeneratedCode.Substring(3, 8), "yyyyMMdd", null);
                if (lastGeneratedDate == now)
                {
                    var lastSequenceNumber = int.Parse(lastGeneratedCode.Substring(11));
                    _sequenceNumbers[now] = lastSequenceNumber;
                }
            }

            if (!_sequenceNumbers.ContainsKey(now))
            {
                _sequenceNumbers[now] = 1;
            }
            else
            {
                _sequenceNumbers[now]++;
            }

            var sequenceNumber = _sequenceNumbers[now];
            return $"{prefix}{now:yyyyMMdd}{sequenceNumber:D4}";
        }
    }
    
    public static string ToTitleCase(string? input)
    {
        if (string.IsNullOrWhiteSpace(input))
            return string.Empty;

        return CultureInfo.CurrentCulture.TextInfo.ToTitleCase(input.ToLower());
    }
}

