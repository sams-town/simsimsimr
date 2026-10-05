namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;

public class AccountTypeValidator : AbstractValidator<AccountType>
{
    public AccountTypeValidator()
    {
      
        RuleFor(x => x.AccountTypeName)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'AccountTypeName' is required");

    }
}