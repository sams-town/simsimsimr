namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class OptionController: BaseController
{
    private IOptionService optionService;
    private readonly IEmailService _emailService;
    private readonly ILogger<OptionController> _logger;
    private const string TAG = "Option {0}";
    private const string LOG = "Option {x}";
    public OptionController(IOptionService optionService, IEmailService emailService, ILogger<OptionController> logger,IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor){
        this.optionService = optionService;
        _emailService = emailService;
        _logger = logger;
    }

    [Authorize(Page.CfgOption,Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] Option item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Body Null"));

            if(string.IsNullOrEmpty(item.OptionName))
                return _BadRequest(string.Format(TAG,"The 'OptionName' is required"));


            var id = await optionService.Add(item,userSession.BranchId ?? 0);
            return _Ok(id,string.Format(TAG, "data has been successfully saved"));
            
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
   
    [Authorize(Page.CfgOption,Action.Read)]
    [HttpGet("type/{typename}")]
    public async Task<IActionResult> GetByTypeName(string typename)
    {
        try
        {
            if (string.IsNullOrEmpty(typename))
                return _BadRequest(string.Format(TAG,"Typename Null"));

            var data = await optionService.GetByType(typename,userSession.BranchId ?? 0);
            if (data == null)
                return _NotFound(string.Format(TAG,"Not Found"));

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.CfgOption,Action.Read)]
    [HttpGet("{name}")]
    public async Task<IActionResult> GetByName(string name)
    {
        try
        {
            if (string.IsNullOrEmpty(name))
                return _BadRequest(string.Format(TAG,"Name Null"));

            var data = await optionService.GetByName(name,userSession.BranchId ?? 0);
            if (data == null)
                return _NotFound(string.Format(TAG,"Not Found"));

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.CfgOption,Action.Update)]
    [HttpPut]
    public async Task<IActionResult> UpdateOne([FromBody] Option item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Body Null"));

            var id = await optionService.Update(item,userSession.BranchId ?? 0);
            return _Ok(id,string.Format(TAG, "data has been successfully updated"));
            
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.CfgOption,Action.Update)]
    [HttpPut("bulk")]
    public async Task<IActionResult> UpdateBulk([FromBody] List<Option> item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Body Null"));

            var id = await optionService.Update(item,userSession.BranchId ?? 0);
            return _Ok(id,string.Format(TAG, "data has been successfully updated"));
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.CfgOption,Action.Delete)]
    [HttpDelete("{name}")]
    public async Task<IActionResult> Delete(string name)
    {
         try
        {
            var data = await optionService.GetByName(name,userSession.BranchId ?? 0);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            var id = await optionService.Remove(data);   
            return _Ok(id,string.Format(TAG, "data has been successfully deleted"));
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }


    [Authorize(Page.CfgOption, Action.Update)]
    [HttpPost("TestSmtp")]
    public async Task<IActionResult> TestSmtp([FromBody] SmtpConfig config)
    {
        try
        {
            if (config == null)
                return _BadRequest(string.Format(TAG, "Config Null"));

            // Use the config provided to send a test email to the sender (or the configured FromEmail)
            if (string.IsNullOrEmpty(config.FromEmail))
                return _BadRequest("From Email is required");

            await _emailService.SendEmailTestAsync(config, config.FromEmail);
            return _Ok(true, "Test email sent successfully to " + config.FromEmail);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest("Failed to send email: " + e.Message);
        }
    }
}


