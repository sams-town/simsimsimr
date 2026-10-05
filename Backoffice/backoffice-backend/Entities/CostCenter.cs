using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace RSSMBackend.Entities
{
    [Table("mst_cost_center", Schema = "master")]
    public class CostCenter
    {
        [Column("costcenter_id")]
        [Key]
        public int CostcenterId { get; set; }

        [Column("cost_center_code")]
        public string CostCenterCode { get; set; }

        [Column("cost_center_name")]
        public string? CostCenterName { get; set; }

        [Column("department_id")]
        public int DepartmentId { get; set; }

        [Column("branch_id")]
        public int BranchId { get; set; }

        [NotMapped]
        public string? DepartmentName { get; set; }

        [ForeignKey(nameof(BranchId))]
        public Branch? Branch { get; set; }


        [ForeignKey(nameof(DepartmentId))]
        public Department? Department { get; set; }
    }
}
