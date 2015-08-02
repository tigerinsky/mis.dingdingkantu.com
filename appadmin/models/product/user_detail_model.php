<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 *  用户详情信息model
 */
class User_detail_model extends CI_Model {

    private $table_name = 'ci_user_detail';
	function __construct()
	{
        parent::__construct();
	}

    function add($query) {
        $result = $this->db->insert($this->table_name, $query);
        if (false === $result) {
            return false;
        }
        if (0 < $this->db->affected_rows()) {
            return true;
        }
        return NULL;
    }

    function get_info_by_sname($sname, $fields = '*') {
        $this->db->select($fields);
        $this->db->where('sname', $sname);
        $this->db->limit(1);
        $result = $this->db->get($this->table_name);
        if (false === $result) {
            return false;
        }
        if (0 >= $result->num_rows) {
            return NULL;
        }
        return $result->result_array()[0];
    }

    function get_info_by_uid($uid, $fields = '*') {
        $this->db->select($fields);
        $this->db->where('uid', $uid);
        $result = $this->db->get($this->table_name);
        log_message('error', $this->db->last_query());
        // 获取数据库信息失败
        if (false === $result) {
            return false;
        }
        // 查询无结果
        if (0 === $result->num_rows) {
            return NULL;
        }
        $arr_res = $result->result_array()[0];

        return $arr_res;
    }

    function update_info_by_uid($uid, $fields) {
        $this->db->where('uid', $uid);
        $ret = $this->db->update($this->table_name, $fields);
        if (false === $ret) {
            log_message('error', __METHOD__.':'.__LINE__.' update error, uid='.strval($uid));
            return false;
        }

        return true;
    }

}
