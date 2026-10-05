namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;

public class CurrencyValidator : AbstractValidator<Currency>
{
    public CurrencyValidator()
    {
      
        RuleFor(x => x.CurrencyCode)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'CurrencyCode' is required");

        RuleFor(x => x.CurrencyName)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'CurrencyName' is required");

        RuleFor(x => x.Symbol)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Symbol' is required");

        RuleFor(x => x.Country)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Country' is required");

        RuleFor(x => x.ExchangeRate)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'ExchangeRate' is required");
     
    }
}