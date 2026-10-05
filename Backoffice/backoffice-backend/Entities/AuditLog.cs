using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace RSSMBackend.Entities
{
    [Table("audit_logs", Schema = "logs")]
    public class AuditLog
    {
        [Column("id")]
        [Key]
        public long Id { get; set; }

        [Column("module")]
        public string Module { get; set; }

        [Column("ref_id")]
        public string RefId { get; set; }

        [Column("operation")]
        public string Operation { get; set; }

        [Column("before_data", TypeName = "jsonb")]
        public string? BeforeData { get; set; }

        [Column("after_data", TypeName = "jsonb")]
        public string? AfterData { get; set; }

        [Column("user_id")]
        public long? UserId { get; set; }

        [Column("ip_address")]
        public string? IpAddress { get; set; }

        [Column("branch_id")]
        public int? BranchId { get; set; }

        [Column("status")]
        public string? Status { get; set; }

        [Column("created_at")]
        public DateTime? CreatedAt { get; set; }

        // Resolved from master.mst_users for display; the log itself only stores UserId.
        [NotMapped]
        public string? UserName { get; set; }

        [NotMapped]
        public string? RoleName { get; set; }

        [NotMapped]
        public string? BranchName { get; set; }
    }
}
