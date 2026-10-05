namespace RSSMBackend.Helpers;

public static class Constants
{
    public const string STATUS_PENDING = "Pending";
    public const string STATUS_ON_PROGRESS = "On Progress";
    public const string STATUS_COMPLETED = "Completed";
    public const string STATUS_SENT = "Sent";
    public const string STATUS_DUE_DATE = "Due Date";

    public const string BAD_SERVER = "Internal Server Error.";

    // Contact Category IDs
    public const int CONTACT_CATEGORY_SUPPLIER = 1;
    public const int CONTACT_CATEGORY_PATIENT = 2;
    public const int CONTACT_CATEGORY_DOCTOR = 3;
    public const int CONTACT_CATEGORY_PAYER = 5;
}