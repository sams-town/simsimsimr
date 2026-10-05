namespace RSSMBackend.Validation;

using System.Globalization;
using FluentValidation;
using RSSMBackend.Models;

public class BillingValidator : AbstractValidator<BillingRequestModel>
{
    public BillingValidator()
    {
      
       RuleFor(x => x.id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'id' is required");

        RuleFor(x => x.institute_branch_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'institute_branch_id' is required");

        RuleFor(x => x.patient_ref_code)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'patient_ref_code' is required");

        RuleFor(x => x.cashier_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'cashier_id' is required");

         RuleFor(x => x.cashier)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'cashier' is required"); 

        RuleFor(x => x.payment_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'payment_id' is required");

        RuleFor(x => x.date_added)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'date_added' is required");

         RuleFor(x => x.date_added)
            .Must(BeValidDateTime)
            .WithMessage("The 'date_added' must be in the format 'yyyy-MM-dd HH:mm:ss'.");

         RuleFor(x => x.var_1)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'var_1' is required");

        RuleFor(x => x.var_2)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'var_2' is required");

         RuleFor(x => x.var_3)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'var_3' is required");
           

        RuleFor(x => x.payment)
            .Must(HaveAtLeastOnePayment)
            .WithMessage("At least one 'payment' must be provided."); 

        RuleFor(x => x.transactions)
            .Must(HaveAtLeastOneTransaction)
            .WithMessage("At least one 'Transaction'  must be provided.");        


         When(x => x.cashier != null, () =>
        {
            RuleFor(x => x.cashier).SetValidator(new CashierValidator());
        });    

        When(x => x.payment != null && x.payment.Any(), () =>
        {
            RuleForEach(x => x.payment).SetValidator(new BillingPaymentValidator());
        });

        When(x => x.transactions != null && x.transactions.Any(), () =>
        {
            RuleForEach(x => x.transactions).SetValidator(new BillingTransactionValidator());
        });             
               
    }

    private bool BeValidDateTime(DateTime? dateTime)
    {
        if (!dateTime.HasValue)
            return true;

        string format = "yyyy-MM-dd HH:mm:ss";
        return DateTime.TryParseExact(dateTime.Value.ToString(format), format, CultureInfo.InvariantCulture, DateTimeStyles.None, out _);
    }

    private bool HaveAtLeastOnePayment(List<BillingRequestModel.PaymentModel> payments)
    {
        return payments != null && payments.Any();
    }

     private bool HaveAtLeastOneTransaction(List<BillingRequestModel.TransactionModel> transactions)
    {
        return transactions != null && transactions.Any();
    }
}