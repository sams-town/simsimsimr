using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace RSSMBackend.Entities
{
    [Table("mst_contact_category", Schema = "master")]
    public class ContactCategory
    {
        [Column("contact_category_id")]
        [Key]
        public int ContactCategoryId { get; set; }

        [Column("contact_category_name")]
        public string ContactCategoryName { get; set; }

        [Column("branch_id")]
        public int? BranchId { get; set; }
    }
}
