namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;
using RSSMBackend.Models;

public class BankReconcileValidator : AbstractValidator<BankReconcileRequestModel>
{
    public BankReconcileValidator()
    {
      
        RuleFor(x => x.CoaCode)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'CoaCode' is required");

        RuleFor(x => x.NewStatementBalance)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'NewStatementBalance' is required");

        RuleFor(x => x.TotalClear)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'TotalClear' is required");

        RuleFor(x => x.TotalOutStanding)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'TotalOutStanding' is required");    


        RuleFor(x => x.ReconciledDate)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'ReconciledDate' is required");    

    }
}