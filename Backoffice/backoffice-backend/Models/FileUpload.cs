
namespace RSSMBackend.Models;
public class FileUpload
{
    public IFormFile File { get; set; }
    public string? ImportMode { get; set; } // "truncate", "skip", "upsert"
}