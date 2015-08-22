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

    function get_info_by_uid($uid, $fields = '*', $avater_type = 0) {
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

        if (isset($arr_res['avatar'])) {
            $arr_res['avatar'] = $this->__process_img_by_type($arr_res['avatar'], $avater_type);
        }

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
    
    /**
     * 查询机器人列表
     * @param str $where 查询条件
     * @param str $limit 条数筛选
     * @return int $data 分会符合条件二维数组
     */
    public function get_robot_list($where, $limit){
    	$query_data="SELECT `id`, `login_type` FROM ci_user {$where} ORDER BY create_time DESC {$limit}";
    	$result_data=$this->dbr->query($query_data);
    	$list_data=$result_data->result_array();
    	return $list_data;
    }
    
    /**
     * 计算出筛选条件下的数据的条数
     * @param int $where 查询条件
     * @return int $data 返回数据的条数
     */
    public function get_count_by_parm($where){
    	$query_data="SELECT count(id) as nums FROM ci_user {$where}";
    	$result_data=$this->dbr->query($query_data);
    	$row_data=$result_data->row_array();
    	return $row_data['nums'];
    }

    /* avatar_type  : 0: small; 1: normal */
    public function __process_img_by_type($arr_img, $avatar_type) {
        $str_img = '';
        switch ($avatar_type) {
        case 0:
            $arr_img_info = json_decode($arr_img, true);
            if ($arr_img_info
                && isset($arr_img_info['img'])
                && isset($arr_img_info['img']['s'])
                && isset($arr_img_info['img']['s']['url'])) {
                    $str_img = $arr_img_info['img']['s']['url'];
                }
            break;
        case 1:
            $arr_img_info = json_decode($user_detail_info['avatar'], true);
            if ($arr_img_info
                && isset($arr_img_info['img'])
                && isset($arr_img_info['img']['n'])
                && isset($arr_img_info['img']['n']['url'])) {
                    $str_img = $arr_img_info['img']['n']['url'];
                }
            break;
        }

        return $str_img;
    }
}
