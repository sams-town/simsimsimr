namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;

public class AccountGroupValidator : AbstractValidator<AccountGroup>
{
    public AccountGroupValidator()
    {
      
        RuleFor(x => x.AccountGroupName)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'AccountGroupName' is required");
    }
}