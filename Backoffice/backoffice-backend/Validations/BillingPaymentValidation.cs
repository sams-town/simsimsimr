namespace RSSMBackend.Validation;

using System.Globalization;
using FluentValidation;
using RSSMBackend.Models;

public class BillingPaymentValidator : AbstractValidator<BillingRequestModel.PaymentModel>
{
    public BillingPaymentValidator()
    {
      
       RuleFor(x => x.payment_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment 'payment_id' is required");

        RuleFor(x => x.payment_method_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment 'payment_method_id' is required");

        RuleFor(x => x.payment_method_name)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment 'payment_method_name' is required");

         RuleFor(x => x.insurance_ref_code)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment 'insurance_id' is required"); 

        RuleFor(x => x.insurance_name)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment 'insurance_name' is required");
   
         RuleFor(x => x.var_1)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment 'var_1' is required");

        RuleFor(x => x.var_2)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment 'var_2' is required");

         RuleFor(x => x.var_3)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment 'var_3' is required");

        RuleFor(x => x.coa_code)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment 'coa_code' is required"); 

        RuleFor(x => x.payment_method_detail)
            .NotNull()
            .When(x => x.payment_method_name.ToLower().Equals("cash/tunai")) 
            .WithMessage("The payment payment_method_detail is required when the payment_method is 'Cash/Tunai'.");

       When(x => x.payment_method_detail != null, () =>
        {
            RuleFor(x => x.payment_method_detail).SetValidator(new BillingPaymentDetailValidationValidator());
        });  

    }
}