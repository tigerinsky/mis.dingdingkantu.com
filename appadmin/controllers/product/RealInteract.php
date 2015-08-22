<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 真人互动
 */
class realinteract extends MY_Controller{
    
    const ROBOT_NUM = 5;

    function __construct(){
        parent::__construct();
        $this->dbr=$this->load->database('dbr',TRUE);
        $this->load->model('product/user_model', 'user_model');
        $this->load->model('product/user_detail_model', 'user_detail_model');
        $this->load->model('product/tweet_model', 'tweet_model');
        $this->load->model('product/resource_model', 'resource_model');
        $this->load->model('product/realinteract_model', 'realinteract_model');
    }
    
    //默认调用控制器
    function index(){
    	$this->product_list();
    }

    public function real_list() {

        $request = $this->request_array;
        $response = $this->response_array;

        //获取真人列表，包括筛选条件

        /* 筛选条件 */
        $login_type = 0;
        $filter_user = array(
            'login_type' => 0,
        );

        /* 获取真人uid */
        $fields_user = 'id';
        $real_user_info = $this->user_model->get_user_info_by_filter($filter_user, $fields_user);

        if(false === $real_user_info) {}
        if(empty($real_user_info)){}

        //获取真人信息和帖子
        foreach($real_user_info as $info) {
            $uid = $info['id'];

            /* 获取真人信息 */
            $fields_user_detail = "avatar, sname";
            $real_user_detail_info = $this->user_detail_model->get_info_by_uid($info['id'], $fields_user_detail);
            if(false === $real_user_detail_info) {
            }
            if(empty($real_user_detail_info)) {
            }
            //echo json_encode($real_user_detail_info);exit;

            /* 获取帖子信息 */
            $fields_tweet = "tid, resource_id";
            $tweet_info = $this->tweet_model->get_tweet_info_by_uid($uid, $fields_tweet);
            if(false === $tweet_info) {
            }
            if(empty($tweet_info)) {
            
                continue;
            }

            $rid = $tweet_info['resource_id'];
            $resource_info = $this->resource_model->get_resource_by_rid($rid);
            $result[] = array(
                'uid' => $uid,
                'avatar' => $real_user_detail_info['avatar'],
                'sname' => $real_user_detail_info['sname'],
                'tid' => $tweet_info['tid'],
                'resource_info' => $resource_info,
            );
        }

        //随机获取10个机器人
        $filter_robot = array(
            'login_type' => 2,
        );

        /* 获取机器人uid */
        $fields_robot = 'id';
        $robot_info = $this->user_model->get_user_info_by_filter($filter_robot, $fields_robot);
        $robot_num = count($robot_info);
        $start = rand(0, $robot_num - $this::ROBOT_NUM);
        $robots = array_slice($robot_info, $start, $this::ROBOT_NUM);
        foreach($robots as $robot) {

            /* 获取机器人信息*/
            $fields_robot_detail = "sname";
            $robot_detail_info = $this->user_detail_model->get_info_by_uid($robot['id'], $fields_robot_detail);
            if(false === $robot_detail_info) {
            }
            if(empty($robot_detail_info)) {
            }
            $result_robot[] = array(
                'robot_id' => $robot['id'],
                'robot_sname' => $robot_detail_info['sname'],
            );
        }
        $response['data'] = array(
            'content' => $result,
            'robot_info' => $result_robot,
        );  
        $this->renderJson($response['errno'], $response['data']);

    }

    function submit() {
    
        $request = $this->request_array;
        $response = $this->response_array;

        if(isset($request['tid'])) {
            $tid = $request['tid'];
        }
    
    }

}
