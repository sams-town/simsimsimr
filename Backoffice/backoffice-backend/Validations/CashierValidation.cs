namespace RSSMBackend.Validation;

using System.Globalization;
using FluentValidation;
using RSSMBackend.Models;

public class CashierValidator : AbstractValidator<BillingRequestModel.CashierModel>
{
    public CashierValidator()
    {
      
       RuleFor(x => x.cashier_name)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'cashier_name' is required");

        RuleFor(x => x.shift)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'shift' is required");
    }
}