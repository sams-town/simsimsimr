using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.DTOs.User;

namespace RSSMBackend.Services
{
    public interface IUserService
    {
        Task<PagedResult<User>> GetAll(string? keyword,string? status,int? role,int? branchId,int page, int pageSize,string? orderBy,string? orderType);
        Task<User> GetByID(int id,int? branchId);
        Task<User> GetByEmail(string email);
        Task<int> Add(User item);
        Task<int> Remove(User id);
        Task<int> Update(User item);
        AuthenticateResponse Authenticate(AuthenticateRequest model);
        
        // Profile & Password
        Task<ProfileResponse> GetProfile(int userId);
        Task<int> UpdateProfile(int userId, UpdateProfileDto dto);
        Task<bool> ChangePassword(int userId, ChangePasswordDto dto);
        Task<bool> ResetPassword(int userId, string newPassword);
        Task<bool> UpdateProfilePicture(int userId, string profilePictureUrl);
    }
}