namespace RSSMBackend.Validation;

using RSSMBackend.Entities;
using FluentValidation;
using RSSMBackend.Models;

public class PaymentValidation : AbstractValidator<PaymentUpdateRequest> {
    public PaymentValidation() {
        RuleFor(x => x.TransactionId)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'TransactionId' is required");

        RuleFor(x => x.PaymentStatus)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'PaymentStatus' is required");

        RuleFor(x => x.PaymentNo)
            .NotEmpty()
            .When(x => x.PaymentStatus == PaymentStatus.Paid.ToString())
            .WithMessage("The 'Payment No' is required");

        RuleFor(x => x.PaymentNo)
            .Null()
            .When(x => x.PaymentStatus != PaymentStatus.Paid.ToString())
            .WithMessage("The 'Payment No' is not required");

        RuleFor(x => x.PaymentDate)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'PaymentDate' is required");
    }
}