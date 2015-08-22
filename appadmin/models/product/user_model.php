<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  用户信息model
 */
class User_model extends CI_Model {

    private $table_name = 'ci_user';
    function __construct()
    {
        parent::__construct();
    }

    function get_login_type_by_uid($uid) {
        $this->db->select('login_type');
        $this->db->where('id', $uid);
        $result = $this->db->get($this->table_name);
        if(false === $result) {
            return false;
        }
        if(0 === $result->num_rows) {
            return null;
        }
        return $result->result_array()[0];
    }

    function get_user_info_by_filter($fields = '*', $filter = array()) {
        $this->db->select($fields);
        if(is_array($filter)) {
            foreach($filter as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        $result = $this->db->get($this->table_name);
        //log_message('error', $this->db->last_query());
        // 获取数据库信息失败
        if (false === $result) {
            return false;
        }   
        // 查询无结果
        if (0 === $result->num_rows) {
            return NULL;
        }   
        return $result->result_array();
    }   

}
