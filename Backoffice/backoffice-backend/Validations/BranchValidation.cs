namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;
using RSSMBackend.Models;

public class BranchValidator : AbstractValidator<Branch>
{
    public BranchValidator()
    {
      
        RuleFor(x => x.BranchName)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'BranchName' is required");

        RuleFor(x => x.Email)
            .EmailAddress()
            .WithMessage("The 'Email' not valid format");

        RuleFor(x => x.Email)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Email' is required");

        RuleFor(x => x.Phone)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Phone' is required");

        RuleFor(x => x.CurrencyId)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Currency' is required");    

        RuleFor(x => x.CompanyId)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Company' is required");        

        RuleFor(x => x.FinancialClosingDate)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'FinancialClosingDate' is required");    

        
        RuleFor(x => x.Status)
            .Must(status => status == Status.Active.ToString() || status.Equals(Status.InActive.ToString()))
            .WithMessage("Status must be 'Active' or 'InActive'");   
       
    }
}