namespace RSSMBackend.Controllers;
using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Helpers;
using System.IO;

[Route("[controller]")]
[ApiController]
public class DownloadController : BaseController
{

    private const string TAG = "DownloadController {error}";
 private readonly ILogger<DownloadController> _logger;
    public DownloadController(ILogger<DownloadController> logger,IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor){
        _logger = logger;
    }
    [HttpGet("{filename}")]
    public IActionResult DownloadFile(string filename)
    {
       try
        {
            string projectDirectory = AppContext.BaseDirectory;
            string downloadPath = projectDirectory + @"Files/Download/";
            string filePath = downloadPath + filename;
            _logger.LogInformation("filePath {filePath}", filePath);

            if (System.IO.File.Exists(filePath))
            {
                var fileBytes = System.IO.File.ReadAllBytes(filePath);
                return File(fileBytes, "application/octet-stream", filename);
            }
            else
            {
                return _NotFound("");
            }
        }
        catch (Exception e)
        {
            _logger.LogError(TAG, e);
            return _BadRequest(e.Message);
        }
        
    }

    [HttpGet("coa-template")]
    public IActionResult template()
    {
       
        string projectDirectory = AppContext.BaseDirectory;
        string downloadPath = projectDirectory + @"Files/Templates/";
        string filePath = downloadPath + "COA_TEMPLATE_2024.xlsx";
        if (System.IO.File.Exists(filePath))
        {
            var fileBytes = System.IO.File.ReadAllBytes(filePath);
            return File(fileBytes, "application/octet-stream", "COA_TEMPLATE_2024.xlsx");
        }
        else
        {
            return _NotFound("");
        }
    }
}