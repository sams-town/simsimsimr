using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace RSSMBackend.Entities
{
    [Table("mst_bpjs_cost", Schema = "master")]
    public class BpjsCost
    {
        [Column("cost_id")]
        [Key]
        public int CostId { get; set; }

        [Column("procedure")]
        public string Procedure { get; set; }

        [Column("cost", TypeName = "decimal(18, 2)")]
        public decimal Cost { get; set; }

        [Column("branch_id")]
        public int? BranchId { get; set; }
    }
}
