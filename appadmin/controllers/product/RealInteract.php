<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 真人互动
 */
class RealInteract extends MY_Controller{
    
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
    	$this->realList();
    }

    /**
     * 真人互动列表，支持筛选操作
     */
    public function realList() {

        $request = $this->request_array;
        $response = $this->response_array;

        $filter_tweet = array();
        $fields_tweet = array();
        $filter_user = array();
        $fields_user = array();

        /* 获取帖子信息 */
        /* 按照帖子发布时间筛选 */
        if(isset($request['start_time']) && isset($request['end_time'])) {
            $filter_tweet['start_time'] = $request['start_time'];
            $filter_tweet['end_time'] = $request['end_time'];
        }
        /* 按照帖子所在区域筛选 */
        if(isset($request['city'])) {
            $filter_tweet['city'] = $request['city'];
        }
        /* 翻页 */
        if(isset($request['page'])) {
            $filter_tweet['page'] = $request['page'];
        }

        /* 结果为按照条件筛选出的真人帖子 */
        $tweet_info = $this->tweet_model->get_tweet_info_by_filter($filter_tweet);
        if(false === $tweet_info) {
            $response['errno'] = ERR_RESPONSE_FALSE;
            log_message('error', __METHOD__ .':'.__LINE__.' tweet response error, errno[' . $response['errno'] .']');
            goto end;
        }

        $count_tweet_info = $this->tweet_model->get_count_tweet_info_by_filter($filter_tweet);
        if(false === $count_tweet_info) {
            $response['errno'] = ERR_RESPONSE_FALSE;
            log_message('error', __METHOD__ .':'.__LINE__.' count tweet response error, errno[' . $response['errno'] .']');
            goto end;
        }

        $count = $count_tweet_info['count'];
        $page = ceil($count / TWEET_LIST_SIZE);

        if(!empty($tweet_info)) {
            foreach($tweet_info as $tinfo) {
                $tid = $tinfo['tid'];

                /* 获取帖子资源信息 */
                $rid = $tinfo['resource_id'];
                $resource_info = $this->resource_model->get_resource_by_rid($rid);
                $img = json_decode($resource_info['img'], true);
                $resource_info['img'] = $img['t']['url'];

                $uid = $tinfo['uid'];

                /* 获取真人信息 */
                $fields_user_detail = "avatar, sname";
                $real_user_detail_info = $this->user_detail_model->get_info_by_uid($uid, $fields_user_detail);
                if(false === $real_user_detail_info) {
                    $response['errno'] = ERR_RESPONSE_FALSE;
                    log_message('error', __METHOD__ .':'.__LINE__.' real user detail info response error, errno[' . $response['errno'] .']');
                    goto end;
                }
                if(empty($real_user_detail_info)) {
                    $response['errno'] = ERR_RESPONSE_EMPTY;
                    log_message('error', __METHOD__ .':'.__LINE__.' real user detail info response empty, errno[' . $response['errno'] .']');
                //    goto end;
                }

                $result[] = array(
                    'uid' => $uid,
                    'avatar' => $real_user_detail_info['avatar'],
                    'sname' => $real_user_detail_info['sname'],
                    'tid' => $tinfo['tid'],
                    'resource_info' => $resource_info,
                );
            }
        }

        //随机获取10个机器人
        $filter_robot = array(
            'login_type' => 2,
        );

        /* 获取机器人uid */
        $fields_robot = 'id';
        $robot_info = $this->user_model->get_user_info_by_filter($fields_robot, $filter_robot);
        $robot_num = count($robot_info);
        $start = rand(0, $robot_num - ROBOT_NUM);
        $robots = array_slice($robot_info, $start, ROBOT_NUM);
        foreach($robots as $robot) {

            /* 获取机器人信息*/
            $fields_robot_detail = "sname";
            $robot_detail_info = $this->user_detail_model->get_info_by_uid($robot['id'], $fields_robot_detail);
            if(false === $robot_detail_info) {
                $response['errno'] = ERR_RESPONSE_FALSE;
                log_message('error', __METHOD__ .':'.__LINE__.' robot detail info response error, errno[' . $response['errno'] .']');
                goto end;
            }
            if(empty($robot_detail_info)) {
                $response['errno'] = ERR_RESPONSE_EMPTY;
                log_message('error', __METHOD__ .':'.__LINE__.' robot detail info response empty, errno[' . $response['errno'] .']');
                goto end;
            }
            $result_robot[] = array(
                'robot_id' => $robot['id'],
                'robot_sname' => $robot_detail_info['sname'],
            );
        }
        $response['data'] = array(
            'content' => isset($result) ? $result : array(),
            'page' => $page,
            'robot_info' => $result_robot,
        );  

        end:
        $this->renderJson($response['errno'], $response['data']);
    }


    /**
     * 真人互动提交操作,支持批量提交
     */
    function submit() {
    
        $request = $this->request_array;
        $response = $this->response_array;

        if(!isset($request['tid']) || empty($request['tid'])) {
            $response['errno'] = ERR_REQUEST_EMPTY;
            log_message('error', __METHOD__ .':'.__LINE__.' request error, errno[' . $response['errno'] .']');
            goto end;
        
        }
        if(isset($request['tid'])) {
            $tid = $request['tid'];
            $tids = explode(',', $tid);
            foreach($tids as $id) {
                $data = array(
                    'tweet_id' => $id,
                    'user_id' => isset($request['uid']) ? $request['uid'] : 0,
                    'owner_id' => isset($request['ownerid']) ? $request['ownerid'] : 0,
                    'ctime' => time(),
                );
                $result = $this->realinteract_model->add($data); 
                if(false === $result) {
                    $response['errno'] = ERR_MYSQL_INSERT;
                    log_message('error', __METHOD__ .':'.__LINE__.' submit error, errno[' . $response['errno'] .']');
                    goto end;
                }
            }
        }

        end:
        $this->renderJson($response['errno'], $response['data']);
    }

}


/* End of file RealInteract.php */
/* Location: ./appadmin/controllers/RealInteract.php */
