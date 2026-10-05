namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;

public class RoleValidator : AbstractValidator<Role>
{
    public RoleValidator()
    {
        RuleFor(x => x.RoleName)
            .NotEmpty()
            .NotNull()
            .WithMessage("The 'RoleName' is required");
    }
}