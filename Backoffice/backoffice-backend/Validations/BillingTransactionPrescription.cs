namespace RSSMBackend.Validation;

using System.Globalization;
using FluentValidation;
using RSSMBackend.Models;

public class BillingTransactionPrescriptionValidator : AbstractValidator<BillingRequestModel.PrescriptionModel>
{
    public BillingTransactionPrescriptionValidator()
    {
      
       RuleFor(x => x.prescription_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'prescription_id' is required");

        RuleFor(x => x.catalog_name)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'catalog_name' is required");

        RuleFor(x => x.catalog_id)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'catalog_id' is required");


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