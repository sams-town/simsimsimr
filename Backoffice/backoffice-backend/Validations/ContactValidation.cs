namespace RSSMBackend.Validation;

using RSSMBackend.Entities;
using FluentValidation;
using RSSMBackend.Models;
using System.Data;

public class ContactValidation : AbstractValidator<Contact> {
    public ContactValidation() {
        RuleFor(x => x.ContactName)
        .NotNull()
        .NotEmpty()
        .WithMessage("Contact Name is Required!");

        RuleFor(x => x.Phone)
        .NotNull()
        .NotEmpty()
        .WithMessage("Phone Number is Required!")
        .Length(10, 13)
        .WithMessage("Phone number must be a numeric value with a length between 10 and 13 characters!");

        RuleFor(x => x.Address)
        .NotNull()
        .NotEmpty()
        .WithMessage("Address is Required!");

        RuleFor(x => x.Email)
        .NotNull()
        .NotEmpty()
        .EmailAddress()
        .WithMessage("Emai is Required!");

    RuleFor(x => x.ContactCategoryId)
        .NotNull()
        .NotEmpty()
        .WithMessage("Contact Category is Required!");

     

        RuleFor(x => x.ProvinceId)
        .NotNull()
        .NotEmpty()
        .WithMessage("Field 'ProvinceId' is required!");
        
        RuleFor(x => x.CityId)
        .NotNull()
        .NotEmpty()
        .WithMessage("Field 'CityId' is required!");
    }
    private bool CheckEnum(string item) {
        return Enum.IsDefined(typeof(TypeContact), (TypeContact)Enum.Parse(typeof(TypeContact), item));
    }
}