namespace RSSMBackend.Validation;
    
using RSSMBackend.Entities;
using FluentValidation;
public class JournalValidator : AbstractValidator<Journal>
{
    public JournalValidator()
    {
        RuleFor(x => x.CoaCode)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'CoaDebit' is required");

       RuleFor(x => x)
            .Must(HaveEitherDebitOrCredit)
            .WithMessage("Either 'DebitAmount' or 'CreditAmount' must be provided");          
    }

     private bool HaveEitherDebitOrCredit(Journal journal)
    {
        return journal.DebitAmount != 0 || journal.CreditAmount != 0;
    }
}