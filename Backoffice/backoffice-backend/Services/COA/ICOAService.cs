using Microsoft.EntityFrameworkCore.Storage;
using RSSMBackend.Entities;
using RSSMBackend.Models;
namespace RSSMBackend.Services
{
    public interface ICOAService
    {
        Task<PagedResult<Coa>> GetAll(string? keyword,string? status,int? level,int? branch,string? NormalPosition,bool? IsCashBank,bool? IsCashFlow,int? parent,int? accountGroup,int? accountType,int? currancy,int page, int pageSize,string? orderBy,string? orderType);
        Task<List<OptionResponse>> GetAllOption(string? keyword,string? status,int? level,int? branch,string? NormalPosition,bool? IsCashBank,bool? IsCashFlow,string? parentCode,string? startswith,int? parent,int? accountGroup,int? accountType,int? currancy,int page, int pageSize,string? orderBy,string? orderType);
        Task<Coa> GetByID(int id,int? branch);
        Task<Coa> GetByCode(string code,int? branchId);
        Task<CoaMapping> GetMappingByCode(string code);
        Task<int> Add(Coa item);
        Task<int> Remove(Coa id);
        Task<int> Update(Coa item,bool fullmode);
        Task<bool> checkIfExistGroup(int groupId,int? branchId);
        Task<bool> checkIfExistType(int type,int? branchId);
        Task<bool> checkIfExistCoa(string coa,int? branchId);
        Task RemoveAllByBranch(int? branchId);
        
    }
}