<?php class User_group_model extends MY_Model {
	protected $tableName = 'user_group';
	protected $resultMode = 'object';	  
	public $primaryKey = 'user_id';	
	public $whereFields;
	protected $orderFields = array('a.user_id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();


    function read_auth () {
        $this->selectFields = "a.group_id, m.*,";
        $this->orderFields = array('m.index' => 'ASC');
        $this->joinFields = array(
            array('group g', "a.group_id = g.id", "LEFT"),
            array('group_module gm', "g.id = gm.group_id", "LEFT"),
            array('module m', "gm.module_id = m.id", "LEFT"),
        );
    }
}?>