<?php
class Incharge extends LoggedController {

	public function ToIncharge($request, $response, $args) {
		//充值申请
		//post
		$incharge=$request->getParsedBody();
		if($this->data['status']!='error') {
			if(!$incharge['user_pass']) {
				$this->data['status']='error';
				$this->data['data']='请输入您的支付密码。。';
			} else {
				$incharge['user_pass']=md5(md5($incharge['user_pass']).'whj');
				if($incharge['user_pass']!=$this->loguser->user_pass2) {
					$this->data['status']='error';
					$this->data['data']='您的支付密码输入错误。。。';
				} else {
					unset($incharge['user_pass']);
				}
			}
		}

		if($this->data['status']!='error') {
			if(!$incharge['incharge_money']) {
				$this->data['status']='error';
				$this->data['data']='请输入需要充值的金额。。';
			}
		}
		if($this->data['status']!='error') {
			$incharge['user_id']=$this->loguser->id;
			if($old_incharge=$this->ci->db->table('incharge')->where('user_id',$incharge['user_id'])->where('incharge_status',0)->first()) {
				$this->data['status']='error';
				$this->data['data']='您还有未处理的'.$old_incharge->incharge_money.'元充值申请正在等待管理员审核。。请稍后再试。。谢谢';
			}
		}
		if($this->data['status']!='error'){
			$incharge['incharge_time']=time();
			$incharge['incharge_status']=0;
			$incharge['real_name']=$this->loguser->real_name;
			if($this->data['data'] = $this->ci->db->table('incharge')->insertGetId($incharge)) {
				$this->data['status']='success';
				$this->data['refer']=$this->ci->router->pathFor('Incharges');
				$this->data['data']='申请充值'.$incharge['incharge_money'].'元成功！请等待管理员审核，谢谢';
			} else {
				$this->data['status']='error';
				$this->data['data']='申请充值失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function Incharges($request, $response, $args) {
		//用户充值列表
		//post  /incharges
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('incharge')->where('user_id',$this->loguser->id)->orderBy('incharge_time','desc')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('incharge')->where('user_id',$this->loguser->id)->orderBy('incharge_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
		}
		$this->display('Incharge');
	}
	public function GetIncharge($request, $response, $args) {
		//单个充值信息
		//get  /incharge/id
		if($this->data['status']!='error'){
			if(!$this->data['data']= $this->ci->db->table('incharge')->where('user_id',$this->loguser->id)->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此充值信息。。';
			} else {
				$this->data['status']='success';
			}
		}
		$response->withJson($this->data);
	}
}