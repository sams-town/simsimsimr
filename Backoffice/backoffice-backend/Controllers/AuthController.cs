namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Models;
using RSSMBackend.DTOs.User;
using RSSMBackend.Validation;
using RSSMBackend.Entities;
using Microsoft.Extensions.Options;
using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using Microsoft.IdentityModel.Tokens;
using System.Text;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class AuthController: BaseController
{
    private IUserService userService;
    private readonly IEmailService _emailService;
    private readonly AppSettings _appSettings;
    private readonly ILogger<AuthController> _logger;
    private const string TAG = "Auth {error}";
    IRoleService roleService;
    public AuthController(IUserService userService,IRoleService roleService, IEmailService emailService, IOptions<AppSettings> appSettings, ILogger<AuthController> logger,IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor){
        this.userService = userService;
        this.roleService = roleService;
        _emailService = emailService;
        _appSettings = appSettings.Value;
        _logger = logger;
    }

    [HttpPost("login")]
    public async Task<IActionResult> Authenticate(AuthenticateRequest model)
    {
        try{
            var response =  userService.Authenticate(model);
            if (response == null){
                _logger.LogError(TAG, "Email or password is incorrect");
                return _BadRequest("Email or password is incorrect");
            }

            var user = await userService.GetByID(response.UserID,response.BranchId);
            var role = await roleService.GetByID(user.RoleID,response.BranchId);
            if (role != null){
                role.AccessList =  await roleService.GetPageAccessByRoleID(role.RoleID); 
                response.role = role;
            }
            
            return _Ok(response);

        } catch (Exception e){
             _logger.LogError(TAG, e);
            return _BadRequest(e.Message);
        }
       
    }

    [HttpGet("profile")]
    public async Task<IActionResult> GetProfile()
    {
        try
        {
            if (userSession == null)
                return Unauthorized(new { message = "Unauthorized" });

            var profile = await userService.GetProfile(userSession.UserID);
            if (profile == null)
                return _NotFound("Profile not found");

            // Convert relative path to absolute URL
            if (!string.IsNullOrEmpty(profile.ProfilePicture) && !profile.ProfilePicture.StartsWith("http"))
            {
                var baseUrl = $"{Request.Scheme}://{Request.Host}";
                profile.ProfilePicture = $"{baseUrl}{profile.ProfilePicture}";
            }

            return _Ok(profile);
        }
        catch (Exception e)
        {
            _logger.LogError(TAG, e);
            return _BadRequest(e.Message);
        }
    }

    [HttpPut("profile")]
    public async Task<IActionResult> UpdateProfile([FromBody] UpdateProfileDto dto)
    {
        try
        {
            if (userSession == null)
                return Unauthorized(new { message = "Unauthorized" });

            if (dto == null)
                return _BadRequest("Request body is empty");

            var userId = await userService.UpdateProfile(userSession.UserID, dto);
            return _Ok(userId, "Profile updated successfully");
        }
        catch (Exception e)
        {
            _logger.LogError(TAG, e);
            return _BadRequest(e.Message);
        }
    }

    [HttpPut("password")]
    public async Task<IActionResult> ChangePassword([FromBody] ChangePasswordDto dto)
    {
        try
        {
            if (userSession == null)
                return Unauthorized(new { message = "Unauthorized" });

            if (dto == null)
                return _BadRequest("Request body is empty");

            if (string.IsNullOrEmpty(dto.CurrentPassword) || string.IsNullOrEmpty(dto.NewPassword))
                return _BadRequest("Current password and new password are required");

            if (dto.NewPassword != dto.ConfirmPassword)
                return _BadRequest("New password and confirmation do not match");

            await userService.ChangePassword(userSession.UserID, dto);
            return _Ok(null, "Password changed successfully");
        }
        catch (Exception e)
        {
            _logger.LogError(TAG, e);
            return _BadRequest(e.Message);
        }
    }

    [HttpPost("upload-photo")]
    public async Task<IActionResult> UploadProfilePhoto(IFormFile file)
    {
        try
        {
            if (userSession == null)
                return Unauthorized(new { message = "Unauthorized" });

            if (file == null || file.Length == 0)
                return _BadRequest("No file uploaded");

            // Validate file type
            var allowedTypes = new[] { "image/jpeg", "image/png", "image/gif", "image/webp" };
            if (!allowedTypes.Contains(file.ContentType))
                return _BadRequest("Invalid file type. Only JPEG, PNG, GIF, WEBP allowed.");

            // Max 2MB
            if (file.Length > 2 * 1024 * 1024)
                return _BadRequest("File size exceeds 2MB limit.");

            // Generate unique filename
            var fileName = $"{userSession.UserID}_{DateTime.UtcNow.Ticks}{Path.GetExtension(file.FileName)}";
            var uploadPath = Path.Combine(Directory.GetCurrentDirectory(), "wwwroot", "uploads", "profiles");
            
            if (!Directory.Exists(uploadPath))
                Directory.CreateDirectory(uploadPath);

            var filePath = Path.Combine(uploadPath, fileName);

            using (var stream = new FileStream(filePath, FileMode.Create))
            {
                await file.CopyToAsync(stream);
            }

            // Update user profile picture URL (store relative path in DB)
            var profilePictureUrl = $"/uploads/profiles/{fileName}";
            await userService.UpdateProfilePicture(userSession.UserID, profilePictureUrl);

            // Return absolute URL in response
            var baseUrl = $"{Request.Scheme}://{Request.Host}";
            var absoluteUrl = $"{baseUrl}{profilePictureUrl}";

            return _Ok(new { url = absoluteUrl }, "Profile picture uploaded successfully");
        }
        catch (Exception e)
        {
            _logger.LogError(TAG, e);
            return _BadRequest(e.Message);
        }
    }

    [HttpDelete("photo")]
    public async Task<IActionResult> DeleteProfilePhoto()
    {
        try
        {
            if (userSession == null)
                return Unauthorized(new { message = "Unauthorized" });

            // Get current profile picture path
            var profile = await userService.GetProfile(userSession.UserID);
            if (profile?.ProfilePicture != null && !profile.ProfilePicture.StartsWith("http"))
            {
                // Delete file from disk
                var filePath = Path.Combine(Directory.GetCurrentDirectory(), "wwwroot", profile.ProfilePicture.TrimStart('/'));
                if (System.IO.File.Exists(filePath))
                {
                    System.IO.File.Delete(filePath);
                }
            }

            // Clear profile picture in database
            await userService.UpdateProfilePicture(userSession.UserID, null);

            return _Ok(null, "Profile picture deleted successfully");
        }
        catch (Exception e)
        {
            _logger.LogError(TAG, e);
            return _BadRequest(e.Message);
        }
    }


    [HttpPost("forgot-password")]
    public async Task<IActionResult> ForgotPassword([FromBody] ForgotPasswordDto dto)
    {
        try
        {
            var user = await userService.GetByEmail(dto.Email);
            if (user == null)
                return _BadRequest("Email not found"); 

            var token = GenerateResetToken(user);
            
            // Construct Link. 
            var link = $"{_appSettings.FrontendUrl}/auth/reset-password?token={token}";
            
            var body = $@"
                <h3>Reset Password Request</h3>
                <p>Hello {user.DisplayName},</p>
                <p>You requested to reset your password. Please click the link below to proceed:</p>
                <p><a href='{link}'>Reset Password</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you did not request this, please ignore this email.</p>
            ";

            await _emailService.SendEmailAsync(user.Email, "Reset Password", body, user.BranchId ?? 1);

            return _Ok(null, "Password reset link sent to your email.");
        }
        catch (Exception e)
        {
            _logger.LogError(TAG, e);
            return _BadRequest(e.Message);
        }
    }

    [HttpPost("reset-password")]
    public async Task<IActionResult> ResetPassword([FromBody] ResetPasswordDto dto)
    {
        try
        {
            if (dto.NewPassword != dto.ConfirmPassword)
                return _BadRequest("Passwords do not match");

            var userId = ValidateResetToken(dto.Token);
            if (userId == null)
                return _BadRequest("Invalid or expired token");

            await userService.ResetPassword(userId.Value, dto.NewPassword);
            return _Ok(null, "Password has been reset successfully.");
        }
        catch (Exception e)
        {
            _logger.LogError(TAG, e);
            return _BadRequest(e.Message);
        }
    }

    private string GenerateResetToken(User user)
    {
        var tokenHandler = new JwtSecurityTokenHandler();
        var key = Encoding.ASCII.GetBytes(_appSettings.Secret);
        var tokenDescriptor = new SecurityTokenDescriptor
        {
            Subject = new ClaimsIdentity(new[] { 
                new Claim("id", user.UserID.ToString()), 
                new Claim("type", "reset") 
            }),
            Expires = DateTime.UtcNow.AddHours(1),
            SigningCredentials = new SigningCredentials(new SymmetricSecurityKey(key), SecurityAlgorithms.HmacSha256Signature)
        };
        var token = tokenHandler.CreateToken(tokenDescriptor);
        return tokenHandler.WriteToken(token);
    }

    private int? ValidateResetToken(string token)
    {
        if (token == null) return null;
        var tokenHandler = new JwtSecurityTokenHandler();
        var key = Encoding.ASCII.GetBytes(_appSettings.Secret);
        try
        {
            tokenHandler.ValidateToken(token, new TokenValidationParameters
            {
                ValidateIssuerSigningKey = true,
                IssuerSigningKey = new SymmetricSecurityKey(key),
                ValidateIssuer = false,
                ValidateAudience = false,
                ClockSkew = TimeSpan.Zero
            }, out SecurityToken validatedToken);

            var jwtToken = (JwtSecurityToken)validatedToken;
            var type = jwtToken.Claims.First(x => x.Type == "type").Value;

            if (type != "reset") return null;

            var userId = int.Parse(jwtToken.Claims.First(x => x.Type == "id").Value);
            return userId;
        }
        catch
        {
            return null;
        }
    }
}
