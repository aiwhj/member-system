<?php
class MessageAdmin extends AdminController {

	public function Messages($request, $response, $args) {
		//获取Admins列表
		//get
		$id=$this->loguser->id;
		$count= $this->ci->db->table('message')
						->where('topic_id',0)
						->count();
		$page=$this->page($count,2);
		$this->data['data']= $this->ci->db->table('message')
						->where('topic_id',0)
						->orderBy('send_time','desc')->skip($page->firstRow)->take($page->listRows)
						->get();
		$this->data['page']=$page->show();
		$this->data['count']=$count;
		// $response->withJson($this->data);
		$this->display('Admin\Messages');
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
										$query->where('type', '=', 1)
											  ->where('send_id', '=', $id);
									})
								->orWhere(function($query) use ($id){
										$query->where('type', '=', 2)
										->where('accept_id', '=', 0);
									});
							})
							->get();
		}
		// $response->withJson($this->data);
		$this->display('Admin\MessageInfo');
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
		if($this->data['status']!='error') {
			if(!$message['accept_id']) {
				$this->data['status']='error';
				$this->data['data']='请输入消息接受者ID。。';
			} else {
				if(!$member = $this->ci->db->table('members')->where('id',$message['accept_id'])->first()) {
						$this->data['status']='error';
						$this->data['data']='接受者不存在，请您核实后再发送。。。';
				} else {
					$message['accept_name']=$member->real_name;
				}
			}
		}
		if($this->data['status']!='error'){
			$message['send_id']=$this->loguser->id;
			$message['send_name']=$this->loguser->admin_name;
			$message['send_time']=time();
			$message['type']=1;
			$message['topic_id']=0;
			if($this->data['data'] = $this->ci->db->table('message')->insertGetId($message)) {
				$this->data['status']='success';
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
		if($this->data['status']!='error') {
			if(!$message['accept_id']) {
				$this->data['status']='error';
				$this->data['data']='请输入消息接受者ID。。';
			} else {
				if(!$member = $this->ci->db->table('members')->where('id',$message['accept_id'])->first()) {
						$this->data['status']='error';
						$this->data['data']='接受者不存在，请您核实后再发送。。。';
				} else {
					$message['accept_name']=$member->real_name;
				}
			}
		}
		if($this->data['status']!='error'){
			$message['send_id']=$this->loguser->id;
			$message['send_name']=$this->loguser->admin_name;
			$message['send_time']=time();
			$message['type']=1;
			$message['topic_id']=$message['topic_id'];
			if($this->data['data'] = $this->ci->db->table('message')->insertGetId($message)) {
				$this->data['status']='success';
				$this->data['data']='回复消息成功，谢谢';
				$this->data['refer']=$this->ci->router->pathFor('MessageInfoAdmin',array('topic_id'=>$message['topic_id']));
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
		if($this->data['data'] = $this->ci->db->table('message')->where('id',$args['id'])->delete()) {
			$this->data['status']='success';
			$this->data['data']='删除消息成功！';
		} else {
			$this->data['status']='error';
			$this->data['data']='删除消息失败，请重试！';
		}
		$response->withJson($this->data);
	}

}