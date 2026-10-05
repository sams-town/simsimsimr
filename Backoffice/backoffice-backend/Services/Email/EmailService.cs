
using System.Net;
using System.Net.Mail;
using Newtonsoft.Json;
using RSSMBackend.Models;
using RSSMBackend.Services;
using Microsoft.Extensions.Logging;

namespace RSSMBackend.Services;

public class EmailService : IEmailService
{
    private readonly IOptionService _optionService;
    private readonly ILogger<EmailService> _logger;

    public EmailService(IOptionService optionService, ILogger<EmailService> logger)
    {
        _optionService = optionService;
        _logger = logger;
    }

    private async Task<SmtpConfig> GetConfigAsync(int branchId)
    {
        var option = await _optionService.GetByName("SMTP_CONFIG", branchId);
        if (option == null)
            option = await _optionService.GetByName("SMTP_CONFIG", 0);

        if (option == null || string.IsNullOrEmpty(option.OptionValue))
            throw new Exception("SMTP Configuration not found.");

        return JsonConvert.DeserializeObject<SmtpConfig>(option.OptionValue);
    }

    public async Task SendEmailAsync(string to, string subject, string body, int branchId = 1)
    {
        try
        {
            var config = await GetConfigAsync(branchId);
            await SendInternalAsync(config, to, subject, body);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to send email to {To}", to);
            throw;
        }
    }

    public async Task SendEmailTestAsync(SmtpConfig config, string to)
    {
        try
        {
            await SendInternalAsync(config, to, "Test Email from Backoffice", "This is a test email from your Backoffice system.");
        }
        catch (Exception ex)
        {
             _logger.LogError(ex, "Failed to send test email to {To}", to);
            throw;
        }
    }

    private async Task SendInternalAsync(SmtpConfig config, string to, string subject, string body)
    {
        using (var message = new MailMessage())
        {
            message.From = new MailAddress(config.FromEmail, config.FromName);
            message.To.Add(new MailAddress(to));
            message.Subject = subject;
            message.Body = body;
            message.IsBodyHtml = true;

            using (var client = new SmtpClient(config.Host, config.Port))
            {
                client.EnableSsl = config.Encryption?.ToLower() == "tls" || config.Encryption?.ToLower() == "ssl";
                client.Credentials = new NetworkCredential(config.Username, config.Password);
                
                // For TLS on port 587, EnableSsl is usually enough. 
                // Some servers might need specific protocol settings but default is usually fine for StartTLS.
                
                await client.SendMailAsync(message);
            }
        }
    }
}
