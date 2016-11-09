<?php
class Transfer extends LoggedController {

	public function ToTransfer($request, $response, $args) {
		//转账申请
		//post
		$transfer=$request->getParsedBody();
		if($this->data['status']!='error') {
			if(!$transfer['user_pass']) {
				$this->data['status']='error';
				$this->data['data']='请输入您的支付密码。。';
			} else {
				$transfer['user_pass']=md5(md5($transfer['user_pass']).'whj');
				if($transfer['user_pass']!=$this->loguser->user_pass2) {
					$this->data['status']='error';
					$this->data['data']='您的支付密码输入错误。。。';
				} else {
					unset($transfer['user_pass']);
				}
			}
		}
		if($this->data['status']!='error') {
			if(!$transfer['phone_verity']) {
				$this->data['status']='error';
				$this->data['data']='请输入短信验证码';
			} else {
				if($transfer['phone_verity'] != $this->ci->session->get('phone_verity', 'www')) {
					$this->data['status']='error';
					$this->data['data']='您输入的短信验证码出错了';
				} else {
					unset($transfer['phone_verity']);
				}
			}
		}

		if($this->data['status']!='error') {
			if(!$transfer['transfer_money']) {
				$this->data['status']='error';
				$this->data['data']='请输入需要转账的金额。。';
			} else {
				if($transfer['transfer_money']>$this->loguser->flow_money) {
					$this->data['status']='error';
					$this->data['data']='您的流动资金不足'.$transfer['transfer_money'].'元。。请您核实后再申请。。';
				}
			}
		}
		if($this->data['status']!='error') {
			if(!$transfer['accept_id']) {
				$this->data['status']='error';
				$this->data['data']='请输入需要转账的用户ID。。';
			}
		}
		if($this->data['status']!='error') {
			if(!$transfer['accept_name']) {
				$this->data['status']='error';
				$this->data['data']='请输入需要转账的用户姓名。。';
			}
		}
		if($this->data['status']!='error') {
			if(!$member=$this->ci->db->table('members')->where('id',$transfer['accept_id'])->where('real_name',$transfer['accept_name'])->first()) {
				$this->data['status']='error';
				$this->data['data']='您输入的用户名及ID对不上号，请您核实后再试，以免对您造成不必要的损失。。';
			}
		}
		if($this->data['status']!='error') {
			if($oldtransfer=$this->ci->db->table('transfer')->where('accept_id',$transfer['accept_id'])->where('send_id',$this->loguser->id)->first()) {
				if((time()-$oldtransfer->transfer_time)<$this->ci->member_config->transfer_limit) {
					$this->data['status']='error';
					$this->data['data']=$this->ci->member_config->transfer_limit.'秒内不允许向同一个人进行转账。。';
				}
			}
		}
		if($this->data['status']!='error'){
			$transfer['transfer_time']=time();
			$transfer['send_name']=$this->loguser->real_name;
			$transfer['send_id']=$this->loguser->id;
			$send_log['flow_money']=$this->loguser->flow_money-$transfer['transfer_money'];
			$accept_log['flow_money']=$member->flow_money+$transfer['transfer_money'];
			if($this->ci->db->table('members')->where('id',$this->loguser->id)->update($send_log)) {
				if($this->ci->db->table('members')->where('id',$member->id)->update($accept_log)) {
					if($this->data['data'] = $this->ci->db->table('transfer')->insertGetId($transfer)) {
						$this->data['status']='success';
						$this->data['refer']=$this->ci->router->pathFor('Transfers');
						$this->data['data']='转账'.$transfer['transfer_money'].'元成功！谢谢';
						$this->ci->session->delete('phone_verity');
					} else {
						$this->data['status']='error';
						$this->data['data']='请转账失败，请重试！';
					}
				} else {
					$this->ci->db->table('members')->where('id',$this->loguser->id)->update('flow_money',$this->loguser->flow_money);
					$this->data['status']='error';
					$this->data['data']='申请转账失败，请重试！';
				}
			} else {
				$this->data['status']='error';
				$this->data['data']='申请转账失败，请重试！';
			}
		}
		$response->withJson($this->data);
		// $this->jump($this->ci->router->pathFor('Transfers'));
	}
	public function Transfers($request, $response, $args) {
		//用户转账列表
		//post  /transfers
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('transfer')->where('send_id',$this->loguser->id)->orderBy('transfer_time','desc')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('transfer')->where('send_id',$this->loguser->id)->orderBy('transfer_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
		}
		$this->display('Transfer');
	}
	public function GetTransfer($request, $response, $args) {
		//单个转账信息
		//get  /transfer/id
		if($this->data['status']!='error'){
			if(!$this->data['data']= $this->ci->db->table('transfer')->where('send_id',$this->loguser->id)->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此转账信息。。';
			} else {
				$this->data['status']='success';
			}
		}
		$response->withJson($this->data);
	}
}