using FluentValidation;
using RSSMBackend.Entities;

namespace RSSMBackend.Validation;

public class CostCenterValidation : AbstractValidator<CostCenter>
{
    public CostCenterValidation()
    {
        RuleFor(x => x.CostCenterCode)
           .NotNull()
           .NotEmpty()
           .WithMessage("The 'CostCenterCode' is required");
            
        RuleFor(x => x.DepartmentId)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'DepartmentId' is required");    
    }
}
