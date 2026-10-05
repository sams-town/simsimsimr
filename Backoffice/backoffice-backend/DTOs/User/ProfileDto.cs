namespace RSSMBackend.DTOs.User;

public class UpdateProfileDto
{
    public string DisplayName { get; set; } = "";
    public string Email { get; set; } = "";
    public string? Phone { get; set; }
    public string? ProfilePicture { get; set; }
}

public class ChangePasswordDto
{
    public string CurrentPassword { get; set; } = "";
    public string NewPassword { get; set; } = "";
    public string ConfirmPassword { get; set; } = "";
}

public class ProfileResponse
{
    public int UserId { get; set; }
    public string DisplayName { get; set; } = "";
    public string Email { get; set; } = "";
    public string? Phone { get; set; }
    public string? ProfilePicture { get; set; }
    public int? RoleId { get; set; }
    public string? RoleName { get; set; }
}
