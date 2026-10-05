using FluentValidation;
using RSSMBackend.Entities;

namespace RSSMBackend.Validation;

public class BpjsCostValidation : AbstractValidator<BpjsCost>
{
    public BpjsCostValidation()
    {
        RuleFor(x => x.Procedure)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Procedure' is required");

        RuleFor(x => x.Cost)
            .NotNull()
            .NotEmpty()
            .GreaterThan(0)
            .WithMessage("The 'Cost' must be greater than 0");
    }
}
