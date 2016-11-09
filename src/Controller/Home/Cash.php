<?php
class Cash extends LoggedController {

	public function ToCash($request, $response, $args) {
		//获取提现申请
		//post
		$cash=$request->getParsedBody();
		if($this->data['status']!='error') {
			if(!$cash['phone_verity']) {
				$this->data['status']='error';
				$this->data['data']='请输入短信验证码';
			} else {
				if($cash['phone_verity'] != $this->ci->session->get('phone_verity', 'www')) {
					$this->data['status']='error';
					$this->data['data']='您输入的短信验证码出错了';
				} else {
					unset($cash['phone_verity']);
				}
			}
		}
		if($this->data['status']!='error') {
			if(!$cash['user_pass']) {
				$this->data['status']='error';
				$this->data['data']='请输入您的支付密码。。';
			} else {
				$cash['user_pass']=md5(md5($cash['user_pass']).'whj');
				if($cash['user_pass']!=$this->loguser->user_pass2) {
					$this->data['status']='error';
					$this->data['data']='您的支付密码输入错误。。。';
				} else {
					unset($cash['user_pass']);
				}
			}
		}
		if($this->data['status']!='error') {
			if(!$cash['cash_money']) {
				$this->data['status']='error';
				$this->data['data']='请输入需要提现的金额。。';
			} else {
				if($cash['cash_money']>$this->ci->member_config->cash_max) {
					$this->data['status']='error';
					$this->data['data']='您申请的'.$cash['cash_money'].'元提现申请超过单次最大申请额度'.$this->ci->member_config->cash_max.'，提现申请失败。。';
				}
			}
		}
		if($this->data['status']!='error') {
			$cash['user_id']=$this->loguser->id;
			if($old_cash=$this->ci->db->table('cash')->where('user_id',$cash['user_id'])->where('cash_status',0)->first()) {
				$this->data['status']='error';
				$this->data['data']='您还有未处理的'.$old_cash->cash_money.'元提现申请正在等待管理员审核。。请稍后再试。。谢谢';
			}
		}
		if($this->data['status']!='error') {
			$cash['real_name']=$this->loguser->real_name;
			$cash['cash_process']=$cash['cash_money']*$this->ci->member_config->cash_fee;
			if($this->loguser->flow_money<$cash['cash_process']+$cash['cash_money']) {
				$this->data['status']='error';
				$this->data['data']='您的流动资金不足'.($cash['cash_process']+$cash['cash_money']).'元，提现申请失败。。';
			}
		}
		if($this->data['status']!='error'){
			$cash['cash_time']=time();
			$cash['cash_status']=0;
			if($this->data['data'] = $this->ci->db->table('cash')->insertGetId($cash)) {
				$this->data['status']='success';
				$this->data['refer']=$this->ci->router->pathFor('Cashs');
				$this->data['data']='申请提现'.$cash['cash_money'].'元成功！请等待管理员审核，谢谢';
				$this->ci->session->delete('phone_verity');
			} else {
				$this->data['status']='error';
				$this->data['data']='申请提现失败，请重试！';
			}
		}
		// $this->jump($this->ci->router->pathFor('Cashs'));
		$response->withJson($this->data);
	}
	public function Cashs($request, $response, $args) {
		//用户提现列表
		//post  /cashs
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('cash')->where('user_id',$this->loguser->id)->orderBy('cash_time','desc')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('cash')->where('user_id',$this->loguser->id)->orderBy('cash_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['cash_fee']=$this->ci->member_config->cash_fee*100;
		}
		$this->display('Cashs');
	}
	public function GetCash($request, $response, $args) {
		//单个提现信息
		//get  /cash/id
		if($this->data['status']!='error'){
			if(!$this->data['data']= $this->ci->db->table('cash')->where('user_id',$this->loguser->id)->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此提现信息。。';
			} else {
				$this->data['status']='success';
			}
		}
		$response->withJson($this->data);
	}
}