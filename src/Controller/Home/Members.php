<?php
class Members extends BaseController {

	public function MembersLogin($request, $response, $args) {
		//会员登陆
		//get
		$user=$request->getParsedBody();
		$this->ci->view->render($response, 'MembersLogin.html');
		//$response->withJson($this->data);
	}
	public function MembersLoginDo($request, $response, $args) {
		//会员登陆处理
		//post
		$user=$request->getParsedBody();
		if($this->ci->session->get('user_name')&&$this->ci->session->get('user_id')) {
			$this->data['status']='error';
			$this->data['data']='您已经登陆过了。。。';
			$this->data['refer']=$this->ci->router->pathFor('PersonalInfo');
		}
		if($this->data['status']!='error') {
			if(!$user['user_name']) {
				$this->data['status']='error';
				$this->data['data']='请输入用户名';
			}
		}
		if($this->data['status']!='error') {
			if(!$user['user_pass']) {
				$this->data['status']='error';
				$this->data['data']='请输入用户密码';
			} else {
				$user['user_pass']=md5(md5($user['user_pass']).'whj');
			}
		}
		if($this->data['status']!='error') {
			if(!$user['verity']) {
				$this->data['status']='error';
				$this->data['data']='请输入验证码';
			} else {
				if($user['verity'] != $this->ci->session->get('captcha', 'www')) {
					$this->data['status']='error';
					$this->data['data']='您输入的验证码不正确';
				}
			}
		}
		if($this->data['status']!='error'){
			unset($user['verity']);
			if(!$user_info = $this->ci->db->table('members')->where($user)->first()) {
				$this->data['status']='error';
				$this->data['data']='登陆失败，您输入的密码是不是错了，您再试试？';
			} else {
				if($user_info->user_grade<=0) {
					$this->data['status']='error';
					$this->data['data']='您的账户尚未审核，暂时不能登陆。。';
				} else {
					$this->ci->session->set('user_id', $user_info->id);
					$this->ci->session->set('user_name', $user_info->user_name);
					$log['user_id']=$user_info->id;
					$log['user_name']=$user_info->user_name;
					$log['real_name']=$user_info->real_name;
					$log['login_ip']=getIp();
					$log['login_time']=time();
					$this->ci->db->table('login_log')->insertGetId($log);
					$this->data['status']='success';
					$this->data['data']='登陆成功！';
					$this->data['refer']=$this->ci->router->pathFor('PersonalInfo');
					$this->ci->session->delete('verity');
				}
			}
		}
		$response->withJson($this->data);
	}
	public function MembersReg($request, $response, $args) {
		//会员注册
		//post
		$this->data['data']['regions']['sheng'] = $this->ci->db->table('region_conf')->where('region_level',2)->get();
		$this->data['data']['regions']['shi'] = $this->ci->db->table('region_conf')->where('pid',16)->where('region_level',3)->get();
		$this->data['data']['regions']['xian'] = $this->ci->db->table('region_conf')->where('pid',252)->where('region_level',4)->get();
		$this->data['data']['cartype'] = $this->ci->db->table('cartype')->get();
		$this->display('MembersReg');
	}
	public function MembersRegDo($request, $response, $args) {
		//会员注册处理
		//post
		$user=$request->getParsedBody();
		if(!$user['recommender_id']) {
			$this->data['status']='error';
			$this->data['data']='没有推荐人编号';
		} else {
			if(!is_numeric($user['recommender_id'])) {
				$user['recommender_id']=(int)str_replace('jc','',$user['recommender_id']);
			}
		}
/* 		if($this->data['status']!='error') {
			if(!$user['verity']) {
				$this->data['status']='error';
				$this->data['data']='请输入验证码';
			} else {
				if($user['verity'] != $this->ci->session->get('captcha', 'www')) {
					$this->data['status']='error';
					$this->data['data']='您输入的验证码不正确';
				} else {
					unset($user['verity']);
					
				}
			}
		} */
/* 		if($this->data['status']!='error') {
			if(!$user['phone_verity']) {
				$this->data['status']='error';
				$this->data['data']='请输入短信验证码';
			} else {
				if($user['phone_verity'] != $this->ci->session->get('phone_verity', 'www')) {
					$this->data['status']='error';
					$this->data['data']='您输入的短信验证码不正确';
				} else {
					unset($user['phone_verity']);
					
				}
			}
		} */
		unset($user['phone_verity']);
		if($this->data['status']!='error') {
			if(!$recommender = $this->ci->db->table('members')->where('id',$user['recommender_id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='暂时没有此推荐人。。';
			} else {
				if($recommender->real_name!=$user['recommender_name']) {
					$this->data['status']='error';
					$this->data['data']='您填写的推荐人姓名与编号不符。。。请您核对后再注册';
				} else {
					if($recommender->user_grade<=0) {
						$this->data['status']='error';
						$this->data['data']='您填写的推荐人还未审核。。。暂时不能使用其代号';
					} else {
						$user['recommender_name']=$recommender->real_name;
					}
				}
			}
		}
		if($this->data['status']!='error') {
			if(!$user['real_name']) {
				$this->data['status']='error';
				$this->data['data']='请输入会员姓名';
			}
		}
		if($this->data['status']!='error') {
			if($user['cartype']<=0) {
				if(!$user['cartype2']) {
					$this->data['status']='error';
					$this->data['data']='请输入你的车型';
				} else {
					$user['cartype']=$user['cartype2'];
				}
			}
		}
		unset($user['cartype2']);
		if($this->data['status']!='error') {
			if(!$user['user_name']) {
				$this->data['status']='error';
				$this->data['data']='请输入用于登陆的账户名';
			}
		}
		if($this->data['status']!='error') {
			if($this->ci->db->table('members')->where('user_name',$user['user_name'])->first()) {
				$this->data['status']='error';
				$this->data['data']='你输入的用于登陆的账户名已经有人使用，请换一个试试。。';
			}
		}
		if($this->data['status']!='error') {
			if(!$user['user_pass']) {
				$this->data['status']='error';
				$this->data['data']='请您设置您的登陆密码';
			}
		}
		if($this->data['status']!='error') {
			if(!$user['re_user_pass']) {
				$this->data['status']='error';
				$this->data['data']='请重复输入登录密码';
			} else {
				if($user['re_user_pass']!=$user['user_pass']) {
					$this->data['status']='error';
					$this->data['data']='您输入的确认密码不一致。。请重新输入';
				} else {
					$user['user_pass']=md5(md5($user['re_user_pass']).'whj');
					unset($user['re_user_pass']);
				}
			}
		}
		if($this->data['status']!='error') {
			if(!$user['user_pass2']) {
				$this->data['status']='error';
				$this->data['data']='请设置您的支付密码';
			}
		}
		if($this->data['status']!='error') {
			if(!$user['re_user_pass2']) {
				$this->data['status']='error';
				$this->data['data']='请输入确认的支付密码';
			} else {
				if($user['re_user_pass2']!=$user['user_pass2']) {
					$this->data['status']='error';
					$this->data['data']='您输入的确认支付密码不一致。。请重新输入';
				} else {
					$user['user_pass2']=md5(md5($user['re_user_pass2']).'whj');
					unset($user['re_user_pass2']);
				}
			}
		}
		if($this->data['status']!='error') {
			if(!$user['phone']) {
				$this->data['status']='error';
				$this->data['data']='请输入您的手机';
			}
		}
		if($this->data['status']!='error') {
			if($this->ci->db->table('members')->where('phone',$user['phone'])->first()) {
				$this->data['status']='error';
				$this->data['data']='你输入的手机号码已经有人使用，请换一个试试。。';
			}
		}
		if($this->data['status']!='error') {
			if(!$user['addres']) {
				$this->data['status']='error';
				$this->data['data']='请输入您的详细地址';
			}
		}
		if($this->data['status']!='error') {
			if(!$user['sheng']) {
				$this->data['status']='error';
				$this->data['data']='请输入您居住的省';
			}
		}
		if($this->data['status']!='error') {
			if(!$user['shi']) {
				$this->data['status']='error';
				$this->data['data']='请输入您居住的市';
			}
		}
		if($this->data['status']!='error') {
			if(!$user['xian']) {
				$this->data['status']='error';
				$this->data['data']='请输入您居住的县（区）';
			}
		}
		if($this->data['status']!='error') {
			$user['reg_time']=time();			
		}
		if($this->data['status']!='error'){
			if($this->data['data'] = $this->ci->db->table('members')->insertGetId($user)) {
				$this->data['status']='success';
				$this->data['data']='注册成功！请等待管理员审核。。谢谢';
				$this->ci->session->delete('phone_verity');
				$this->ci->session->delete('verity');
				$this->data['refer']=$this->ci->router->pathFor('MembersLogin');
			} else {
				$this->data['status']='error';
				$this->data['data']='注册失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function MembersInfoUpdate($request, $response, $args) {
		//会员信息更新
		//put
		if(!$olduser = $this->ci->db->table('members')->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='还没有此会员信息';
		}
		$user=$request->getParsedBody();
		// print_r($user);
		if($this->data['status']!='error') {
			$user_update['user_pass']=$user['user_pass']?md5(md5($user['user_pass']).'whj'):$olduser->user_pass;
			$user_update['user_pass2']=$user['user_pass2']?md5(md5($user['user_pass2']).'whj'):$olduser->user_pass2;
			$user_update['car_money']=$user['car_money']?$user['car_money']:$olduser->car_money;
			$user_update['real_name']=$user['real_name']?$user['real_name']:$olduser->real_name;
			$user_update['email']=$user['email']?$user['email']:$olduser->email;
			$user_update['qq']=$user['qq']?$user['qq']:$olduser->qq;
			$user_update['idcard']=$user['idcard']?$user['idcard']:$olduser->idcard;
			$user_update['bank_id']=$user['bank_id']?$user['bank_id']:$olduser->bank_id;
			$user_update['bank']=$user['bank']?$user['bank']:$olduser->bank;
			$user_update['addres']=$user['addres']?$user['addres']:$olduser->addres;
		}
		// print_r($user_update);
		if($this->data['status']!='error'){
			if($this->data['data'] = $this->ci->db->table('members')->where('id',$args['id'])->update($user_update)) {
				$this->data['status']='success';
				$this->data['data']='更新会员信息成功！';
			} else {
				$this->data['status']='error';
				$this->data['data']='更新会员信息失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function MembersInfoDelete($request, $response, $args) {
		//会员删除
		//delete
		$response->withJson($this->data);
	}
}