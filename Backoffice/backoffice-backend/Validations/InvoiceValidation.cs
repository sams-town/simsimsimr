namespace RSSMBackend.Validation;

using FluentValidation;
using RSSMBackend.Models;

public class InvoiceValidator : AbstractValidator<InvoiceRequestModel>
{
    public InvoiceValidator()
    {

        RuleFor(x => x.Number)
             .NotNull()
             .NotEmpty()
             .WithMessage("The 'Number' is required");

        RuleFor(x => x.CustomerNo)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'CustomerNo' is required");

        RuleFor(x => x.TransDate)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'TransDate' is required");


        RuleFor(x => x.DetailItem)
            .Must(HaveAtLeastOneItem)
            .WithMessage("At least one 'DetailItem' must be provided.");
    }

    private bool HaveAtLeastOneItem(List<InvoiceRequestModel.InvoiceDetailModel> detail)
    {
        return detail != null && detail.Any();
    }

}