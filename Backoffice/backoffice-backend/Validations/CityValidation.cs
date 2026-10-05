namespace RSSMBackend.Validation;

using RSSMBackend.Entities;
using FluentValidation;
using RSSMBackend.Models;

public class CityValidation : AbstractValidator<City> {
    public CityValidation() {
        RuleFor(x => x.CityName)
           .NotNull()
           .NotEmpty()
           .WithMessage("The 'CityName' is required");
    }
}