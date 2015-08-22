<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 机器人私信列表
 */

class Robot_dialog extends MY_Controller {
    private $_max_dialog_num = 10;
    private $_max_talked_user_num = 100;

    public function __construct() {
        parent::__construct();
        $this->load->model('product/dialog_model', 'dialog_model');
        $this->load->model('product/talk_model', 'talk_model');
        $this->load->model('product/user_detail_model', 'user_detail_model');
        $this->load->model('product/user_model', 'user_model');
    }

    public function index() {
        $request = $this->request_array;
        $talking_list = array();

        $is_first = isset($request['is_first']) ? intval($request['is_first']) : 1;
        $page_num = 0;
        $rn = DIALOG_DEFAULT_PAGE_NUM;
        if (0 === $is_first) {
            $page_num = isset($reuqest['pn']) ? intval($request['pn']) : 0;
            $rn = isset($request['rn']) ? intval($request['rn']) : DIALOG_DEFAULT_PAGE_NUM;
        }

        // refresh snapshot
        if (1 === $is_first) {
            $snapshot_ret = $this->dialog_model->snap_robot_dialog($this->_max_talked_user_num);
            if (!$snapshot_ret) {
                goto index_display;
            }
            $max_page = $snapshot_ret;
        }

        // get user list
        $users = $this->dialog_model->get_robot_talked_users($page_num, $rn);
        if (!$users) {
            goto index_display;
        }

        $counter = 0;
        foreach ($users as $uniq_id) {
            $arr_user_talking = array();
            $arr_uniq_id = explode('-', $uniq_id);
            if (2 != count($arr_uniq_id)) {
                continue;
            }
            $a_uid = $arr_uniq_id[0];
            $b_uid = $arr_uniq_id[1];
            // get user dialog info
            $talk_infor_list = $this->dialog_model->get_talking_infor($a_uid,
                                                                        $b_uid,
                                                                        $this->_max_dialog_num);
            if (false === $talk_infor_list) {
                continue;
            }
            $a_user_info = $this->user_detail_model->get_info_by_uid($a_uid, array('uid', 'sname', 'avatar'));
            if (false === $user_info) {
                continue;
            }
            $a_login_type = $this->user_model->get_login_type_by_uid($a_uid);
            if (false === $a_login_type) {

                continue;
            }
            $b_user_info = $this->user_detail_model->get_info_by_uid($b_uid, array('uid', 'sname', 'avatar'));
            if (false === $b_user_info) {
                continue;
            }
            $b_login_type = $this->user_model->get_login_type_by_uid($b_uid);
            if (false === $b_login_type) {
                continue;
            }
            if ('2' == $a_login_type) {
                $arr_user_talking['robot_info'] = $a_user_info;
                $arr_user_talking['user_info'] = $b_user_info;
            } else {
                $arr_user_talking['robot_info'] = $b_user_info;
                $arr_user_talking['user_info'] = $a_user_info;
            }

            $arr_user_talking['talk_list'] = $talk_infor_list;

            $talking_list[] = $arr_user_talking;

            $counter++;
            if ($counter >= $rn) {
                break;
            }
        }

    index_display:
        $arr_info = array(
            'talking_list'  => $talking_list,
        );
        if ($max_page) {
            $arr_info['max_page'] = ceil($max_page / $rn);
        }

        echo json_encode($arr_info);
    }
}
