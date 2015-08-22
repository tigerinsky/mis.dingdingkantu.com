<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Realinteract_model extends CI_Model {
    private $table_name = 'ci_mis_realinteract';

	function __construct() {
		parent::__construct();
	}

    /**
     * add 
     *
     * @return true/false
     */
    function add($data) {
        $result = $this->db->insert($this->table_name, $data);
        if((false === $result) 
            || $this->db->affected_rows() == 0) {
            return false;
        }else if($this->db->affected_rows() > 0) {
            $id = $this->db->insert_id();
            return $id;
        }
        return false;
    }
    
}



/* End of file resource_model.php */
/* Location: ./application/models/resource_model.php*/
