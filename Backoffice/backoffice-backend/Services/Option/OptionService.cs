using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;

namespace RSSMBackend.Services
{
    public class OptionService : IOptionService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public OptionService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<Option> GetByName(string name, int branchId)
        {
            return _context.Options.SingleOrDefault(x => x.BranchId == branchId && x.OptionName.ToLower().Equals(name.ToLower()));
        }

        public async Task<int> Add(Option item, int branchId)
        {

            if (_context.Options.Any(u => u.OptionName == item.OptionName && u.BranchId == branchId))
                throw new ApplicationException("Option Name is already in use.");

            _context.Options.Add(item);
            _context.SaveChanges();
            return item.OptionId;
        }
        public async Task<int> Remove(Option item)
        {
            _context.Options.Remove(item);
            _context.SaveChanges();
            return item.OptionId;
        }

        public async Task<int> Update(Option item, int branchId)
        {
            var data = await GetByName(item.OptionName, branchId);
            if (data == null)
            {
                // Auto-create if not exists (upsert)
                item.BranchId = branchId;
                _context.Options.Add(item);
                _context.SaveChanges();
                return item.OptionId;
            }

            data.OptionValue = item.OptionValue;
            if (!string.IsNullOrEmpty(item.OptionLabel)) data.OptionLabel = item.OptionLabel;
            if (!string.IsNullOrEmpty(item.OptionType)) data.OptionType = item.OptionType;
            if (!string.IsNullOrEmpty(item.OptionDesc)) data.OptionDesc = item.OptionDesc;

            _context.Options.Update(data);
            _context.SaveChanges();
            return data.OptionId;
        }

        public async Task<bool> Update(List<Option> item, int branchId)
        {
            foreach (Option it in item)
            {
                var data = await GetByName(it.OptionName, branchId);
                if (data == null)
                {
                    // Auto-create if not exists (upsert)
                    it.BranchId = branchId;
                    _context.Options.Add(it);
                    _context.SaveChanges();
                    continue;
                }

                data.OptionValue = it.OptionValue;
                if (!string.IsNullOrEmpty(it.OptionLabel)) data.OptionLabel = it.OptionLabel;
                if (!string.IsNullOrEmpty(it.OptionType)) data.OptionType = it.OptionType;
                if (!string.IsNullOrEmpty(it.OptionDesc)) data.OptionDesc = it.OptionDesc;

                _context.Options.Update(data);
                _context.SaveChanges();
            }
            return true;
        }

        public async Task<List<Option>> GetByType(string type, int branchId)
        {
            return await _context.Options.Where(x => x.BranchId == branchId && x.OptionType.ToLower().Equals(type.ToLower())).ToListAsync();
        }
    }
}