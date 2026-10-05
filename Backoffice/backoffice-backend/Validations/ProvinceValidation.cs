namespace RSSMBackend.Validation;

using RSSMBackend.Entities;
using FluentValidation;
using RSSMBackend.Models;

public class ProvinceValidation : AbstractValidator<Province> {
    public ProvinceValidation() {
        RuleFor(x => x.ProvinceName)
           .NotNull()
           .NotEmpty()
           .WithMessage("The 'ProvinceName' is required");
    }
}