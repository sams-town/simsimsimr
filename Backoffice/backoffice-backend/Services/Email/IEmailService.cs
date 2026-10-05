
namespace RSSMBackend.Services;
using RSSMBackend.Models;

public interface IEmailService
{
    Task SendEmailAsync(string to, string subject, string body, int branchId = 1);
    Task SendEmailTestAsync(SmtpConfig config, string to);
}
