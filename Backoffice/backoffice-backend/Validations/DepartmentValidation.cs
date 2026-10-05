using FluentValidation;
using RSSMBackend.Entities;

namespace RSSMBackend.Validation;

public class DepartmentValidation : AbstractValidator<Department>
{
    public DepartmentValidation()
    {
         RuleFor(x => x.DepartmentName)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'DepartmentName' is required");
    }
}
