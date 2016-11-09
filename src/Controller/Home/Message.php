<?php
class Message extends LoggedController {

	public function Messages($request, $response, $args) {
		//获取Admins列表
		//get
		if($this->data['status']!='error') {
			$id=$this->loguser->id;
			$count= $this->ci->db->table('message')
							->where('topic_id',0)
							->where(function($query) use ($id){
								$query->where(function($query) use ($id){
										$query->where('type', '=', 2)
											  ->where('send_id', '=', $id);
									})
								->orWhere(function($query) use ($id){
										$query->where('type', '=', 1)
											  ->where('accept_id', '=', $id);
									});
							})
							->orderBy('send_time','desc')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('message')
							->where('topic_id',0)
							->where(function($query) use ($id){
								$query->where(function($query) use ($id){
										$query->where('type', '=', 2)
											  ->where('send_id', '=', $id);
									})
								->orWhere(function($query) use ($id){
										$query->where('type', '=', 1)
											  ->where('accept_id', '=', $id);
									});
							})
							->orderBy('send_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
		}
		// $response->withJson($this->data);
		$this->display('Message');
	}
	public function MessageInfo($request, $response, $args) {
		//获取Admins列表
		//get
		if($this->data['status']!='error') {
			$id=$this->loguser->id;
			if(!$args['topic_id']) {
				$this->data['status']='error';
				$this->data['data']='暂时没有此消息，请核实您的操作。。';
			}
		}
		if($this->data['status']!='error') {
			if(!$this->data['data']['topic'] = $this->ci->db->table('message')->where('id',$args['topic_id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='暂时没有此消息，请核实您的操作。。';
			}
			$id=$this->loguser->id;
			$this->data['data']['replay']= $this->ci->db->table('message')
							->where('topic_id',$this->data['data']['topic']->id)
							->where(function($query) use ($id){
								$query->where(function($query) use ($id){
										$query->where('type', '=', 2)
											  ->where('send_id', '=', $id);
									})
								->orWhere(function($query) use ($id){
										$query->where('type', '=', 1)
											  ->where('accept_id', '=', $id);
									});
							})
							->orderBy('send_time','desc')->get();
		}
		// $response->withJson($this->data);
		$this->display('MessageInfo');
	}

	public function MessageAdd($request, $response, $args) {
		//充值申请
		//post
		$message=$request->getParsedBody();
		if($this->data['status']!='error') {
			if(!$message['contants']) {
				$this->data['status']='error';
				$this->data['data']='请输入消息内容。。';
			}
		}
		if($this->data['status']!='error'){
			$message['send_id']=$this->loguser->id;
			$message['send_name']=$this->loguser->real_name;
			$message['send_time']=time();
			$message['type']=2;
			$message['topic_id']=0;
			if($this->data['data'] = $this->ci->db->table('message')->insertGetId($message)) {
				$this->data['status']='success';
				$this->data['refer']=$this->ci->router->pathFor('Messages');
				$this->data['data']='发起消息成功，谢谢';
			} else {
				$this->data['status']='error';
				$this->data['data']='发起消息失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function MessageReply($request, $response, $args) {
		//充值申请
		//post
		$message=$request->getParsedBody();
		if($this->data['status']!='error') {
			if(!$message['contants']) {
				$this->data['status']='error';
				$this->data['data']='请输入消息内容。。';
			}
		}
		if($this->data['status']!='error') {
			if(!$args['topic_id']) {
				$this->data['status']='error';
				$this->data['data']='请输入消息主题ID。。';
			} else {
				if(!$this->data['data'] = $this->ci->db->table('message')->where('id',$args['topic_id'])->first()) {
					$this->data['status']='error';
					$this->data['data']='暂时没有此主题ID，您是不是回复错了。。';
				} else {
					$message['topic_id']=$args['topic_id'];
				}
			}
		}
		if($this->data['status']!='error'){
			$message['send_id']=$this->loguser->id;
			$message['send_name']=$this->loguser->real_name;
			$message['send_time']=time();
			$message['type']=2;
			$message['topic_id']=$message['topic_id'];
			if($this->data['data'] = $this->ci->db->table('message')->insertGetId($message)) {
				$this->data['status']='success';
				$this->data['data']='回复消息成功，谢谢';
				$this->data['refer']=$this->ci->router->pathFor('MessageInfo',array('topic_id'=>$message['topic_id']));
			} else {
				$this->data['status']='error';
				$this->data['data']='回复消息失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function MessageDelete($request, $response, $args) {
		//会员删除
		//delete
		if($this->data['data'] = $this->ci->db->table('message')->where('id',$args['id'])->where('send_id',$this->loguser->id)->delete()) {
			$this->data['status']='success';
			$this->data['data']='删除消息成功！';
		} else {
			$this->data['status']='error';
			$this->data['data']='删除消息失败，您没有权限删除或者该消息不存在';
		}
		$response->withJson($this->data);
	}

}