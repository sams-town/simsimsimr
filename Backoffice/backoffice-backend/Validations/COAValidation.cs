namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;
using RSSMBackend.Models;

public class COAValidator : AbstractValidator<Coa>
{
    public COAValidator()
    {
      
        RuleFor(x => x.CoaCode)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'CoaCode' is required");


        RuleFor(x => x.CoaName)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Email' is required");

        RuleFor(x => x.AccountGroupId)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'AccountGroup' is required");

        RuleFor(x => x.AccountTypeId)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'AccountType' is required");    


        RuleFor(x => x.NormalPosition)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'NormalPosition' is required");    

        RuleFor(x => x.Status)
            .Must(status => status == Status.Active.ToString() || status.Equals(Status.InActive.ToString()))
            .WithMessage("Status must be 'Active' or 'InActive'");    


        RuleFor(x => x.IsCashBank)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'IsCashBank' is required"); 

      RuleFor(x => x.IsCashFlow)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'IsCashBank' is required"); 
       
    }
}