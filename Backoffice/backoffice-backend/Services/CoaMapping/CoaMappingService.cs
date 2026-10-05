using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;

namespace RSSMBackend.Services
{
    public class CoaMappingService : ICoaMappingService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;

        public CoaMappingService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<PagedResult<CoaMapping>> GetAll(string? keyword, int page, int pageSize, string? orderBy, string? orderType)
        {
            IQueryable<CoaMapping> query = _context.CoaMappings;

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m =>
                    m.CoaSource.ToLower().Contains(keyword.ToLower()) ||
                    m.CoaDest.ToLower().Contains(keyword.ToLower()) ||
                    (m.CoaSourceName != null && m.CoaSourceName.ToLower().Contains(keyword.ToLower()))
                );
            }

            orderBy = string.IsNullOrEmpty(orderBy) ? "id" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "id":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Id) : query.OrderByDescending(x => x.Id);
                    break;
                case "coasource":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CoaSource) : query.OrderByDescending(x => x.CoaSource);
                    break;
                case "coasourcename":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CoaSourceName) : query.OrderByDescending(x => x.CoaSourceName);
                    break;
                case "coadest":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CoaDest) : query.OrderByDescending(x => x.CoaDest);
                    break;
                default:
                    query = query.OrderBy(x => x.Id);
                    break;
            }

            var q = query.Select(x => new CoaMapping
            {
                Id = x.Id,
                CoaSource = x.CoaSource,
                CoaSourceName = x.CoaSourceName,
                CoaDest = x.CoaDest,
                CoaDestName = _context.Coa
                    .Where(c => c.CoaCode == x.CoaDest)
                    .Select(c => c.CoaName)
                    .FirstOrDefault()
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<CoaMapping>(await q.ToListAsync(), total);
        }

        public async Task<CoaMapping> GetByID(int id)
        {
            IQueryable<CoaMapping> query = _context.CoaMappings;

            var q = query.Select(x => new CoaMapping
            {
                Id = x.Id,
                CoaSource = x.CoaSource,
                CoaSourceName = x.CoaSourceName,
                CoaDest = x.CoaDest,
                CoaDestName = _context.Coa
                    .Where(c => c.CoaCode == x.CoaDest)
                    .Select(c => c.CoaName)
                    .FirstOrDefault()
            });

            return q.SingleOrDefault(x => x.Id == id);
        }

        public async Task<int> Add(CoaMapping item)
        {
            IQueryable<CoaMapping> query = _context.CoaMappings;

            if (query.Any(u => u.CoaSource.ToLower().Equals(item.CoaSource.ToLower())))
                throw new ApplicationException("CoaSource is already mapped.");

            _context.CoaMappings.Add(item);
            _context.SaveChanges();
            return item.Id;
        }

        public async Task<int> Remove(CoaMapping item)
        {
            _context.CoaMappings.Remove(item);
            _context.SaveChanges();
            return item.Id;
        }

        public async Task<int> Update(CoaMapping item)
        {
            var data = await GetByID(item.Id);
            if (data == null)
                throw new ApplicationException("CoaMapping Not Found");

            IQueryable<CoaMapping> query = _context.CoaMappings;

            if (query.Any(u => u.CoaSource.ToLower().Equals(item.CoaSource.ToLower()) && u.Id != item.Id))
                throw new ApplicationException("CoaSource is already mapped.");

            data.CoaSource = item.CoaSource;
            data.CoaSourceName = item.CoaSourceName;
            data.CoaDest = item.CoaDest;

            _context.CoaMappings.Update(data);
            _context.SaveChanges();
            return data.Id;
        }
    }
}
