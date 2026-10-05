namespace RSSMBackend.Validation;

using FluentValidation;
using RSSMBackend.Models;

public class SalesReceiveValidator : AbstractValidator<SalesReceiveRequestModel>
{
    public SalesReceiveValidator()
    {

        RuleFor(x => x.bankNo)
             .NotNull()
             .NotEmpty()
             .WithMessage("The 'bankNo' is required");

        RuleFor(x => x.bankName)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'bankName' is required");

        RuleFor(x => x.customerNo)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'customerNo' is required");    

        RuleFor(x => x.TransDate)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'TransDate' is required");


        RuleFor(x => x.detailInvoice)
            .Must(HaveAtLeastOneItem)
            .WithMessage("At least one 'detailInvoice' must be provided.");
    }

    private bool HaveAtLeastOneItem(List<SalesReceiveRequestModel.SalesReceiveInvoiceDetailModel> detail)
    {
        return detail != null && detail.Any();
    }

}