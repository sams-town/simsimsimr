namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;
public class TransactionDetailValidator : AbstractValidator<TransactionRequest.TransactionDetail>
{
    public TransactionDetailValidator()
    {
        
        RuleFor(x => x.CoaCode)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Account No' is required");

        RuleFor(x => x.Amount)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Amount' is required");     

    }
}