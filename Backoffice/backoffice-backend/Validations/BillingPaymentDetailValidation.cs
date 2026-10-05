namespace RSSMBackend.Validation;

using System.Globalization;
using FluentValidation;
using RSSMBackend.Models;

public class BillingPaymentDetailValidationValidator : AbstractValidator<BillingRequestModel.PaymentDetailModel>
{
    public BillingPaymentDetailValidationValidator()
    {
      
       RuleFor(x => x.id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment_method_detail 'id' is required");

        RuleFor(x => x.payment_method_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment_method_detail_ 'payment_method_id' is required");

        RuleFor(x => x.payment_method_name)
            .NotNull()
            .NotEmpty()
            .WithMessage("The payment_method_detail_ 'payment_method_name' is required");

        RuleFor(x => x.name)
            .NotNull()
            .When(x => x.payment_method_name.ToLower().Equals("cc") ||  x.payment_method_name.ToLower().Equals("debit")) 
            .WithMessage("payment_method_detail_name is required when the payment_method is 'CC or DEBIT'.");

         RuleFor(x => x.name)
            .NotNull()
            .When(x => x.payment_method_name.ToLower().Equals("cc") ||  x.payment_method_name.ToLower().Equals("debit")) 
            .WithMessage("payment_method_detail_name is required when the payment_method is 'CC or DEBIT'.");

         RuleFor(x => x.CARD_NUMBER)
            .NotNull()
            .When(x => x.payment_method_name.ToLower().Equals("cc") ||  x.payment_method_name.ToLower().Equals("debit")) 
            .WithMessage("payment_method_detail_CARD_NUMBER is required when the payment_method is 'CC or DEBIT'.");

         RuleFor(x => x.AUTHORIZATION_NUMBER)
            .NotNull()
            .When(x => x.payment_method_name.ToLower().Equals("cc") ||  x.payment_method_name.ToLower().Equals("debit")) 
            .WithMessage("payment_method_detail_AUTHORIZATION_NUMBER is required when the payment_method is 'CC or DEBIT'.");

        RuleFor(x => x.BATCH_NUMBER)
            .NotNull()
            .When(x => x.payment_method_name.ToLower().Equals("cc") ||  x.payment_method_name.ToLower().Equals("debit")) 
            .WithMessage("payment_method_detail_BATCH_NUMBER is required when the payment_method is 'CC or DEBIT'.");

        RuleFor(x => x.FEE)
            .NotNull()
            .When(x => x.payment_method_name.ToLower().Equals("cc") ||  x.payment_method_name.ToLower().Equals("debit")) 
            .WithMessage("payment_method_detail_FEE is required when the payment_method is 'CC or DEBIT'."); 

        RuleFor(x => x.SEP)
            .NotNull()
            .When(x => x.payment_method_name.ToLower().Equals("bpjs")) 
            .WithMessage("payment_method_detail_SEP is required when the payment_method is 'CC or DEBIT'.");

        RuleFor(x => x.INA_CBG)
            .NotNull()
            .When(x => x.payment_method_name.ToLower().Equals("bpjs")) 
            .WithMessage("payment_method_detail_INA_CBG is required when the payment_method is 'BPJS'.");

       RuleFor(x => x.INA_CBG_NAME)
            .NotNull()
            .When(x => x.payment_method_name.ToLower().Equals("bpjs")) 
            .WithMessage("payment_method_detail_INA_CBG_NAME is required when the payment_method is 'BPJS'.");                                  
    }
}