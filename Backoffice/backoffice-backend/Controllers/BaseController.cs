namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using OfficeOpenXml.FormulaParsing.Excel.Functions.Text;
using RSSMBackend.Entities;

public class BaseController: ControllerBase {

    public User userSession;
    public BaseController(IHttpContextAccessor httpContextAccessor)
    {
         userSession = (User?)httpContextAccessor.HttpContext.Items["User"];
    }
    protected IActionResult _Ok(Object data){
        return Ok(new {  message = "Success" , data = data });
    }

    protected IActionResult _Ok(Object data,String message){
        return Ok(new {  message = message , data = data });
    }

    protected IActionResult _Ok(Object data,int count){
        return Ok(new {  message = "Success" , data = data ,totalData = count});
    }
    protected IActionResult _BadRequest(Object _message){
        if(_message == null)
            _message = "Somethings Wrong, Please Contact Your Administrator";

        return BadRequest(new {  message = _message });
    }

    protected IActionResult _NotFound(Object _message){
        return NotFound(new {  message = _message });
    }

    protected IActionResult _File(byte[] fileContent, string mimeType, string fileName) {
        switch (mimeType) {
            case "csv" :
                mimeType = "text/csv";
                break;
             case "xlsx" :
                mimeType = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                break;    
        }

        return File(fileContent, mimeType, fileName);
    }
}