using RSSMBackend.Entities;

namespace RSSMBackend.Models;
public class COAFile{

    public string CoaCode { get; set; }
    public string CoaName { get; set; }
    public int CoaLevel { get; set; }
    public string AccountType { get; set; }
    public string CoaHeaderCode { get; set; }
    public string NormalPosition { get; set; }
    public string AccountGroup { get; set; }
    public bool IsCashFlow { get; set; }
    public bool IsCashBank { get; set; }
    public bool IsActive { get; set; }
    public double InitialBalance { get; set; }
    public string? Error { get; set; }

    public COAFile(Coa coa){
        this.CoaCode = coa.CoaCode;
        this.CoaName = coa.CoaCode;
        this.CoaLevel = (int)coa.CoaLevel;
        this.AccountType = coa.AccountTypeName;
        this.CoaHeaderCode = coa.ParentCode;
        this.NormalPosition = coa.NormalPosition;
        this.AccountGroup = coa.AccountGroupName;
        this.IsCashFlow = (bool)coa.IsCashFlow;
        this.IsCashBank = (bool)coa.IsCashBank;
        this.IsActive = coa.Status.Equals("Active") ? true : false;
        this.InitialBalance = (double)coa.Balance;
    }
   
}