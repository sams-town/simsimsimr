namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;
using RSSMBackend.Models;
using RSSMBackend.DbContext;

public class BankValidator : AbstractValidator<Bank> {
    public BankValidator() {
        RuleFor(x => x.BankCode)
           .NotNull()
           .NotEmpty()
           .WithMessage("The 'BankCode' is required");
        
        RuleFor(x => x.BankName)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'BankName' is required");
    }
}