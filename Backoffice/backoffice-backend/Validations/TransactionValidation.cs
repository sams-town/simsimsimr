namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;
using RSSMBackend.Models;

public class TransactionValidator : AbstractValidator<TransactionRequest>
{
    public TransactionValidator()
    {
        // Jika transaction type selain biaya coa code wajib, jika transaction type = biaya & payment status paid code code wajib jika unpaid tidak wajib
        RuleFor(x => x.CoaCode)
            .NotEmpty()
            .When(x => x.TransactionType != TransactionType.Payment.ToString())
            .WithMessage("The 'Account No' is required");

        // RuleFor(x => x.CoaCode)
        //     .Null()
        //     .When(x => x.TransactionType == TransactionType.Payment.ToString())
        //     .WithMessage("The 'Account No' is not required");

        // RuleFor(x => x.CoaCode)
        //     .NotEmpty()
        //     .When(x => x.TransactionType == TransactionType.Payment.ToString() && x.PaymentStatus == PaymentStatus.Paid.ToString())
        //     .WithMessage("The 'Account No' is required");

        // RuleFor(x => x.CoaCode)
        //     .Null()
        //     .When(x => x.TransactionType == TransactionType.Payment.ToString() && x.PaymentStatus == PaymentStatus.Unpaid.ToString())
        //     .WithMessage("The 'Account No' is not required");

        // RuleFor(x => x.PaymentStatus)
        //     .NotEmpty()
        //     .When(x => x.TransactionType == TransactionType.Payment.ToString())
        //     .WithMessage("The 'Payment Status' is required");

        RuleFor(x => x.PaymentStatus)
            .Null()
            .When(x => x.TransactionType != TransactionType.Payment.ToString())
            .WithMessage("The 'Payment Status' is not required");

        RuleFor(x => x.PaymentNo)
            .NotEmpty()
            .When(x => x.PaymentStatus == PaymentStatus.Paid.ToString())
            .WithMessage("The 'Payment No' is required");

        RuleFor(x => x.PaymentNo)
            .Null()
            .When(x => x.PaymentStatus != PaymentStatus.Paid.ToString())
            .WithMessage("The 'Payment No' is not required");

        RuleFor(x => x.TransactionDate)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'TransactionDate' is required");

         RuleFor(x => x.Details)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'Details' is required");      

         When(x => x.Details != null && x.Details.Any(), () =>
        {
            RuleForEach(x => x.Details).SetValidator(new TransactionDetailValidator());
        });              

    }
}