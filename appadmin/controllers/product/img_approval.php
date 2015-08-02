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
        $this->load->model('product/img_approval_model', 'img_approval_model');
        $this->load->model('product/user_detail_model', 'user_detail_model');
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
    	$tid = 614271491118215168;
    	//$tweet = $this->img_approval_model->get_tweet($tid);
    	//$tweet = $this->img_approval_model->get_tweet_info($tid);
    	$uid = 1;
    	$data = $this->user_detail_model->get_info_by_uid($uid);
    	//print_r(json_encode($data));
    	
    	$user_detail_info = $this->user_detail_model->get_info_by_uid($uid);
    	if (isset($user_detail_info['avatar'])) {
    		$arr_img_info = json_decode($user_detail_info['avatar'], true);
    		if ($arr_img_info && isset($arr_img_info['img']) && isset($arr_img_info['img']['n']) && isset($arr_img_info['img']['n']['url'])) {
    			$user_detail_info['avatar'] = $arr_img_info['img']['n']['url'];
    		} else {
    			$user_detail_info['avatar'] ="";
    		}
    	}
    	
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
        $pages = $tweet_num/$pagesize;
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
        	$img_arr = json_decode($tweet['img'], true);
        	$img_url = $img_arr[0]['n']['url'];
        	$img_url_s = $img_arr[0]['s']['url'];
        	
        	# 用户信息
        	$uid = $item['uid'];
        	$user_detail_info = $this->user_detail_model->get_info_by_uid($uid);
        	if (isset($user_detail_info['avatar'])) {
        		$arr_img_info = json_decode($user_detail_info['avatar'], true);
        		if ($arr_img_info && isset($arr_img_info['img']) && isset($arr_img_info['img']['n']) && isset($arr_img_info['img']['n']['url'])) {
        			$user_detail_info['avatar'] = $arr_img_info['img']['n']['url'];
        		} else {
        			$user_detail_info['avatar'] ="";
        		}
        	}
        
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
        );
        
        $this->renderJson($response['errno'], $response['data']);

    }
    
    
    private function get_img_list_by_ios(&$response, $timestamp){
    	$img_timestamp = $this->redis->get($this->key_img);
    	
    	$result = array();
    	if (isset($timestamp) && $timestamp > $img_timestamp) {
    		$response['errno'] = 901;
    		$response['data']['content'] = $result;
    	} else {
	    	$where_array[]="is_deleted=1";
	    
	    	if(is_array($where_array) and count($where_array)>0){
	    		$where=' WHERE '.join(' AND ',$where_array);
	    	}
	    
	    	$row_num=$this->imgmgr_model->get_count_by_parm($where);
	    	$limit="LIMIT $row_num";
	    	$list_data=$this->imgmgr_model->get_data_by_parm($where,$limit);
	    
	    	//$img_type_list = array('1'=>'素描','2'=>'色彩','3'=>'速写','4'=>'设计','5'=>'创作','6'=>'照片');
	    	$img_type_list = $this->mis_imgmgr['imgmgr_level_1'];
	    	
    		foreach($list_data as $img_data) {
    			$tmp_array = array('name' => $img_data['title'], 'img' => $img_data['img_url']);
    			if ($img_data['img_type'] == 1) {
    				if (!isset($sketch_array_tmp[$img_data['cell']])) {
    					$sketch_array_tmp[$img_data['cell']][] = $tmp_array;
    				} else {
    					array_push($sketch_array_tmp[$img_data['cell']], $tmp_array);
    				}
    				$sketch_array = array_values($sketch_array_tmp);
    			} elseif ($img_data['img_type'] == 2) {
    				if (!isset($color_painting_array_tmp[$img_data['cell']])) {
    					$color_painting_array_tmp[$img_data['cell']][] = $tmp_array;
    				} else {
    					array_push($color_painting_array_tmp[$img_data['cell']], $tmp_array);
    				}
    				$color_painting_array = array_values($color_painting_array_tmp);
    			} elseif ($img_data['img_type'] == 3) {
    				if (!isset($quick_sketch_array_tmp[$img_data['cell']])) {
    					$quick_sketch_array_tmp[$img_data['cell']][] = $tmp_array;
    				} else {
    					array_push($quick_sketch_array_tmp[$img_data['cell']], $tmp_array);
    				}
    				$quick_sketch_array = array_values($quick_sketch_array_tmp);
    			} elseif ($img_data['img_type'] == 4) {
    				if (!isset($design_array_tmp[$img_data['cell']])) {
    					$design_array_tmp[$img_data['cell']][] = $tmp_array;
    				} else {
    					array_push($design_array_tmp[$img_data['cell']], $tmp_array);
    				}
    				$design_array = array_values($design_array_tmp);
    			} elseif ($img_data['img_type'] == 5) {
    				if (!isset($creation_array_tmp[$img_data['cell']])) {
    					$creation_array_tmp[$img_data['cell']][] = $tmp_array;
    				} else {
    					array_push($creation_array_tmp[$img_data['cell']], $tmp_array);
    				}
    				$creation_array = array_values($creation_array_tmp);
    			} elseif ($img_data['img_type'] == 6) {
    				if (!isset($photo_array_tmp[$img_data['cell']])) {
    					$photo_array_tmp[$img_data['cell']][] = $tmp_array;
    				} else {
    					array_push($photo_array_tmp[$img_data['cell']], $tmp_array);
    				}
    				$photo_array = array_values($photo_array_tmp);
    			}
    		}
	    
	    	if(count($sketch_array) > 0) {
	    		$result['sketch'] = $sketch_array;
	    	}
	    	if(count($color_painting_array) > 0) {
	    		$result['color_painting'] = $color_painting_array;
	    	}
	    	if(count($quick_sketch_array) > 0) {
	    		$result['quick_sketch'] = $quick_sketch_array;
	    	}
	    	if(count($design_array) > 0) {
	    		$result['design'] = $design_array;
	    	}
	    	if(count($creation_array) > 0) {
	    		$result['creation'] = $creation_array;
	    	}
	    	if(count($photo_array) > 0) {
	    		$result['photo'] = $photo_array;
	    	}
	    	
	    	$response['errno'] = 0;
	    	$response['data']['content'] = $result;
    	}
    
    }
    
    
    private function get_img_list_by_android(&$response, $timestamp){
    	$img_timestamp = $this->redis->get($this->key_img);
    	
    	$result = array();
    	
    	if (isset($timestamp) && $timestamp > $img_timestamp) {
    		$response['errno'] = 901;
    		$response['data']['content'] = $result;
    	} else {
	    	$where_array[]="is_deleted=1";
	    
	    	if(is_array($where_array) and count($where_array)>0){
	    		$where=' WHERE '.join(' AND ',$where_array);
	    	}
	    
	    	$row_num=$this->imgmgr_model->get_count_by_parm($where);
	    	$limit="LIMIT $row_num";
	    	$list_data=$this->imgmgr_model->get_data_by_parm($where,$limit);
	    
	    	//$img_type_list = array('1'=>'素描','2'=>'色彩','3'=>'速写','4'=>'设计','5'=>'创作','6'=>'照片');
	    	$img_type_list = $this->mis_imgmgr['imgmgr_level_1'];
	    	
    		foreach($list_data as $img_data) {
	        	$tmp_array = array('name' => $img_data['title'], 'img' => $img_data['img_url']);
	            if ($img_data['img_type'] == 1) {
	                $sketch_array[] = $tmp_array;
	            } elseif ($img_data['img_type'] == 2) {
	                $color_painting_array[] = $tmp_array;
	            } elseif ($img_data['img_type'] == 3) {
	                $quick_sketch_array[] = $tmp_array;
	            } elseif ($img_data['img_type'] == 4) {
	                $design_array[] = $tmp_array;
	            } elseif ($img_data['img_type'] == 5) {
	                $creation_array[] = $tmp_array;
	            } elseif ($img_data['img_type'] == 6) {
	                $photo_array[] = $tmp_array;
	            }
	        }
	    
	    	if(count($sketch_array) > 0) {
	    		$result['sketch'] = $sketch_array;
	    	}
	    	if(count($color_painting_array) > 0) {
	    		$result['color_painting'] = $color_painting_array;
	    	}
	    	if(count($quick_sketch_array) > 0) {
	    		$result['quick_sketch'] = $quick_sketch_array;
	    	}
	    	if(count($design_array) > 0) {
	    		$result['design'] = $design_array;
	    	}
	    	if(count($creation_array) > 0) {
	    		$result['creation'] = $creation_array;
	    	}
	    	if(count($photo_array) > 0) {
	    		$result['photo'] = $photo_array;
	    	}
	    	
	    	$response['errno'] = 0;
	    	$response['data']['content'] = $result;
    	}
    
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
