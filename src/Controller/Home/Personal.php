<?php
class Personal extends LoggedController {

	public function PersonalInfo($request, $response, $id) {
		//获取单个Member信息
		//get
		if($this->data['status']!='error') {
			if(!$this->data['data'] = $this->ci->db->table('members')->where('id',$this->loguser->id)->first()) {
				$this->data['status']='error';
				$this->data['data']='暂时没有此用户信息。。';
			} else {
				if(is_numeric($this->data['data']->cartype)) {
					if($cartype=$this->ci->db->table('cartype')->where('id',$this->data['data']->cartype)->first()) {
						$this->data['data']->cartype=$cartype->name;
					} else {
						$this->data['data']->cartype='未填写';
					}
				}
				$this->data['data']->announcement=$this->ci->db->table('announcement')->orderBy('send_time','desc')->first();
				
			}
		}
		// PersonalInfo
		$this->display('PersonalInfo');
	}
	public function LoginLog($request, $response, $id) {
		//获取单个Member信息
		//get
		if($this->data['status']!='error') {
			$count=$this->ci->db->table('login_log')->where('user_id',$this->loguser->id)->count();
			$page=$this->page($count,2);
			if(!$this->data['data']['login_log'] = $this->ci->db->table('login_log')->where('user_id',$this->loguser->id)->orderBy('login_time','desc')->skip($page->firstRow)->take($page->listRows)->get()) {
				$this->data['status']='error';
				$this->data['data']='系统错误';
			} else {
				$this->data['page']=$page->show();
			}
		}
		if($this->data['status']!='error') {
			if(!$this->data['data']['upgrade'] = $this->ci->db->table('upgrade')->where('user_id',$this->loguser->id)->get()) {
				$this->data['status']='error';
				$this->data['data']='系统错误';
			}
		}
		// PersonalInfo
		$this->display('LoginLog');
	}
	public function PersonalLogout($request, $response, $args) {
		$this->ci->session->delete('user_name');
		$this->ci->session->delete('user_id');
		app_redirect($this->ci->router->pathFor('MembersLogin'));
	}
	public function PersonalLogin($request, $response, $args) {
		//会员登陆
		//get
		$user=$request->getParsedBody();
		// $this->ci->view->render($response, 'PersonalLogin.html', [
			// 'name' => '王鸿杰'
		// ]);
		$this->display();
	}
	public function PersonalLoginDo($request, $response, $args) {
		//会员登陆处理
		//post
		$user=$request->getParsedBody();
		if($this->ci->session->get('user_name')&&$this->ci->session->get('user_id')) {
			$this->data['status']='error';
			$this->data['data']='您已经登陆过了。。。';
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
			$this->ci->session->delete('verity');
			if(!$user_info = $this->ci->db->table('members')->where($user)->first()) {
				$this->data['status']='error';
				$this->data['data']='登陆失败，您输入的密码是不是错了，您再试试？';
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
			}
		}
		$response->withJson($this->data);
	}
	public function PersonalReg($request, $response, $args) {
		//会员注册
		//post
		$this->ci->view->render($response, 'PersonalReg.html');
	}
	public function PassUpdate($request, $response, $args) {
		//会员注册
		//post
				$user_info = $this->ci->db->table('members')->where('id',$this->loguser->id)->first();
		$this->data['data']['real_name']=$user_info->real_name;
		$this->display("PassUpdate");
	}
	public function PersonalRegDo($request, $response, $args) {
		//会员注册处理
		//post
		$user=$request->getParsedBody();
		if(!$user['recommender_id']) {
			$this->data['status']='error';
			$this->data['data']='没有推荐人编号';
		}
		if($this->data['status']!='error') {
			if(!$user['verity']) {
				$this->data['status']='error';
				$this->data['data']='请输入验证码';
			} else {
				if($user['verity'] != $this->ci->session->get('captcha', 'www')) {
					$this->data['status']='error';
					$this->data['data']='您输入的验证码不正确';
				} else {
					unset($user['verity']);
					$this->ci->session->delete('verity');
				}
			}
		}
		if($this->data['status']!='error') {
			if(!$recommender = $this->ci->db->table('members')->where('id',$user['recommender_id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='暂时没有此推荐人。。';
			} else {
				$user['recommender_name']=$recommender->real_name;
			}
		}
		if($this->data['status']!='error') {
			if(!$user['real_name']) {
				$this->data['status']='error';
				$this->data['data']='请输入会员姓名';
			}
		}
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
				$this->data['data']='请输入您的住址';
			}
		}
		if($this->data['status']!='error') {
			$user['reg_time']=time();			
		}
		if($this->data['status']!='error'){
			if($this->data['data'] = $this->ci->db->table('members')->insertGetId($user)) {
				$this->data['status']='success';
				$this->data['data']='注册成功！谢谢';
			} else {
				$this->data['status']='error';
				$this->data['data']='注册失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function PersonalInfoUpdate($request, $response, $args) {
		//会员信息更新
		//put
		if(!$olduser = $this->ci->db->table('members')->where('id',$this->loguser->id)->first()) {
				$this->data['status']='error';
				$this->data['data']='还没有此会员信息';
		}
		$user=$request->getParsedBody();
		// print_r($user);
		if($this->data['status']!='error') {
			if($user['old_user_pass2']) {
				if($olduser->user_pass2!=md5(md5($user['old_user_pass2']).'whj')) {
					$this->data['status']='error';
					$this->data['data']='原支付密码不正确';
				} else {
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
							unset($user['old_user_pass2']);
							$user_update['user_pass2']=$user['user_pass2'];
						}
					}
				}
			}
		}
		if($this->data['status']!='error') {
			if($user['old_user_pass']) {
				if($olduser->user_pass!=md5(md5($user['old_user_pass']).'whj')) {
					$this->data['status']='error';
					$this->data['data']='原密码不正确';
				} else {
					if(!$user['re_user_pass']) {
						$this->data['status']='error';
						$this->data['data']='请输入确认的登陆密码';
					} else {
						if($user['re_user_pass']!=$user['user_pass']) {
							$this->data['status']='error';
							$this->data['data']='您输入的确认登陆密码不一致。。请重新输入';
						} else {
							$user['user_pass']=md5(md5($user['re_user_pass']).'whj');
							unset($user['re_user_pass']);
							unset($user['old_user_pass']);
							$user_update['user_pass']=$user['user_pass'];
						}
					}
				}
			}
		}
		if($this->data['status']!='error') {
			if($user['phone']) {
				if(!$user['phone_verity']) {
					$this->data['status']='error';
					$this->data['data']='请输入短信验证码';
				} else {
					if($user['phone_verity'] != $this->ci->session->get('phone_verity', 'www')) {
						$this->data['status']='error';
						$this->data['data']='您输入的短信验证码不正确';
					} else {
						if($olduser->phone!=$user['phone']) {
							$user_update['phone']=$user['phone'];
						}
						unset($user['phone_verity']);
						$this->ci->session->delete('phone_verity');
					}
				}
			}
		}

		if($this->data['status']!='error') {
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
			if($this->data['data'] = $this->ci->db->table('members')->where('id',$this->loguser->id)->update($user_update)) {
				$this->data['status']='success';
				$this->data['data']='更新会员信息成功！';
				$this->data['refer']=$this->ci->router->pathFor('PersonalInfo');
			} else {
				$this->data['status']='error';
				$this->data['data']='更新会员信息失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function PersonalInfoDelete($request, $response, $args) {
		//会员删除
		//delete
		$response->withJson($this->data);
	}
}