using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Options;

using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Helpers;
using Microsoft.Extensions.Azure;

namespace RSSMBackend.Services
{
    public class ContactService : IContactService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public ContactService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<PagedResult<Contact>> GetAll(string? keyword, string? email, string? phone, int page, int pageSize, string? orderBy, string? orderType) {
            IQueryable<Contact> query = _context.Contacts;
            IQueryable<ContactAccount> contactAccount = _context.ContactAccounts;
            contactAccount = contactAccount.Include(x => x.Bank);
            query = query.Include(x => x.City)
            .Include(x => x.Province);
        
            if (!string.IsNullOrEmpty(keyword)) {
                query = query.Where(x => x.ContactName.ToLower().Contains(keyword.ToLower()) || x.Email.ToLower().Contains(keyword.ToLower())
                 || (x.RefCode != null && x.RefCode.ToLower().Contains(keyword.ToLower())));
            }

            if  (!string.IsNullOrEmpty(phone))
                query = query.Where(x => x.Phone.Contains(phone));

            if (!string.IsNullOrEmpty(email))
                query = query.Where(x => x.Email.Contains(email.ToLower()));

            orderBy = string.IsNullOrEmpty(orderBy) ? "contactid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType)? "asc" : orderType.ToLower();

            var q = query.Select(x => new Contact {
                ContactId = x.ContactId,
                ContactName = x.ContactName,
                Phone = x.Phone,
                Address = x.Address,
                Email = x.Email,
                ContactCategory = x.ContactCategory,
                ContactCategoryName= x.ContactCategoryName,
                AccountDetail = contactAccount
                .Where(y => y.ContactId.Equals(x.ContactId))
                .Select(x => new ContactAccount
                {
                    AccountId = x.AccountId,
                    AccountName = x.AccountName,
                    BankCode = x.BankCode,
                    BankName = x.BankName,
                    AccountNo = x.AccountNo,
                    ContactId = x.ContactId
                }).ToList(),
                RefCode = x.RefCode,
                DueDate = x.DueDate,
                Status = x.Status,
                ProvinceId = x.ProvinceId,
                ProviceName = x.Province.ProvinceName,
                CityId = x.CityId,
                CityName = x.City.CityName,
                NPWP = x.NPWP,
                PPh21 = x.PPh21,
                DoctorShare = x.DoctorShare,
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Contact>(await q.ToListAsync(), total);
        }

        public async Task<Contact> GetByID(int id) {
            IQueryable<Contact> query = _context.Contacts;
            IQueryable<ContactAccount> contactAccount = _context.ContactAccounts;
            contactAccount = contactAccount.Include(x => x.Bank);
            query = query.Include(x => x.City)
            .Include(x => x.Province);
            
            var q = query.Select(x => new Contact {
                ContactId = x.ContactId,
                ContactName = x.ContactName,
                Phone = x.Phone,
                Address = x.Address,
                Email = x.Email,
                ContactCategoryId = x.ContactCategoryId,
                ContactCategoryName= x.ContactCategoryName,
                AccountDetail = contactAccount
                .Where(y => y.ContactId.Equals(x.ContactId))
                .Select(x => new ContactAccount
                {
                    AccountId = x.AccountId,
                    AccountName = x.AccountName,
                    BankCode = x.BankCode,
                    BankName = x.BankName,
                    AccountNo = x.AccountNo,
                    ContactId = x.ContactId
                }).ToList(),
                RefCode = x.RefCode,
                DueDate = x.DueDate,
                Status = x.Status,
                ProvinceId = x.ProvinceId,
                ProviceName = x.Province.ProvinceName,
                CityId = x.CityId,
                CityName = x.City.CityName,
                NPWP = x.NPWP,
                PPh21 = x.PPh21,
                DoctorShare = x.DoctorShare,
            });
            return await q.SingleOrDefaultAsync(x => x.ContactId.Equals(id));
        }

        public async Task<Contact> GetByRefCode(string refCode) {
            IQueryable<Contact> query = _context.Contacts;
            IQueryable<ContactAccount> contactAccount = _context.ContactAccounts;
            contactAccount = contactAccount.Include(x => x.Bank);
            query = query.Include(x => x.City)
            .Include(x => x.Province);
            
            var q = query.Select(x => new Contact {
                ContactId = x.ContactId,
                ContactName = x.ContactName,
                Phone = x.Phone,
                Address = x.Address,
                Email = x.Email,
                ContactCategoryId = x.ContactCategoryId,
                ContactCategoryName= x.ContactCategoryName,
                AccountDetail = contactAccount
                .Where(y => y.ContactId.Equals(x.ContactId))
                .Select(x => new ContactAccount
                {
                    AccountId = x.AccountId,
                    AccountName = x.AccountName,
                    BankCode = x.BankCode,
                    BankName = x.Bank.BankName,
                    AccountNo = x.AccountNo,
                    ContactId = x.ContactId
                }).ToList(),
                RefCode = x.RefCode,
                DueDate = x.DueDate,
                Status = x.Status,
                ProvinceId = x.ProvinceId,
                ProviceName = x.Province.ProvinceName,
                CityId = x.CityId,
                CityName = x.City.CityName,
                NPWP = x.NPWP,
                PPh21 = x.PPh21,
                DoctorShare = x.DoctorShare,
            });
            return await q.SingleOrDefaultAsync(x => x.RefCode.Equals(refCode));
        }

        public async Task<int> Add(Contact item) {
            //     //if (item.ContactType.ToLower() != TypeContact.Patient.ToString().ToLower() && item.ContactType.ToLower() != TypeContact.Insurance.ToString().ToLower()) {
            //         if (_context.Contacts.Any(x => x.Phone.Equals(item.Phone)))
            //             throw new ApplicationException("Phone number already exists");

            //         if (_context.Contacts.Any(x => x.Email.ToLower().Equals(item.Email.ToLower())))
            //             throw new ApplicationException("Email already exists");

            //         if (!_context.Cities.Any(x => x.CityId.Equals(item.CityId)))
            //             throw new ApplicationException("City not found");

            //         if (!_context.Provinces.Any(x => x.ProvinceId.Equals(item.ProvinceId)))
            //             throw new ApplicationException("Province not found");

            //         if (!_context.Cities.Any(x => x.ProvinceId.Equals(item.ProvinceId)))
            //             throw new ApplicationException("ProvinceId not match");
            //    // }

            if (!string.IsNullOrEmpty(item.RefCode))
            {
                if (_context.Contacts.Any(x => x.RefCode.Equals(item.RefCode)))
                    throw new ApplicationException("RefCode already exists");
            }
            
            
            if (string.IsNullOrEmpty(item.Status))
                item.Status = "Active";

            _context.Contacts.Add(item);
            await _context.SaveChangesAsync();
            return item.ContactId;
        }

        public async Task<int> Update(Contact item) {
            var data = await GetByID(item.ContactId);
            if (data == null)
                throw new ApplicationException("Contact not found");

            if (_context.Contacts.Any(x => x.Phone.Equals(item.Phone) && !x.ContactId.Equals(item.ContactId)))
                throw new ApplicationException("Phone number already exists");
            
            if (_context.Contacts.Any(x => x.Email.ToLower().Equals(item.Email.ToLower()) && !x.ContactId.Equals(item.ContactId)))
                throw new ApplicationException("Email already exists");

            if (!_context.Cities.Any(x => x.CityId.Equals(item.CityId)))
                throw new ApplicationException("City not found");

            if (!_context.Provinces.Any(x => x.ProvinceId.Equals(item.ProvinceId)))
                throw new ApplicationException("Province not found");

            if (!_context.Cities.Any(x => x.ProvinceId.Equals(item.ProvinceId)))
                throw new ApplicationException("ProvinceId not match");

            if (!string.IsNullOrEmpty(item.RefCode))
            {
                if (_context.Contacts.Any(x => x.RefCode.Equals(item.RefCode) && !x.ContactId.Equals(item.ContactId)))
                    throw new ApplicationException("RefCode already exists");
            }
            
                
            var local = _context.Set<Contact>()
                .Local
                .FirstOrDefault(entry => entry.ContactId.Equals(item.ContactId));

            if (local != null)
            {
                _context.Entry(local).State = EntityState.Detached;
            }

            _context.Contacts.Update(item);
            await _context.SaveChangesAsync();
            return item.ContactId;
        }
        
        public async Task<int> Remove(Contact item) {
            
            if (!string.IsNullOrEmpty(item.RefCode)) {
                
               bool isMedicalFeeExist = await _context.MedicalFees.AnyAsync(x => x.DoctorCode == item.RefCode);
               if (isMedicalFeeExist)
                 throw new ApplicationException("Contact is used in Medical Fee transaction");

               bool isInvoiceExchangeExist = await _context.InvoiceExchanges.AnyAsync(x => x.SupplierCode == item.RefCode);
                if (isInvoiceExchangeExist)
                 throw new ApplicationException("Contact is used in Invoice Exchange transaction");
            }

            var local = _context.Set<Contact>()
                .Local
                .FirstOrDefault(entry => entry.ContactId.Equals(item.ContactId));

            if (local != null)
            {
                _context.Contacts.Remove(local);
            }
            else
            {
                _context.Contacts.Remove(item);
            }
            await _context.SaveChangesAsync();
            return item.ContactId;
        }

        public async Task<bool> CheckCityIsExists(int id) {
            return await _context.Cities.AnyAsync(x => x.CityId.Equals(id));
        }
    }
}    
