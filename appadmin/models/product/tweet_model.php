<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  帖子信息model
 */
class Tweet_model extends CI_Model {

    private $table_name = 'ci_tweet';
	function __construct()
	{
        parent::__construct();
	}

    //需要联合查询，通过tweet查询到uid，再判断uid是否为真人。
    function get_tweet_info_by_filter($filter = array()) {

        $sql = 'SELECT `tid`, `uid`, `resource_id` FROM (`ci_tweet`) JOIN `ci_user` ON `ci_user`.`id` = `ci_tweet`.`uid` and ci_user.login_type = 0 WHERE `ci_tweet`.`tid` not in(SELECT `tweet_id` FROM (`ci_mis_realinteract`))';
        
        $page = 0;
        if(is_array($filter) && !empty($filter)) {
            foreach($filter as $key => $value) {
                if($key == 'city') {
                    $city = $value;
                    $sql .= ' and `ci_tweet`.`city`="' . $city . '"';
                }
                if($key == 'start_time') {
                    $stime = $value;
                    $sql .= ' and `ci_tweet`.`ctime`>' . $stime;
                }
                if($key == 'end_time') {
                    $etime = $value;
                    $sql .= ' and `ci_tweet`.`ctime`<=' . $etime;
                }
                if($key == 'page') {
                    $page = $value;
                }
            }
        }
        $sql .= ' ORDER BY `ci_tweet`.`ctime` desc LIMIT '.$page*10;
        $sql .= ",10";

        /* test sql
            SELECT `tid`, `uid`, `resource_id`,`ci_tweet`.`ctime`
            FROM (`ci_tweet`)
            JOIN `ci_user` ON `ci_user`.`id` = `ci_tweet`.`uid` and ci_user.login_type = 0
            where `ci_tweet`.`tid` not in(SELECT `tweet_id` FROM (`ci_mis_realinteract`))
            and `ci_tweet`.`ctime`> 1438927718 
            ORDER BY `ci_tweet`.`ctime` desc
            LIMIT 10
         */

        log_message('error', 'get_tweet_info_ty_filter:'.$sql);
        $result = $this->db->query($sql);
        log_message('error', 'get_tweet_info_ty_filter_:'.var_export($result, true));
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

    //需要联合查询，通过tweet查询到uid，再判断uid是否为真人。
    function get_count_tweet_info_by_filter($filter = array()) {

        $sql = 'SELECT count(`tid`) as `count` FROM (`ci_tweet`) JOIN `ci_user` ON `ci_user`.`id` = `ci_tweet`.`uid` and ci_user.login_type = 0 WHERE `ci_tweet`.`tid` not in(SELECT `tweet_id` FROM (`ci_mis_realinteract`))';
        
        $page = 0;
        if(is_array($filter) && !empty($filter)) {
            foreach($filter as $key => $value) {
                if($key == 'city') {
                    $city = $value;
                    $sql .= ' and `ci_tweet`.`city`="' . $city . '"';
                }
                if($key == 'start_time') {
                    $stime = $value;
                    $sql .= ' and `ci_tweet`.`ctime`>' . $stime;
                }
                if($key == 'end_time') {
                    $etime = $value;
                    $sql .= ' and `ci_tweet`.`ctime`<=' . $etime;
                }
            }
        }
        $sql .= ' ORDER BY `ci_tweet`.`ctime` desc ';

        /* test sql
            SELECT `tid`, `uid`, `resource_id`,`ci_tweet`.`ctime`
            FROM (`ci_tweet`)
            JOIN `ci_user` ON `ci_user`.`id` = `ci_tweet`.`uid` and ci_user.login_type = 0
            where `ci_tweet`.`tid` not in(SELECT `tweet_id` FROM (`ci_mis_realinteract`))
            and `ci_tweet`.`ctime`> 1438927718 
            ORDER BY `ci_tweet`.`ctime` desc
            LIMIT 10
         */

        log_message('error', 'get_tweet_info_ty_filter:'.$sql);
        $result = $this->db->query($sql);
        log_message('error', 'get_tweet_info_ty_filter_:'.var_export($result, true));
        // 获取数据库信息失败
        if (false === $result) {
            return false;
        }   
        // 查询无结果
        if (0 === $result->num_rows) {
            return NULL;
        }   
        return $result->result_array()[0];
    }  


    function get_tweet_info_by_uid($uid, $fields = '*', $filter = array()) {
        $this->db->select($fields);
        $this->db->where('uid', $uid);
        if(is_array($filter) && !empty($filter)) {
            foreach($filter as $key => $value) {
                if($key == 'start_time') {
                    $stime = $value * 1000;
                    $this->db->where('ctime >', $stime);
                }
                if($key == 'end_time') {
                    $etime = $value * 1000;
                    $this->db->where('ctime <=', $etime);
                }
            }
            $start_time = isset($filter['start_time']);
        }
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
        return $result->result_array()[0];
    }  

}
