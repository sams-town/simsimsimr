using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;
using RSSMBackend.DTOs.User;
using Newtonsoft.Json;

namespace RSSMBackend.Services
{
    public class UserService : IUserService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public UserService(RSSMDbContext context,IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<PagedResult<User>> GetAll(string? keyword,string? status,int? role,int? branchId,int page, int pageSize,string? orderBy,string? orderType)
        {
            
            IQueryable<User> query = _context.Users;
            query = query.Include(x => x.Role)
            .Include(x => x.Branch);

            if (!string.IsNullOrEmpty(keyword)) {
                query = query.Where(m => m.Email.ToLower().Contains(keyword.ToLower())
                        || (m.Status != null && m.Status.ToLower().Contains(keyword.ToLower()))
                        || (m.DisplayName != null && m.DisplayName.ToLower().Contains(keyword.ToLower()))
                );
            }

            if (!string.IsNullOrEmpty(status)){
                query = query.Where(m => m.Status.ToLower().Equals(status.ToLower()));
            }

            if (role != null){
                query = query.Where(m => m.RoleID == role);
            }

            if (branchId != null){
                query = query.Where(m => m.BranchId == branchId);
            }

            orderBy = string.IsNullOrEmpty(orderBy) ? "userid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "userid": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.UserID) : query.OrderByDescending(x => x.UserID);
                    break; 
                case "email": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Email) : query.OrderByDescending(x => x.Email);
                    break; 
                case "status": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Status) : query.OrderByDescending(x => x.Status);
                    break; 
                case "displayname": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.DisplayName) : query.OrderByDescending(x => x.DisplayName);
                    break;
                case "createddate": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CreatedDate) : query.OrderByDescending(x => x.CreatedDate);
                    break;
                case "lastlogin": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.LastLogin) : query.OrderByDescending(x => x.LastLogin);
                    break;    
                case "role": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Role.RoleName) : query.OrderByDescending(x => x.Role.RoleName);
                    break;
                case "rolename": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Role.RoleName) : query.OrderByDescending(x => x.Role.RoleName);
                    break; 
                case "roleid": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Role.RoleID) : query.OrderByDescending(x => x.Role.RoleID);
                    break;                                 
                default:
                    query = query.OrderBy(x => x.UserID);
                    break;
            }

            var q = query.Select(x => 
            new User { 
                        UserID = x.UserID,
                        DisplayName = x.DisplayName,
                        Email = x.Email,
                        Status = x.Status,
                        LastLogin = x.LastLogin,
                        CreatedDate = x.CreatedDate,
                        RoleID = x.RoleID,
                        RoleName = x.Role.RoleName,
                        BranchId = x.BranchId,
                        BranchName = x.Branch.BranchName
             });

            int total = await q.CountAsync();
            if(page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<User>(await q.ToListAsync(),total);
        }

        public async Task<User> GetByID(int id,int? branchId){
            IQueryable<User> query = _context.Users;
            query = query.Include(x => x.Role)
            .Include(x => x.Branch);
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);
            query = query.Select(x => 
            new User { 
                UserID = x.UserID,
                DisplayName = x.DisplayName,
                Email = x.Email,
                Status = x.Status,
                LastLogin = x.LastLogin,
                CreatedDate = x.CreatedDate,
                RoleID = x.RoleID,
                Password = x.Password,
                Salt = x.Salt,
                RoleName = x.Role.RoleName,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName
             });
            return query.SingleOrDefault(x => x.UserID.Equals(id));
        }

        public async Task<int> Add(User item){

            IQueryable<User> query = _context.Users;
            if(item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);


            if (query.Any(u => u.Email.ToLower().Equals(item.Email.ToLower())))
                throw new ApplicationException("Email is already in use.");

            
            byte[] salt = Utils.GenerateSalt();
            item.Password =  Utils.HashPassword(item.Password, salt);
            item.Salt = Convert.ToBase64String(salt);
            item.CreatedDate = DateTime.UtcNow;

            _context.Users.Add(item);
            _context.SaveChanges();
            return item.UserID;
        }
        public async Task<int> Remove(User item){
             _context.Users.Remove(item);
            _context.SaveChanges();
            return item.UserID;
        }

        public async Task<int> Update(User item){
            
            var user = await GetByID(item.UserID,item.BranchId);
            if(user == null)
                throw new ApplicationException("User Not Found");

             IQueryable<User> query = _context.Users;
            if(item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);    

            if (query.Any(u =>  u.Email.ToLower().Equals(item.Email.ToLower()) && u.UserID != item.UserID))
               throw new ApplicationException("Email is already in use.");

             Console.WriteLine($"Password: {item.Password}");
            if (!string.IsNullOrEmpty(item.Password))
            {
                 Console.WriteLine($"XXX");
                byte[] salt = Utils.GenerateSalt();
                user.Password = Utils.HashPassword(item.Password, salt);
                user.Salt = Convert.ToBase64String(salt);
            }



            user.Email = item.Email;
            user.DisplayName = item.DisplayName;
            user.Status = item.Status;
            user.RoleID = item.RoleID;
            

             Console.WriteLine($"Password: {JsonConvert.SerializeObject(user)}");

            _context.Users.Update(user);
            _context.SaveChanges();
            return user.UserID;
        }

        public AuthenticateResponse Authenticate(AuthenticateRequest model)
        {
            var user = _context.Users.SingleOrDefault(x => x.Email.ToLower().Equals(model.Email.ToLower()));
            if (user != null && Utils.VerifyPassword(model.Password, user.Password, Convert.FromBase64String(user.Salt)))
            {
                if (!string.Equals(user.Status, "Active", StringComparison.OrdinalIgnoreCase))
                {
                    throw new ApplicationException("User account is inactive/not active."); 
                }

                var token = Utils.generateJwtToken(user,_appSettings.Secret);

                user.LastLogin = DateTime.UtcNow;
                 _context.Users.Update(user);
                 _context.SaveChanges();
                return new AuthenticateResponse(user, token);
            }
            else
            {
                return null;
            }
        }

        public async Task<User> GetByEmail(string email)
        {
            IQueryable<User> query = _context.Users;
            query = query.Include(x => x.Role)
            .Include(x => x.Branch);
            query.Select(x => 
            new User { 
                        UserID = x.UserID,
                        DisplayName = x.DisplayName,
                        Email = x.Email,
                        Status = x.Status,
                        LastLogin = x.LastLogin,
                        CreatedDate = x.CreatedDate,
                        RoleID = x.RoleID,
                        RoleName = x.Role.RoleName,
                        BranchId = x.BranchId,
                        BranchName = x.Branch.BranchName
             });
            return query.SingleOrDefault(x => x.Email.ToLower().Equals(email.ToLower()));
        }

        // Profile Methods
        public async Task<ProfileResponse> GetProfile(int userId)
        {
            var user = await _context.Users
                .Include(x => x.Role)
                .FirstOrDefaultAsync(x => x.UserID == userId);

            if (user == null) return null;

            return new ProfileResponse
            {
                UserId = user.UserID,
                DisplayName = user.DisplayName,
                Email = user.Email,
                Phone = user.Phone,
                ProfilePicture = user.ProfilePicture,
                RoleId = user.RoleID,
                RoleName = user.Role?.RoleName
            };
        }

        public async Task<int> UpdateProfile(int userId, UpdateProfileDto dto)
        {
            var user = await _context.Users.FirstOrDefaultAsync(x => x.UserID == userId);
            if (user == null)
                throw new ApplicationException("User not found");

            // Check email uniqueness
            if (await _context.Users.AnyAsync(u => u.Email.ToLower() == dto.Email.ToLower() && u.UserID != userId))
                throw new ApplicationException("Email is already in use.");

            user.DisplayName = dto.DisplayName;
            user.Email = dto.Email;
            user.Phone = dto.Phone;
            user.ProfilePicture = dto.ProfilePicture;

            _context.Users.Update(user);
            await _context.SaveChangesAsync();
            return user.UserID;
        }

        public async Task<bool> ChangePassword(int userId, ChangePasswordDto dto)
        {
            var user = await _context.Users.FirstOrDefaultAsync(x => x.UserID == userId);
            if (user == null)
                throw new ApplicationException("User not found");

            // Verify current password
            if (!Utils.VerifyPassword(dto.CurrentPassword, user.Password, Convert.FromBase64String(user.Salt)))
                throw new ApplicationException("Current password is incorrect");

            // Validate new password confirmation
            if (dto.NewPassword != dto.ConfirmPassword)
                throw new ApplicationException("New password and confirmation do not match");

            // Hash new password
            byte[] salt = Utils.GenerateSalt();
            user.Password = Utils.HashPassword(dto.NewPassword, salt);
            user.Salt = Convert.ToBase64String(salt);

            _context.Users.Update(user);
            await _context.SaveChangesAsync();

            return true;
        }

        public async Task<bool> ResetPassword(int userId, string newPassword)
        {
            var user = await _context.Users.FirstOrDefaultAsync(x => x.UserID == userId);
            if (user == null)
                throw new ApplicationException("User not found");

            byte[] salt = Utils.GenerateSalt();
            user.Password = Utils.HashPassword(newPassword, salt);
            user.Salt = Convert.ToBase64String(salt);

            _context.Users.Update(user);
            await _context.SaveChangesAsync();
            return true;
        }

        public async Task<bool> UpdateProfilePicture(int userId, string profilePictureUrl)
        {
            var user = await _context.Users.FirstOrDefaultAsync(x => x.UserID == userId);
            if (user == null)
                throw new ApplicationException("User not found");

            user.ProfilePicture = profilePictureUrl;
            _context.Users.Update(user);
            await _context.SaveChangesAsync();
            return true;
        }
    }
}