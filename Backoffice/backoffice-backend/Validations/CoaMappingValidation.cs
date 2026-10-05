namespace RSSMBackend.Validation;

using RSSMBackend.Entities;
using FluentValidation;

public class CoaMappingValidator : AbstractValidator<CoaMapping>
{
    public CoaMappingValidator()
    {
        RuleFor(x => x.CoaSource)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'CoaSource' is required");

        RuleFor(x => x.CoaDest)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'CoaDest' is required");
    }
}
