namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;

[ApiController]
[Route("[controller]")]
public class HomeController: BaseController
{
    private readonly ILogger<HomeController> _logger;
    private const string TAG = "Home";

    public HomeController(ILogger<HomeController> logger,IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor){
        _logger = logger;
    }

    [Route("/")] 
    [HttpGet()]
    public IActionResult index(){
        
        return _Ok("Backoffice V1.0.0");
      
    }
}


