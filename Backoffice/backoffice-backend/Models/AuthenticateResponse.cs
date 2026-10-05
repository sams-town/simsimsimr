namespace RSSMBackend.Models;

using RSSMBackend.Entities;

public class AuthenticateResponse
{
    public int UserID { get; set; }
    public string Username { get; set; }
    public string Email { get; set; }
    public string Token { get; set; }
    public string DisplayName { get; set; }
    public string Status { get; set; }
    public int? BranchId { get; set; }
    public DateTime? LastLogin { get; set; }

    public Role? role { get; set; }

    public AuthenticateResponse(User user, string token)
    {
        UserID = user.UserID;
        Email = user.Email;
        LastLogin = user.LastLogin;
        Token = token;
        Status = user.Status;
        DisplayName = user.DisplayName;
        BranchId = user.BranchId;
    }
}