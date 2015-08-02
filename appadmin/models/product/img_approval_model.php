<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 图片审核管理model
 */
class Img_approval_model extends CI_Model {
    
    private $dbr = null;
    private $table_name = 'ci_tweet';
    function __construct() {
        parent::__construct();
        $this->dbr = $this->load->database('dbr',TRUE,FALSE);
        $this->load->library('http2');
    }
    
    /**
     * 查询出筛选条件下的数据
     * @param str $where 查询条件
     * @param str $limit 条数筛选 
     * @return int $data 分会符合条件二维数组
     */
    public function get_data_by_parm($where, $limit){
        $query_data="SELECT `tid`, `uid`, `content`, `ctime`, `is_del`, `dtime`, `resource_id`, `score`, `lon`, `lat`, `achievement_type`, `achievement_name`, `achievement_ctime`, `current_poi_name`, `city`, `approval`, `base_value`, `source`, `interaction` FROM ci_tweet {$where} ORDER BY ctime DESC {$limit}";
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
        $query_data="SELECT count(tid) as nums FROM ci_tweet {$where}";
        $result_data=$this->dbr->query($query_data);
        $row_data=$result_data->row_array();
        return $row_data['nums'];
    }
    
    /**
     * 查询出指定的编号数据
     * @param int $id 数据ID编号
     * @return int $data 返回数据内容
     */
    public function get_info_by_id($id){
        $query_data="SELECT `tid`, `uid`, `content`, `ctime`, `is_del`, `dtime`, `resource_id`, `score`, `lon`, `lat`, `achievement_type`, `achievement_name`, `achievement_ctime`, `current_poi_name`, `city`, `approval`, `base_value`, `source`, `interaction` FROM ci_tweet WHERE tid=?";
        $result_data=$this->dbr->query($query_data,array($id));
        $row_data=$result_data->row_array();
        if($row_data['tid']>0){
            $result=$row_data;
        }else{
            $result='';
        }
        return $result;
    }
    
    
    function get_tweet($tid, $fields = '*') {
    	$this->db->select($fields);
    	$this->db->from($this->table_name);
    	$this->db->where('tid', $tid);
    	$this->db->limit(1);
    	$result = $this->db->get();
    	log_message('error', $this->db->last_query());
    	if (false === $result) {
    		log_message('error', 'get_tweet error: msg['.$this->db->_error_message().']');
    		return false;
    	} else if (0 == $result->num_rows) {
    		return null;
    	} else {
    		return $result->result_array()[0];
    	}
    }
    
    
    function get_tweet_info($tid) {
    	//获取tweet信息
    	$tweet = array();
    	$ret = $this->get_tweet($tid);
    	if (false ===$ret || empty($ret)) {
    		return $ret;
    	}
    
    	$tweet = $ret;
    
    	//get resource
    	$imgs = array();
    	if(!empty($ret['resource_id'])) {
    		$this->load->model('product/Resource_model');
    		$resource_id = explode(',', $ret['resource_id']);
    		log_message('error', 'resource_id:'.var_export($resource_id, true));
    		foreach($resource_id as $rid) {
    			$ret_resource = $this->Resource_model->get_resource_by_rid($rid);
    			log_message('error', 'ret_resource:'.var_export($ret_resource, true));
    			if(false === $ret_resource || empty($ret_resource)) {
    				break;
    			}
    			$img = $ret_resource['img'];
    			$description = $ret_resource['description'];
    			if(!empty($img)) {
    				$img_arr = json_decode($img, true);
    				log_message('error', 'tweet_model_img_arr------------'.var_export($img_arr, true));
    				$img_arr['content'] = $description;
    				log_message('error', 'tweet_model_img_arrrrrr------------'.var_export($img_arr, true));
    				$imgs[] = $img_arr;
    			}
    		}
    	}
    	log_message('error', 'tweet_model_imgs------------'.var_export($imgs, true));
    	$tweet['img'] = json_encode($imgs);
    	
    	return $tweet;
    }
    
    
    function approval_tweet($tid, $base_value) {
    	$this->db->where('tid', $tid);
    	$data = array('base_value' => $base_value);
    	$result = $this->db->update($this->table_name, $data);
    	log_message('error', 'approval_result:'.var_export($result, true));
    	if((false === $result) || (0 == $this->db->affected_rows())) {
    		return 0;
    	}
    	return $this->db->affected_rows();
    }
    
    
    function delete_tweet($tid_array) {
    	$this->db->where_in('tid', $tid_array);
    	$data = array('is_del' => 1, 'dtime' => time());
    	$result = $this->db->update($this->table_name, $data);
    	log_message('error', 'delete_result:'.var_export($result, true));
    	if((false === $result) || (0 == $this->db->affected_rows())) {
    		return 0;
    	}
    	return $this->db->affected_rows();
    }
    
    
    
    /**
     * 对要闻进行单条推荐或取消推荐
     * @param int $id 需要推荐的id
     * @return bool 是否执行成功 
     */
    public function one_sug($id, $op) {
		$info['type'] = 'recommend';
		$info['tid']  = $id;
		$info['op']   = $op;
		$param_str = http_build_query($info);
		$url = $this->mis_tweet['tweet'] . '?' . $param_str;
		$ret = $this->http2->get($url);
        $data = json_decode($ret, true);
        if ($data['errno'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * 批量推荐
     * @param str $ids_str 需要推荐的id集合
     * @return bool 是否执行成功 
     */
    public function tweet_sug($ids_str){
        //is_essence
        return $this->one_sug($ids_str);
    }
    
    /**
     * 批量取消推荐
     * @param str $ids_str 需要变更的id集合
     * @return bool 是否执行成功 
     */
    public function tweet_clear_sug($ids_str){
        $ret = $this->http2->post($this->mis_tweet['tweet']['cancel_sug_url'], array('ds' => $ids_str));
        $data = json_decode($ret, true);
        if ($data['errno'] == 0) {
            return true;
        }

        return false;
    }

    /**
     * 执行单条删除或取消删除操作
     * @param str $ids_str 需要删除的id集合
     * @return bool 是否执行成功 
     */
    public function one_del($id, $op) {
		$info['type'] = 'delete';
		$info['tid']  = $id;
		$info['op']   = $op;	
		$param_str = http_build_query($info);
		$url = $this->mis_tweet['tweet'] . '?' . $param_str;
        $ret = $this->http2->get($url);
        $data = json_decode($ret, true);
        if ($data['errno'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * 对指定编号数据进行删除
     * @param arr $ids 需要删除的数据的id，可以是单个id，也可以是id的数组
     * @return bool 返回执行结果
     */
    public function del_info($ids){
        if(is_array($ids) && count($ids)){
            $id_str=join("','",$ids);
        }else{
            $id_str=$ids;
        }
        $del_rule="UPDATE ci_mis_imgmgr SET is_deleted = 0 WHERE id IN('{$id_str}')";
        if($this->db->query($del_rule)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 批量执行删除
     * @param str $ids_str 需要删除的id集合
     * @return bool 是否执行成功 
     */
    public function tweet_del($ids_str){
        return $this->one_del($ids_str);
    }
    
    /**
     * 批量取消删除
     * @param str $ids_str 需要删除的id集合
     * @return bool 是否执行成功 
     */
    public function tweet_clear_del($ids_str){
        $ret = $this->http2->post($this->mis_tweet['tweet']['cancel_del_url'], array('ds' => $ids_str));
        $data = json_decode($ret, true);
        if ($data['errno'] == 0) {
            return true;
        }

        return false;
    }
    
    
    /**
     * 向数据表中写入一行数据
     * @param arr $info 需要插入的数据
     * @param bool 是否成功执行
     */
    public function create_info($info){
        $insert_query=$this->db->insert_string('ci_mis_imgmgr',$info);
        if($this->db->query($insert_query)){
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 对数据表中的单行数据进行修改
     * @param arr $info 需要修改的键值对
     * @param int $id 被修改的id编号
     * @return bool 是否执行成功 
     */
    public function update_info($info,$id){
        $where="id={$id}";
        $update_rule=$this->db->update_string('ci_mis_imgmgr', $info, $where); 
        if($this->db->query($update_rule)){
            return true;
        }else{
            return false;
        }
    }
}
/* End of file this file */
?>
