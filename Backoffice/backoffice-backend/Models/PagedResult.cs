namespace RSSMBackend.Models;
public class PagedResult<T>
{
    public List<T> Data { get; }
    public int TotalData { get; }

    public PagedResult(List<T> data, int totalCount)
    {
        Data = data;
        TotalData = totalCount;
    }
}