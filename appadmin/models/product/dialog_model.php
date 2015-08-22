<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__).'/../../libraries/RedisProxy.php';

/**
 * 私信
 */

class Dialog_model extends CI_Model {

    private $_robot_dialog_snapshot = 'robot_dialog_snapshot';
    private $_robot_dialog_users_prefix = 'robot_dialog_';
    private $_talking_queue_prefix = 'talk_msg_queue_';
    private $_talking_content_prefix = 'talk_session_';
    private $_robot_dialog_msg_queue = 'robot_dialog_msg_queue';

    public function __construct() {
        parent::__construct();
        $this->_db_redis = RedisProxy::get_instance('db_redis');
        $this->_cache_redis = RedisProxy::get_instance('cache_redis');
        $this->load->model('product/message_model', 'message_model');
    }

    public function snap_robot_dialog($max_user_num) {
        if (!$this->_db_redis) {
            return false;
        }
        $users = $this->_db_redis->zrevrange($this->_robot_dialog_msg_queue,
            0,
            $max_user_num,
            true);
        if (false === $users) {
            return false;
        }
        if (!$this->_cache_redis) {
            return false;
        }
        $redis_ret = $this->_cache_redis->delete($this->_robot_dialog_snapshot);
        if (false === $redis_ret) {
            return false;
        }
        foreach ($users as $user_id => $user_time) {
            $this->_cache_redis->zadd($this->_robot_dialog_snapshot, $user_time, $user_id);
        }

        return count($users);
    }

    public function get_robot_talked_users($pn, $rn) {
        if (!$this->_cache_redis) {
            return false;
        }
        // return $rn * 2 for error user
        return $this->_cache_redis->zrange($this->_robot_dialog_snapshot,
                                            $pn * $rn,
                                            $pn * $rn + $rn * 2);
    }

    public function get_talking_infor($a_uid, $b_uid, $max_rtn_size) {
        $talking_queue_redis_key = '';
        if ($a_uid > $b_uid) {
            $talking_queue_redis_key = 
                $this->_talking_queue_prefix . $b_uid . '-' . $a_uid;
        } else {
            $talking_queue_redis_key =
                $this->_talking_queue_prefix . $a_uid . '-' . $b_uid;
        }
        if (!$this->_db_redis) {
            return false;
        }
        $talking_list =
            $this->_db_redis->lrange($talking_queue_redis_key, 0, $max_rtn_size - 1);
        if (false === $talking_list) {
            return false;
        }
        $talking_info_list = $this->message_model->get_detail_by_mids($talking_list, 1);

        return $talking_info_list;
    }
}
