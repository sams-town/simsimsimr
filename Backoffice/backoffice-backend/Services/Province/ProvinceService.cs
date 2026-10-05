using Microsoft.EntityFrameworkCore;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Helpers;
using RSSMBackend.DbContext;
using Microsoft.Extensions.Options;
using System.Text.Json;

namespace RSSMBackend.Services
{
    public class ProvinceService : IProvinceService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        private readonly string _uploadPath = Path.Combine(Directory.GetCurrentDirectory(), "Files\\Json");
        public ProvinceService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
            // if (!Directory.Exists(_uploadPath))
            //     Directory.CreateDirectory(_uploadPath);
        }

        public async Task<PagedResult<Province>> GetAll(string? keyword, int page, int pageSize, string? orderBy, string? orderType)
        {
            IQueryable<Province> query = _context.Provinces;
            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(x => x.ProvinceName.Equals(keyword.ToLower()));
            }

            orderBy = string.IsNullOrEmpty(orderBy) ? "provinceid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "provinceid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.ProvinceId) : query.OrderByDescending(x => x.ProvinceId);
                    break;
                case "provincename":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.ProvinceName) : query.OrderByDescending(x => x.ProvinceName);
                    break;
                default:
                    query = query.OrderBy(x => x.ProvinceId);
                    break;
            }

            var q = query.Select(x => new Province
            {
                ProvinceId = x.ProvinceId,
                ProvinceName = x.ProvinceName
            });

            int total = await q.CountAsync();
            if (page != -1)
            {
                q = q.Skip((page - 1) * pageSize).Take(pageSize);
            }

            return new PagedResult<Province>(await q.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetAllOption(string? keyword, int page, int pageSize, string? orderBy, string? orderType)
        {
            IQueryable<Province> query = _context.Provinces;

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(x => x.ProvinceName.Equals(keyword.ToLower()));
            }

            orderBy = string.IsNullOrEmpty(orderBy) ? "provinceid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "provinceid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.ProvinceId) : query.OrderByDescending(x => x.ProvinceId);
                    break;
                case "provincename":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.ProvinceName) : query.OrderByDescending(x => x.ProvinceName);
                    break;
                default:
                    query = query.OrderBy(x => x.ProvinceId);
                    break;
            }

            var q = query.Select(x => new OptionResponse
            {
                label = x.ProvinceName,
                value = x.ProvinceId.ToString()
            });

            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return await q.ToListAsync();
        }

        public async Task<Province> GetByID(int provinceid)
        {
            return await _context.Provinces.SingleOrDefaultAsync(x => x.ProvinceId.Equals(provinceid));
        }

        public async Task<int> Add(Province item)
        {
            if (_context.Provinces.Any(x => x.ProvinceName.ToLower().Equals(item.ProvinceName.ToLower())))
                throw new ApplicationException("Province Name already exists");

            _context.Provinces.Add(item);
            await _context.SaveChangesAsync();
            return item.ProvinceId;
        }

        public async Task<int> SaveToDatabase(IFormFile fileJson)
        {
            var fileName = $"{DateTime.UtcNow.ToString("yyyyMMddhhmmssfff")}_{Path.GetFileNameWithoutExtension(fileJson.FileName)}.json";
            var filePath = Path.Combine(_uploadPath, fileName);
            using (var stream = new FileStream(filePath, FileMode.Create))
            {
                await fileJson.CopyToAsync(stream);
            }

            var importTo = new ImportTo();
            var dataImportProvinces = await importTo.JSONFile(filePath);
            if (dataImportProvinces == null)
                throw new ApplicationException("File Not Found or FIle is Empty");

            List<Province> newProvinces = new List<Province>();
            foreach (var value in dataImportProvinces.Data)
            {
                var provinceId = (value as JsonElement?)?.GetProperty("id").GetString();
                var cityName = (value as JsonElement?)?.GetProperty("name").GetString();

                if (await GetByID(int.Parse(provinceId)) != null)
                {
                    System.IO.File.Delete(filePath);
                    throw new ApplicationException($"Data on CityId {int.Parse(provinceId)} Already Exists");
                }

                newProvinces.Add(new Province
                {
                    ProvinceId = int.Parse(provinceId),
                    ProvinceName = cityName
                });
            }

            System.IO.File.Delete(filePath);
            return await AddRange(newProvinces);
        }

        public async Task<int> AddRange(List<Province> data)
        {
            if (data.Count == 0)
                throw new ApplicationException("Data Not Found");

            await _context.Provinces.AddRangeAsync(data);
            return await _context.SaveChangesAsync();
        }

        public async Task<int> Update(Province item)
        {
            var data = await GetByID(item.ProvinceId);
            if (data == null)
                throw new ApplicationException("Province Not Found");

            if (_context.Provinces.Any(u => u.ProvinceName.ToLower().Equals(item.ProvinceName.ToLower()) && u.ProvinceId != item.ProvinceId))
                throw new ApplicationException("Province Name already exists");

            data.ProvinceName = item.ProvinceName;

            _context.Provinces.Update(data);
            await _context.SaveChangesAsync();
            return item.ProvinceId;
        }

        public async Task<int> Remove(Province item)
        {
            _context.Provinces.Remove(item);
            await _context.SaveChangesAsync();
            return item.ProvinceId;
        }
    }
}