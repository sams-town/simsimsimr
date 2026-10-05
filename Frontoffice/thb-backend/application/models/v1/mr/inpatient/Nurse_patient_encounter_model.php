<?php class Nurse_patient_encounter_model extends MY_Model {
	protected $tableName = 'nurse_patient_encounter';
	protected $resultMode = 'object';	  
	public $primaryKey = 'nurse_id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();

    public function encounter($nurse_id, $norm) {
        $select = 'a.count';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.norm' => $norm,
        );
        $encounter = $this->select(0, $select, $where);
        
        $count = 1;
        if (count($encounter) > 0) {
            $count = $encounter[0]->count;
            $count = $count + 1;
        }

        $dt_encounter = array(
            'nurse_id' => $nurse_id,
            'norm' => $norm,
            'count' => $count,
        );

        $this->replace($dt_encounter);
    }
}?>