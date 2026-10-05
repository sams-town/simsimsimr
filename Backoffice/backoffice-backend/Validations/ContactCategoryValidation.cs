using FluentValidation;
using RSSMBackend.Entities;

namespace RSSMBackend.Validation;

public class ContactCategoryValidation : AbstractValidator<ContactCategory>
{
    public ContactCategoryValidation()
    {
         RuleFor(x => x.ContactCategoryName)
            .NotNull()
            .NotEmpty()
            .WithMessage("The 'ContactCategoryName' is required");
    }
}
