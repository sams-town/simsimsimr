using RSSMBackend.Entities;
namespace RSSMBackend.Services
{
    public interface IOptionService
    {
        Task<Option> GetByName(string name,int branchId);
        Task<List<Option>> GetByType(string name,int branchId);
        Task<int> Update(Option item,int branchId);

        Task<int> Add(Option item,int branchId);
        Task<bool> Update(List<Option> item,int branchId);
        Task<int> Remove(Option item);
    }
}