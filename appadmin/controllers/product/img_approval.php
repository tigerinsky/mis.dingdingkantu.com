<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 图片审核管理
 */
class img_approval extends MY_Controller{
        
    function __construct(){
        parent::__construct();
        $this->dbr=$this->load->database('dbr',TRUE);
//         $this->load->config('mis_imgmgr',TRUE);
//         $this->mis_imgmgr = $this->config->item('mis_imgmgr');
//         // $this->mis_imgmgr['imgmgr_level_1']
//         $this->load->library('redis');
//         $this->key_img = 'mis_img_timestamp';
        $this->load->library('offclient');
        $this->load->model('product/img_approval_model', 'img_approval_model');
        $this->load->model('product/resource_model', 'resource_model');
        $this->load->model('product/user_detail_model', 'user_detail_model');
        $this->load->model('product/relation_model', 'relation_model');
        $this->load->model('product/comment_model', 'comment_model');
    }
    
    //默认调用控制器
    function index(){
    	$this->imgmgr_list();
    }
    
    //显示图片列表，同时有检索功能
    private function imgmgr_list(){
        $this->load->library('form');
        $page=$this->input->get('page');
        $page = max(intval($page),1);
        $dosearch=$this->input->get('dosearch');
        
        $search_arr['is_deleted']=1;
        $where_array[]="is_deleted=1";
        
        if($dosearch=='ok'){
            
            $search_filed=array(
                'img_type'=>array(
                    '1'=>'img_type=1',
                    '2'=>'img_type=2',
                    '3'=>'img_type=3',
                    '4'=>'img_type=4',
                    '5'=>'img_type=5',
                    '6'=>'img_type=6',
                ) 
            );
            
            if(intval($this->input->get('img_type_id'))!=''){
                $img_type_id=$this->input->get('img_type_id');
                if($search_filed['img_type'][$img_type_id]!=''){
                    $where_array[]=$search_filed['img_type'][$img_type_id];
                }
            }

            $keywords=trim($this->input->get('keywords'));

            if($keywords!=''){
                $search_arr['keywords']=$keywords;
                $where_array[]="title like '%{$keywords}%'";        
            }

        }

        if(is_array($where_array) and count($where_array)>0){
            $where=' WHERE '.join(' AND ',$where_array);
        }

        $pagesize=10;
        $offset = $pagesize*($page-1);
        $limit="LIMIT $offset,$pagesize";
        
        $user_num=$this->imgmgr_model->get_count_by_parm($where);
        $pages=pages($user_num,$page,$pagesize);
        $list_data=$this->imgmgr_model->get_data_by_parm($where,$limit);

        $this->load->library('form');
        //$img_type_list=array('1'=>'素描','2'=>'色彩','3'=>'速写','4'=>'设计','5'=>'创作','6'=>'照片');
        $img_type_list = $this->mis_imgmgr['imgmgr_level_1'];
        $search_arr['img_type_sel']=$this->form->select($img_type_list,$img_type_id,'name="img_type_id"','选择图片类型');
        $this->smarty->assign('search_arr',$search_arr);
        $this->smarty->assign('img_type_list',$img_type_list);
        $this->smarty->assign('list_data',$list_data);
        $this->smarty->assign('pages',$pages);
        $this->smarty->assign('show_dialog','true');
        $this->smarty->display('imgmgr/imgmgr_list.html');
    }
    
    
    /**
     * 测试接口
     * 
     */
    function test() {
    	$request = $this->request_array;
    	$response = $this->response_array;
    	
    	$args_data = $request['args_data'];
    	
    	log_message('error', 'args_data:'.var_export($args_data, true));
    	
    	
    	$response['data'] = array(
    			'content' => $args_data,
    	);
    	
    	$this->renderJson($response['errno'], $response['data']);
    	
    	
    	$tids = $request['tids'];
    	$tid_array = explode(',', $tids);
    	print_r($tid_array);
    	exit;
    	
    	
    	
    	$tid = 614271491118215168;
    	//$tweet = $this->img_approval_model->get_tweet($tid);
    	$tweet = $this->img_approval_model->get_tweet_info($tid);
    	print_r(json_encode($tweet));
    	exit;
    	$uid = 1;
    	$data = $this->user_detail_model->get_info_by_uid($uid);
    	//print_r(json_encode($data));
    	
    	$user_detail_info = $this->user_detail_model->get_info_by_uid($uid);
    	
    	print_r(json_encode($user_detail_info));
    	
    }
    
	
    /**
     * 对外提供的接口
     * 返回未审核的贴子列表
     * 1.参数：
		a.日期插件，开始时间: start_time, 2015-07-25 16:00:00
		b.日期插件，结束时间: end_time, 2015-07-25 17:00:00
		c.下拉框，来源: source, 1表示nice,2表示weibo,3表示真人
		d.下拉框，城市: city, 北京/上海
		e.当前请求页: current_pagenum
	   2.返回数据：
		a.返回未审核的贴子列表, 返回字段列表：原图、缩略图、文字、账号、来源，tid,发贴时间
     * 
     */ 
    function get_unapproval_tweet_list(){
        $request = $this->request_array;
        $response = $this->response_array;
        
        $page = $request['page'];
        $page = max(intval($page),1);
        $start_time = $request['start_time'];
        $end_time = $request['end_time'];
        $source = $request['source'];
        $approval = $request['approval'];
        $city = $request['city'];
        $current_pagenum = $request['current_pagenum'];
        
        $where_array = array();
        $where_array[] = "is_del = 0";
        if (isset($approval)) {
        	$where_array[] = "approval = {$approval}";
        }
        if (isset($source)) {
        	$where_array[] = "source = {$source}";
        }
        if (isset($start_time)) {
        	$where_array[] = "ctime >= {$start_time}";
        }
        if (isset($end_time)) {
        	$where_array[] = "ctime <= {$end_time}";
        }
        if (isset($city)) {
        	$where_array[] = "city = '{$city}'";
        }
        if(is_array($where_array) and count($where_array)>0){
        	$where=' WHERE '.join(' AND ', $where_array);
        }
        
        //print_r($where);
        
        $pagesize = 10;
        $offset = $pagesize*($page-1);
        $limit = "LIMIT $offset,$pagesize";
        
        $tweet_num = $this->img_approval_model->get_count_by_parm($where);
        //$pages = pages($tweet_num, $page, $pagesize);
        // 总页数
        $pages = intval($tweet_num/$pagesize);
        $mod = $tweet_num%$pagesize;
        if ($mod!=0) {
        	$pages = $pages + 1;
        }
        $list_data = $this->img_approval_model->get_data_by_parm($where, $limit);
        //print_r(json_encode($list_data));
        
        $res_content = array();
        foreach($list_data as $item) {
        	$tid = $item['tid'];
        	$tweet = $this->img_approval_model->get_tweet_info($tid);
        	$img_arr = json_decode($tweet['imgs'], true);
        	$img_url = $img_arr[0]['n']['url'];
        	$img_url_s = $img_arr[0]['s']['url'];
        	
        	# 用户信息
        	$uid = $item['uid'];
        	$user_detail_info = $this->user_detail_model->get_info_by_uid($uid);
        
        	$res_content[] = array(
        			'tid' => $tid,
        			'content' => $item['content'],
        			'ctime' => $item['ctime'],
        			'source' => $item['source'],
        			'city' => $item['city'],
        			'base_value' => $item['base_value'],
        			'img_url' => $img_url,
        			'img_url_s' => $img_url_s,
        			'uid' => $user_detail_info['uid'],
        			'sname' => $user_detail_info['sname'],
        			'avatar' => $user_detail_info['avatar'],
        	);
        }
		
        $response['data'] = array(
        		'content' => $res_content,
        		'pages' => $pages,
        );
        
        $this->renderJson($response['errno'], $response['data']);

    }
    
    
    /**
     * 对外提供的接口
     * 审核贴子的接口
     * 参数：[
			  {
			  "tid":614271483723657200,
			  "uid":7,
			  "base_value":10
			  },
			  {
			  "tid":614271483723657200,
			  "uid":7,
			  "base_value":10
			  },
			  {
			  "tid":614271483723657200,
			  "uid":7,
			  "base_value":10
			  }
			]
     * 返回成功或失败
     */
    function tweet_approval(){
    	$request = $this->request_array;
    	$response = $this->response_array;
    	
    	$args_data = $request['args_data'];
    	$args_data = json_decode($args_data, true);
    	
    	$result = array();
    	foreach($args_data as $item) {
    		$tid = $item['tid'];
    		$uid = $item['uid'];
    		$base_value = $item['base_value'];
    		$result[] = $this->img_approval_model->approval_tweet($tid, $base_value);
    		// 更新用户队列
    		$arr_tweet_info = array(
    				'tid'   => $tid,
    				'uid'   => $uid,
    				'msg_type'  => 0,   // new tweet
    				'timestamp' => time(),
    		);
    		$this->offclient->UpdateFriendQueue($arr_tweet_info);
    	}
    	
    
    	$response['data'] = array(
    			'content' => $result,
    	);
    	$this->renderJson($response['errno'], $response['data']);
    }
    
    
    
//     function tweet_approval(){
//     	$request = $this->request_array;
//     	$response = $this->response_array;
    
//     	$tid = $request['tid'];
//     	$base_value = $request['base_value'];
    
//     	$result = $this->img_approval_model->approval_tweet($tid, $base_value);
    
//     	$response['data'] = array(
//     			'content' => $result,
//     	);
//     	$this->renderJson($response['errno'], $response['data']);
//     }
    
    
    /**
     * 对外提供的接口
     * 删除贴子的接口
     * 参数：tid,tid,tid
     * 返回成功或失败
     */
    function tweet_delete(){
    	$request = $this->request_array;
    	$response = $this->response_array;
    	
    	$tids = $request['tids'];
    	$tid_array = explode(',', $tids);
    	$result = $this->img_approval_model->delete_tweet($tid_array);
    
    	$response['data'] = array(
    			'content' => $result,
    	);
    	$this->renderJson($response['errno'], $response['data']);
    }
    
    
    /**
     * 对外提供的接口
     * 裁剪贴子的接口
     * 参数：
		a.tid
		b.左上角经度lon
		c.左上角纬度lat
		d.图片的宽度width
		e.图片的高度height(由于是正方形，宽高一样)
     * 返回成功或失败
     */
    function tweet_cut(){
    	$request = $this->request_array;
    	$response = $this->response_array;
    	
    	$tid = $request['tid'];
    	$lon = $request['lon'];
    	$lat = $request['lat'];
    	$width = $request['width'];
    	$height = $width;
    	
    	$suffix = '@'.$lon.'-'.$lat.'-'.$width.'-'.$height.'a';
    	
    	$tweet = $this->img_approval_model->get_tweet_info($tid);
    	$img_arr = json_decode($tweet['imgs'], true);
    	log_message('debug', 'img_arr:'.json_encode($img_arr));
    	$img_url_n = $img_arr[0]['n']['url'];
    	$img_url_t = $img_arr[0]['t']['url'];
    	$img_url_s = $img_arr[0]['s']['url'];
    	// 判断是否已经裁剪过,$flag的值为1表示已经裁剪过
    	$pattern = '/(\w+)@(\d+)-(\d+)-(\d+)-(\d+)a/i';
    	$flag = preg_match($pattern, $img_url_n);
    	if ($flag) {
    		$replacement = '${1}'.$suffix;
    		$img_url_n = preg_replace($pattern, $replacement, $img_url_n);
    		$img_url_t = preg_replace($pattern, $replacement, $img_url_t);
    		$img_url_s = preg_replace($pattern, $replacement, $img_url_s);
    	} else {
    		$img_url_n = $img_url_n.'@'.$lon.'-'.$lat.'-'.$width.'-'.$height.'a';
    		$img_url_t = $img_url_t.'@'.$lon.'-'.$lat.'-'.$width.'-'.$height.'a';
    		$img_url_s = $img_url_s.'@'.$lon.'-'.$lat.'-'.$width.'-'.$height.'a';
    	}
    	
    	$img_arr[0]['n']['url'] = $img_url_n;
    	$img_arr[0]['t']['url'] = $img_url_t;
    	$img_arr[0]['s']['url'] = $img_url_s;
    	$resource_id = explode(',', $tweet['resource_id'])[0];
    	$data = array('img' => json_encode($img_arr[0]));
    	log_message('debug', 'data:'.json_encode($data));
    	$result = $this->resource_model->update($resource_id, $data);
    	// 清掉redis中当前tweet的缓存
    	$this->load->library('redis');
    	$tweet_key = 'tweet_'.$tid;
    	$this->redis->delete($tweet_key);
    	
    	$response['data'] = array(
    			'content' => $result,
    	);
    	$this->renderJson($response['errno'], $response['data']);
    }
    
    
    /**
     * 对外提供的接口
     * 机器人互动
     * 真人关注机器人，机器人没有关注真人的列出来
     * 
     */
    function interaction_relation(){
    	$request = $this->request_array;
    	$response = $this->response_array;
    	
    	$pn = $request['pn'];
    	$rn = $request['rn'];
    	
    	// 获取机器人列表
    	$where_array = array();
    	$where_array[] = "login_type = 2";
    	
    	if(is_array($where_array) and count($where_array)>0){
    		$where=' WHERE '.join(' AND ', $where_array);
    	}
    	
    	$robot_num = $this->user_detail_model->get_count_by_parm($where);
    	$limit = "LIMIT 0,$robot_num";
    	$robot_list = $this->user_detail_model->get_robot_list($where, $limit);
    	
    	
    	$robot_uid_list = array();
    	foreach($robot_list as $robot) {
    		$robot_uid = $robot['id'];
    		$robot_uid_list[] = $robot_uid;
    	}
    	
    	$relation_result = $this->relation_model->get_follower_list_by_robot_uid_list($robot_uid_list, $rn, $rn * $pn);
    	
    	$res_content = array();
    	foreach($relation_result as $item) {
    		$real_uid = $item['a_uid'];
    		$robot_uid = $item['b_uid'];
    		// 机器人信息
    		$user_detail_info_robot = $this->user_detail_model->get_info_by_uid($robot_uid);
    		// 真人信息
    		$user_detail_info_real = $this->user_detail_model->get_info_by_uid($real_uid);
    		
    		$res_content[] = array(
    				'robot_uid' => $user_detail_info_robot['uid'],
    				'robot_sname' => $user_detail_info_robot['sname'],
    				'robot_avatar' => $user_detail_info_robot['avatar'],
    				'real_uid' => $user_detail_info_real['uid'],
    				'real_sname' => $user_detail_info_real['sname'],
    				'real_avatar' => $user_detail_info_real['avatar'],
    		);
    		
    	}
    	
     	//log_message('debug', 'data:'.json_encode($data));
    
    	$response['data'] = array(
    			'content' => $res_content,
    	);
    	$this->renderJson($response['errno'], $response['data']);
    }
    
    
    /**
     * 对外提供的接口
     * 机器人互动
     * 真人赞过机器人发的帖子，机器人没有关注真人的列出来
     *
     */
    function interaction_zan(){
    	$request = $this->request_array;
    	$response = $this->response_array;
    	 
    	$page = $request['page'];
        $page = max(intval($page),1);
    	 
    	// 获取机器人列表
    	$where_array = array();
    	$where_array[] = "login_type = 2";
    	 
    	if(is_array($where_array) and count($where_array)>0){
    		$where=' WHERE '.join(' AND ', $where_array);
    	}
    	 
    	$robot_num = $this->user_detail_model->get_count_by_parm($where);
    	$limit = "LIMIT 0,$robot_num";
    	$robot_list = $this->user_detail_model->get_robot_list($where, $limit);
    	
    	$robot_uid_list = array();
    	foreach($robot_list as $robot) {
    		$robot_uid = $robot['id'];
    		$robot_uid_list[] = $robot_uid;
    	}
    	
    	$robot_uid_list_str = implode(',', $robot_uid_list);
    	
    	$pagesize = 10;
    	$offset = $pagesize*($page-1);
    	$limit = "LIMIT $offset,$pagesize";
    	
    	$zan_result = $this->relation_model->get_zan_by_parm($robot_uid_list_str, $limit);
    	
    	$res_content = array();
    	foreach($zan_result as $item) {
    		$real_uid = $item['uid'];
    		$tid = $item['tid'];
    		$robot_uid = $item['owner_id'];
    		// 机器人信息
    		$user_detail_info_robot = $this->user_detail_model->get_info_by_uid($robot_uid);
    		// 真人信息
    		$user_detail_info_real = $this->user_detail_model->get_info_by_uid($real_uid);
    		
    		// 帖子信息
    		$tid = $item['tid'];
    		$tweet = $this->img_approval_model->get_tweet_info($tid);
    		$img_arr = json_decode($tweet['imgs'], true);
    		$img_url = $img_arr[0]['n']['url'];
    		$img_url_s = $img_arr[0]['s']['url'];
    
    		$res_content[] = array(
		    		'robot_uid' => $user_detail_info_robot['uid'],
		    		'robot_sname' => $user_detail_info_robot['sname'],
		    		'robot_avatar' => $user_detail_info_robot['avatar'],
    				'tid' => $tid,
    				'img_url' => $img_url,
    				'img_url_s' => $img_url_s,
		    		'real_uid' => $user_detail_info_real['uid'],
		    		'real_sname' => $user_detail_info_real['sname'],
		    		'real_avatar' => $user_detail_info_real['avatar'],
    		);
    
    	}
    	 
    	//log_message('debug', 'data:'.json_encode($data));
    
    	$response['data'] = array(
    			'content' => $res_content,
    	);
    	$this->renderJson($response['errno'], $response['data']);
    }
    
    
    /**
     * 对外提供的接口
     * 机器人互动
     * 真人评论过机器人发的帖子，机器人没有回复真人，或者机器人回复真人后，真人又回评了机器人
     *
     */
    function interaction_cmt(){
    	$request = $this->request_array;
    	$response = $this->response_array;
    
    	$page = $request['page'];
    	$page = max(intval($page),1);
    
    	// 获取机器人列表
    	$where_array = array();
    	$where_array[] = "login_type = 2";
    
    	if(is_array($where_array) and count($where_array)>0){
    		$where=' WHERE '.join(' AND ', $where_array);
    	}
    
    	$robot_num = $this->user_detail_model->get_count_by_parm($where);
    	$limit = "LIMIT 0,$robot_num";
    	$robot_list = $this->user_detail_model->get_robot_list($where, $limit);
    	 
    	$robot_uid_list = array();
    	foreach($robot_list as $robot) {
    		$robot_uid = $robot['id'];
    		$robot_uid_list[] = $robot_uid;
    	}
    	 
    	$robot_uid_list_str = implode(',', $robot_uid_list);
    	 
    	$pagesize = 10;
    	$offset = $pagesize*($page-1);
    	$limit = "LIMIT $offset,$pagesize";
    	 
    	$cmt_result = $this->relation_model->get_cmt_by_parm($robot_uid_list_str, $limit);
    	 
    	$res_content = array();
    	foreach($cmt_result as $item) {
    		$real_uid = $item['from_uid'];
    		$cid = $item['content_id'];
    		$robot_uid = $item['to_uid'];
    		// 机器人信息
    		$user_detail_info_robot = $this->user_detail_model->get_info_by_uid($robot_uid);
    		// 真人信息
    		$user_detail_info_real = $this->user_detail_model->get_info_by_uid($real_uid);
    		
    		// 评论信息
    		$comment = $this->comment_model->get_detail_by_cid($cid);
    		$cmt_content = $comment['content'];
    		$cmt_ctime = $comment['ctime'];
    		$tid = $comment['tid'];
    
    		// 帖子信息
    		$tweet = $this->img_approval_model->get_tweet_info($tid);
    		$img_arr = json_decode($tweet['imgs'], true);
    		$img_url = $img_arr[0]['n']['url'];
    		$img_url_s = $img_arr[0]['s']['url'];
    
    		$res_content[] = array(
    				'robot_uid' => $user_detail_info_robot['uid'],
    				'robot_sname' => $user_detail_info_robot['sname'],
    				'robot_avatar' => $user_detail_info_robot['avatar'],
    				'tid' => $tid,
    				'img_url' => $img_url,
    				'img_url_s' => $img_url_s,
    				'cid' => $cid,
    				'content' => $cmt_content,
    				'real_uid' => $user_detail_info_real['uid'],
    				'real_sname' => $user_detail_info_real['sname'],
    				'real_avatar' => $user_detail_info_real['avatar'],
    		);
    
    	}
    
    	//log_message('debug', 'data:'.json_encode($data));
    
    	$response['data'] = array(
    			'content' => $res_content,
    	);
    	$this->renderJson($response['errno'], $response['data']);
    }
    
    
    /**
     * 对外提供的接口
     * 批量关注的接口
     * 参数：[
     {
     "a_uid":15,
     "b_uid":60
     },
     {
     "a_uid":15,
     "b_uid":61
     },
     {
     "a_uid":15,
     "b_uid":62
     }
     ]
     * 返回成功或失败
     */
    function relation_batch_follow(){
    	$request = $this->request_array;
    	$response = $this->response_array;
    	 
    	$args_data = $request['args_data'];
    	$args_data = json_decode($args_data, true);
    	 
    	$result = array();
    	foreach($args_data as $item) {
    		$a_uid = $item['a_uid'];
    		$b_uid = $item['b_uid'];
    		$get_url = URL_PREFIX.'/relation/follow?followee_uid='.$b_uid.'&follower_uid='.$a_uid;
    		$follow_data = json_decode(curl_get_contents($get_url), true);
    		$result[] = $follow_data;
    	}
    	
    	$response['data'] = array(
    			'content' => $result,
    	);
    	$this->renderJson($response['errno'], $response['data']);
    }
    
    
    
    /**
     * ajax调用的函数
     *
     */
    function get_img_title_list_ajax(){
    	$request = $this->request_array;
    	$response = $this->response_array;
    
    	$img_type = $request['img_type'];
    
    	$result = array();
    	if (isset($this->mis_imgmgr['imgmgr_level_2'][$img_type])) {
    		$result = $this->mis_imgmgr['imgmgr_level_2'][$img_type];
    	}
    	
    	$response['errno'] = 0;
    	$response['data']['content'] = $result;
    	
    	$this->renderJson($response['errno'], $response['data']);
    
    }


    //对要闻进行单条推荐
    function sug_one_ajax(){
        if(intval($_GET['id'])>0) {
            $id=$this->input->get('id');
            if($this->tweet_model->one_sug($id, 1)){
				echo 1;
            }else{
				echo 0;
            }
        } else {
			echo 0;
        }
    }
    
    //对要闻闻进行批量推荐属性设置
    function tweet_sug(){
        if(intval($_POST['dosubmit'])==1) {
            $ids=$this->input->post('ids');
            if(is_array($ids) and count($ids)>0){
                $ids_str=join("','",$ids);
                if($this->tweet_model->tweet_sug($ids_str)){
                    show_tips('操作成功',HTTP_REFERER);
                }else{
                    show_tips('操作异常');
                }
            }else{
                show_tips('参数有误，请重新提交');
            }
        } else {
            show_tips('操作异常');
        }
    }
    
    //对要闻进行单条取消推荐
    function sug_one_cancel_ajax() {
        if(intval($_GET['id'])>0) {
            $id=$this->input->get('id');
            if($this->tweet_model->one_sug($id, 0)){
				echo 1;
            }else{
				echo 0;
            }
        } else {
			echo 0;
        }
    }
    //对要闻闻进行批量推荐属性取消
    function tweet_clear_sug(){
        if(intval($_POST['dosubmit'])==1) {
            $ids=$this->input->post('ids');
            if(is_array($ids) and count($ids)>0){
                $ids_str=join("','",$ids);
                if($this->tweet_model->tweet_clear_sug($ids_str)){
                    show_tips('操作成功',HTTP_REFERER);
                }else{
                    show_tips('操作异常');
                }
            }else{
                show_tips('参数有误，请重新提交');
            }
        } else {
            show_tips('操作异常');
        }
    }

    //对要闻进行单条删除属性变更
    function del_one_ajax(){
        if(intval($_GET['id'])>0) {
        	// 更新时间戳
        	$this->redis->set($this->key_img, time());
            $id=$this->input->get('id');
            if($this->imgmgr_model->del_info($id)){
				echo 1;
            }else{
				echo 0;
            }
        } else {
			echo 0;
        }
    }
    
	//对要闻进行单条取消删除
    function del_one_cancel_ajax(){
        if(intval($_GET['id'])>0) {
            $id=$this->input->get('id');
            if($this->tweet_model->one_del($id, 0)){
				echo 1;
            }else{
				echo 0;
            }
        } else {
			echo 0;
        }
    }
    //对要闻闻进行批量删除属性设置
    function tweet_del(){
        if(intval($_POST['dosubmit'])==1) {
            $ids=$this->input->post('ids');
            if(is_array($ids) and count($ids)>0){
                $ids_str=join("','",$ids);
                if($this->tweet_model->tweet_del($ids_str)){
                    show_tips('操作成功',HTTP_REFERER);
                }else{
                    show_tips('操作异常');
                }
            }else{
                show_tips('参数有误，请重新提交');
            }
        } else {
            show_tips('操作异常');
        }
    }

    //对要闻闻进行批量删除属性取消
    function tweet_clear_del(){
        if(intval($_POST['dosubmit'])==1) {
            $ids=$this->input->post('ids');
            if(is_array($ids) and count($ids)>0){
                $ids_str=join("','",$ids);
                if($this->tweet_model->tweet_clear_del($ids_str)){
                    show_tips('操作成功',HTTP_REFERER);
                }else{
                    show_tips('操作异常');
                }
            }else{
                show_tips('参数有误，请重新提交');
            }
        } else {
            show_tips('操作异常');
        }
    }
    
    
    //添加图片
    function imgmgr_add(){
        $this->load->library('form');
        //$img_type_list = array('1'=>'素描','2'=>'色彩','3'=>'速写','4'=>'设计','5'=>'创作','6'=>'照片');
        $img_type_list = $this->mis_imgmgr['imgmgr_level_1'];
        $img_type_sel=Form::select($img_type_list,$info['img_type'],'id="img_type" name="info[img_type]"','请选择');

        $this->smarty->assign('img_type_sel',$img_type_sel);
        $this->smarty->assign('img_title_sel',$img_title_sel);
        $this->smarty->assign('random_version', rand(100,999));
        $this->smarty->assign('show_dialog','true');
        $this->smarty->assign('show_validator','true');
        $this->smarty->display('imgmgr/imgmgr_add.html');
    }
    
    //执行添加图片操作
    function imgmgr_add_do(){
    	$this->redis->set($this->key_img, time());
        $info = $this->input->post('info');
        $pic  = $this->input->post('pic');
        log_message('debug', '*****************[test]******************img_add_do');
        log_message('debug', $pic[0]);
        //判断数据有效性
		/*
		$this->load->library('oss');
		if ($_FILES['img']['name'] != "") {
			$pic_ret = $this->oss->upload('img', array('dir'=>'tweet'));
			if (isset($pic_ret['error_code']) && intval($pic_ret['error_code'])) {
				show_tips($pic_ret['error_code']. ":" . $pic_ret['error']);
			}	
			$info['img'] = $pic_ret;
		}
		 */

        //$info['img_url'] = 'http://www.qqw21.com/article/UploadPic/2012-12/2012123185857829.jpg';
        $info['img_url'] = $pic[0];

        if( $info['listorder']!='' && $info['title'] != ''){
			//$info['img'] = !empty($pic) ? json_encode($pic) : '';
            if($this->imgmgr_model->create_info($info)){
                show_tips('操作成功','','','add');
            }else{
                show_tips('操作异常');
            }
        }else{
            show_tips('数据不完整，请检测');
        }
    }
    
    //修改要闻
    function imgmgr_edit(){
        $this->load->library('form');
        $imgmgr_id = $this->input->get('id');
        $info = $this->imgmgr_model->get_info_by_id($imgmgr_id);
		//$info['img'] = !empty($info['img']) ? json_decode($info['img']) : array();

        //$img_type_list = array('1'=>'素描','2'=>'色彩','3'=>'速写','4'=>'设计','5'=>'创作','6'=>'照片');
        $img_type_list = $this->mis_imgmgr['imgmgr_level_1'];

        $img_type_sel=Form::select($img_type_list,$info['img_type'],'id="img_type" name="info[img_type]"','请选择');
        $this->smarty->assign('info',$info);
        $this->smarty->assign('img_type_sel',$img_type_sel);
        $this->smarty->assign('random_version', rand(100,999));
        $this->smarty->assign('show_dialog','true');
        $this->smarty->assign('show_validator','true');
        $this->smarty->display('imgmgr/imgmgr_edit.html');
    }
    
    //执行修改要闻操作
    function imgmgr_edit_do(){
    	$this->redis->set($this->key_img, time());
        $id = $this->input->post('id');
        $info = $this->input->post('info');
		$this->load->library('oss');
		$pic = $this->input->post('pic');
		/*
		if ($_FILES['img']['name'] != "") {
			$pic_ret = $this->oss->upload('img', array('dir'=>'tweet'));
			if (isset($pic_ret['error_code']) && intval($pic_ret['error_code'])) {
				show_tips($pic_ret['error_code']. ":" . $pic_ret['error']);
			}	
			$info['img'] = $pic_ret;
		}
		 */
        $info['img_url'] = $pic[0];
        if($info['listorder'] != '' && $info['title'] != '') {
			//$info['img'] = !empty($pic) ? json_encode($pic) : '';
            if($this->imgmgr_model->update_info($info, $id)){
                show_tips('操作成功','','','edit');
            }else{
                show_tips('操作异常，请检测');
            }
        }else{
            show_tips('数据不完整，请检测');
        }
    }
    
    
    //单条删除要闻
    function tweet_del_one_ajax(){
        $tweet_id=intval($this->input->get('id'));
        $ret=0;
        if($tweet_id>0){
            if($this->tweet_model->del_info($tweet_id)){
                $ret=1;
            }
        }
        echo $ret;
    }

}
