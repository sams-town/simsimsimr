using Microsoft.EntityFrameworkCore;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Helpers;
using RSSMBackend.DbContext;
using Microsoft.Extensions.Options;
using System.Text.Json;

namespace RSSMBackend.Services
{
    public class CityService : ICityService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        private readonly string _uploadPath = Path.Combine(Directory.GetCurrentDirectory(), "Files\\Json");
        public CityService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
            // if (!Directory.Exists(_uploadPath))
            //     Directory.CreateDirectory(_uploadPath);
        }

        public async Task<PagedResult<City>> GetAll(string? keyword, int? provinceid, int page, int pageSize, string? orderBy, string? orderType)
        {
            IQueryable<City> query = _context.Cities;
            query = query.Include(x => x.Province);

            if (keyword != null)
                query = query.Where(x => x.CityName.Contains(keyword.ToLower()) || x.Province.ProvinceName.Contains(keyword.ToLower()));

            orderBy = string.IsNullOrEmpty(orderBy) ? "CityId" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "cityid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CityId) : query.OrderByDescending(x => x.CityId);
                    break;
                case "cityname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CityName) : query.OrderByDescending(x => x.CityName);
                    break;
                case "provinceid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Province.ProvinceId) : query.OrderByDescending(x => x.Province.ProvinceId);

                    break;
                case "provincename":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Province.ProvinceName) : query.OrderByDescending(x => x.Province.ProvinceName);
                    break;
                default:
                    query = query.OrderBy(x => x.CityId);
                    break;
            }

            var q = query.Select(x => new City
            {
                CityId = x.CityId,
                CityName = x.CityName,
                ProvinceId = x.Province.ProvinceId,
                ProvinceName = x.Province.ProvinceName
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<City>(await q.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetAllOption(string? keyword, int? provinceid, int page, int pageSize, string? orderBy, string? orderType)
        {
            IQueryable<City> queryCity = _context.Cities;

            if (!string.IsNullOrEmpty(keyword))
            {
                queryCity = queryCity.Where(x => x.CityName.Contains(keyword));
            }

            if (provinceid != null)
                queryCity = queryCity.Where(x => x.ProvinceId == provinceid);

            orderBy = string.IsNullOrEmpty(orderBy) ? "CityId" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "cityid":
                    queryCity = orderType.Equals("asc") ? queryCity.OrderBy(x => x.CityId) : queryCity.OrderByDescending(x => x.CityId);
                    break;
                case "cityname":
                    queryCity = orderType.Equals("asc") ? queryCity.OrderBy(x => x.CityName) : queryCity.OrderByDescending(x => x.CityName);
                    break;
                default:
                    queryCity = queryCity.OrderBy(x => x.CityId);
                    break;
            }

            var query = queryCity.Select(x => new OptionResponse
            {
                label = x.CityName,
                value = x.CityId.ToString()
            });

            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            return await query.ToListAsync();
        }

        public async Task<City> GetByID(int id)
        {
            IQueryable<City> query = _context.Cities;
            query = query.Include(x => x.Province);
            
            query = query.Select(x => new City
            {
                CityId = x.CityId,
                CityName = x.CityName,
                ProvinceId = x.Province.ProvinceId,
                ProvinceName = x.Province.ProvinceName
            });
            return await query.SingleOrDefaultAsync(x => x.CityId.Equals(id));
        }

        public async Task<int> Add(City item)
        {
            if (_context.Cities.Any(x => x.CityName.ToLower().Equals(item.CityName.ToLower())))
                throw new ApplicationException("City Name is Already Exists");

            if (!_context.Provinces.Any(x => x.ProvinceId.Equals(item.ProvinceId)))
                throw new ApplicationException("Province Id is Not Exists");

            _context.Cities.Add(item);
            await _context.SaveChangesAsync();
            return item.CityId;
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
            var dataImportCities = await importTo.JSONFile(filePath);
            if (dataImportCities == null)
            {
                System.IO.File.Delete(filePath);
                throw new ApplicationException("File Not Found or FIle is Empty");
            }

            List<City> newCities = new List<City>();
            foreach (var value in dataImportCities.Data)
            {
                var cityId = (value as JsonElement?)?.GetProperty("id").GetString();
                var cityName = (value as JsonElement?)?.GetProperty("name").GetString();
                var provinceId = (value as JsonElement?)?.GetProperty("province_id").GetString();

                if (await _context.Provinces.SingleOrDefaultAsync(x => x.ProvinceId.Equals(int.Parse(provinceId))) == null)
                {
                    System.IO.File.Delete(filePath);
                    throw new ApplicationException($"ProvinceId {int.Parse(provinceId)} is Not Exists");
                }

                if (await GetByID(int.Parse(cityId)) != null)
                {
                    System.IO.File.Delete(filePath);
                    throw new ApplicationException($"Data on CityId {int.Parse(cityId)} Already Exists");
                }

                newCities.Add(new City
                {
                    CityId = int.Parse(cityId),
                    CityName = cityName,
                    ProvinceId = int.Parse(provinceId)
                });
            }

            System.IO.File.Delete(filePath);
            await AddRange(newCities);
            return newCities.Count;
        }

        public async Task<int> AddRange(List<City> data)
        {
            if (data.Count == 0)
                throw new ApplicationException("Data is Empty");

            await _context.Cities.AddRangeAsync(data);
            return await _context.SaveChangesAsync();
        }

        public async Task<int> Update(City item)
        {
            var data = await GetByID(item.CityId);
            if (data == null)
                throw new ApplicationException("City Id is Not Exists");

            if (_context.Cities.Any(x => x.CityName.ToLower().Equals(item.CityName.ToLower()) && !x.CityId.Equals(item.CityId)))
                throw new ApplicationException("City Name is Already Exists");

            if (!_context.Provinces.Any(x => x.ProvinceId.Equals(item.ProvinceId)))
                throw new ApplicationException("Province Id is Not Exists");

            data.CityName = item.CityName;
            data.ProvinceId = item.ProvinceId;

            _context.Cities.Update(data);
            await _context.SaveChangesAsync();
            return item.CityId;
        }

        public async Task<int> Remove(City item)
        {
            _context.Cities.Remove(item);
            await _context.SaveChangesAsync();
            return item.CityId;
        }

        public async Task<bool> CheckIfExistProvinceId(int provinceid)
        {
            IQueryable<City> query = _context.Cities;
            return query.Any(x => x.ProvinceId == provinceid);
        }
    }
}