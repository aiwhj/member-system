<?php
class Login extends BaseController {
	
	public function AdminsInfo($request, $response, $args) {
		//获取Admins列表
		//get
		// $this->data= $this->ci->db->table('role_admin')->get();
		echo $this->ci->session->get('admin_name');
		echo $this->ci->session->get('admin_id');
		echo getIp();
		//$response->withJson($this->data);
	}
	public function AdminsLogin($request, $response, $args) {
		//会员登陆
		//get
		$this->data['data']='您已经登陆过了。。。';
		$this->display('Admin\AdminsLogin');
	}
	public function AdminsLogout($request, $response, $args) {
		$this->ci->session->delete('admin_name');
		$this->ci->session->delete('admin_id');
		app_redirect($this->ci->router->pathFor('AdminsLogin'));
	}
	public function AdminsLoginDo($request, $response, $args) {
		//会员登陆处理
		//post
		$admin=$request->getParsedBody();
		if($this->ci->session->get('admin_name')&&$this->ci->session->get('admin_id')) {
			$admin_info = $this->ci->db->table('role_user')->where('id',$this->ci->session->get('admin_id'))->first();
			$this->data['status']='error';
			$this->data['data']='您已经登陆过了。。。';
			if($admin_info->role_id!=1) {
				$rect=$this->ci->db->table('auth_access')->where('role_id',$admin_info->role_id)->where('pid',0)->first();
				$rect2=$this->ci->db->table('auth_access')->where('role_id',$admin_info->role_id)->where('pid',$rect->mid)->where('display',1)->first();
				$this->data['refer']=$rect2->rule_url;
			} else {
				$this->data['refer']=$this->ci->router->pathFor('Matter');
			}
		}
		if($this->data['status']!='error') {
			if(!$admin['admin_name']) {
				$this->data['status']='error';
				$this->data['data']='请输入用户名';
			}
		}
		if($this->data['status']!='error') {
			if(!$admin['admin_pass']) {
				$this->data['status']='error';
				$this->data['data']='请输入用户密码';
			} else {
				$admin['admin_pass']=md5(md5($admin['admin_pass']).'whjsys');
			}
		}
		if($this->data['status']!='error') {
			if(!$admin['verity']) {
				$this->data['status']='error';
				$this->data['data']='请输入验证码';
			} else {
				if($admin['verity'] != $this->ci->session->get('captcha', 'www')) {
					$this->data['status']='error';
					$this->data['data']='您输入的验证码不正确';
				}
			}
		}
		if($this->data['status']!='error'){
			unset($admin['verity']);
			$this->ci->session->delete('verity');
			if(!$admin_info = $this->ci->db->table('role_user')->where($admin)->first()) {
				$this->data['status']='error';
				$this->data['data']='登陆失败，您输入的密码是不是错了，您再试试？';
			} else {
				$this->ci->session->set('admin_id', $admin_info->id);
				$this->ci->session->set('admin_name', $admin_info->admin_name);
				$log['admin_id']=$admin_info->id;
				$log['admin_name']=$admin_info->admin_name;
				$log['login_ip']=getIp();
				$log['login_time']=time();
				$this->ci->db->table('adminlogin_log')->insertGetId($log);
				$this->data['status']='success';
				$this->data['data']='登陆成功！';
				if($admin_info->role_id!=1) {
					$rect=$this->ci->db->table('auth_access')->where('role_id',$admin_info->role_id)->where('pid',0)->first();
					$rect2=$this->ci->db->table('auth_access')->where('role_id',$admin_info->role_id)->where('pid',$rect->mid)->where('display',1)->first();
					$this->data['refer']=$rect2->rule_url;
				} else {
					$this->data['refer']=$this->ci->router->pathFor('Matter');
				}
				
			}
		}
		$response->withJson($this->data);
	}
}