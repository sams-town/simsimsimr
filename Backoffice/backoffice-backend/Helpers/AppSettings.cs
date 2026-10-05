namespace RSSMBackend.Helpers;

public class AppSettings
{
    public string Secret { get; set; }
    public string BackendUrl { get; set; }
    public string FrontendUrl { get; set; } = "http://localhost:3000";
}