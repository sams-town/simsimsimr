namespace RSSMBackend.Validation;

using System.Globalization;
using FluentValidation;
using RSSMBackend.Models;

public class BillingTransactionValidator : AbstractValidator<BillingRequestModel.TransactionModel>
{
    public BillingTransactionValidator()
    {
      
       RuleFor(x => x.transaction_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'transaction_id' is required");

        RuleFor(x => x.flag_status_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'flag_status_id' is required");

        RuleFor(x => x.flag_status_name)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'flag_status_name' is required");

         RuleFor(x => x.type_transaction_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'type_transaction_id' is required"); 

        RuleFor(x => x.type_transaction_name)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'type_transaction_name' is required");

        RuleFor(x => x.costing_id)
            .NotNull()
            .NotEmpty()
            .When(x => !x.type_transaction_id.Equals("11"))
            .WithMessage("The 'costing_id' is required when type_transaction_id NOT '11'"); 

        RuleFor(x => x.costing_name)
            .NotNull()
            .NotEmpty()
             .When(x => !x.type_transaction_id.Equals("11"))
            .WithMessage("The 'costing_name' is required when type_transaction_id NOT '11'");

        RuleFor(x => x.prescription_id)
            .NotNull()
            .NotEmpty()
            .When(x => x.type_transaction_id.Equals("11"))
            .WithMessage("The 'prescription_id' is required when type_transaction_id '11'");

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

          RuleFor(x => x.coa_code)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'coa_code' is required"); 

        RuleFor(x => x.prescription)
            .Must(HaveAtLeastOnePrescription)
            .When(x => x.type_transaction_id.Equals("11"))
            .WithMessage("The 'prescription' is required when type_transaction_id '11' "); 
        
        RuleFor(x => x.code)
            .NotNull()
            .NotEmpty()
            .When(x => x.type_transaction_id.Equals("11"))
            .WithMessage("The 'coa_code' is required when type_transaction_id '11' "); 

         When(x => x.prescription != null && x.prescription.Any(), () =>
        {
            RuleForEach(x => x.prescription).SetValidator(new BillingTransactionPrescriptionValidator());
        });   
    }   

    private bool BeValidDateTime(DateTime? dateTime)
    {
        if (!dateTime.HasValue)
            return true;

        string format = "yyyy-MM-dd HH:mm:ss";
        return DateTime.TryParseExact(dateTime.Value.ToString(format), format, CultureInfo.InvariantCulture, DateTimeStyles.None, out _);
    }

    private bool HaveAtLeastOnePrescription(List<BillingRequestModel.PrescriptionModel> prescriptionModels)
    {
        return prescriptionModels != null && prescriptionModels.Any();
    }
}