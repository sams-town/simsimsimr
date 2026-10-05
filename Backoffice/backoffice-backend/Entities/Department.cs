using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace RSSMBackend.Entities
{
    [Table("mst_department", Schema = "master")]
    public class Department
    {
        [Column("department_id")]
        [Key]
        public int DepartmentId { get; set; }

        [Column("department_name")]
        public string DepartmentName { get; set; }

        [Column("branch_id")]
        public int? BranchId { get; set; }
    }
}
