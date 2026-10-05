namespace RSSMBackend.Models;
public class ImportFileResponse<T> {
    public string Message {get; set;}
    public List<T> Data {get; set;}
    public ImportFileResponse(string message, List<T> data) {
        Message = message;
        Data = data;
    }
}