<?php
class Admins extends AdminController {

	public function AdminsInfo($request, $response, $args) {
		//获取Admins列表
		//get
		// $this->data= $this->ci->db->table('admins')->get();
		echo $this->ci->session->get('admin_name');
		echo $this->ci->session->get('admin_id');
		echo getIp();
		//$response->withJson($this->data);
	}
	public function AdminsLoginLog($request, $response, $args) {
		//获取Admins列表
		//get
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('adminlogin_log')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('adminlogin_log')->orderBy('login_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		//$response->withJson($this->data);
		$this->display('Admin\AdminsLoginLog');
	}
	public function PassUpdate($request, $response, $args) {
		//会员注册
		//post
		$user_info = $this->ci->db->table('role_user')->where('id',$this->loguser->id)->first();
		$this->data['data']['admin_name']=$user_info->admin_name;
		$this->display("Admin\PassUpdate");
	}
	public function AdminInfo($request, $response, $id) {
		//获取单个Admin信息
		//get
		if(!$id) {
			$this->data['status']='error';
		} else {
			if(!$admin = $this->ci->db->table('admins')->where('id',$id)->first()) {
				$this->data['status']='error';
				$this->data['data']='暂时没有此用户信息。。';
			} else {
				return $admin;
			}
		}
	}
	public function AdminsRoleAdd($request, $response, $id) {
		//获取单个Admin信息
		//get
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('role')->count();
			$page=$this->page($count,10);
			$this->data['data']= $this->ci->db->table('role')->orderBy('create_time','asc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['role']= $this->ci->db->table('auth_rule')->select('id','pid','title as name')->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		//Admin\AdminsRoleAdd
		$this->display('Admin\AdminsRoleAdd');
	}
	public function AdminsAuthRule($request, $response, $id) {
		//获取单个Admin信息
		//get
		if($this->data['status']!='error'){
			$this->data['data']= $this->ci->db->table('auth_rule')->select('id','pid as pId','title as name')->get();
		}
		//Admin\AdminsRoleAdd
		$response->withJson($this->data);
	}
	public function AdminsList($request, $response, $id) {
		//获取单个Admin信息
		//get
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('role_user')->count();
			$page=$this->page($count,10);
			$this->data['data']= $this->ci->db->table('role_user')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
			$this->data['roles']= $this->ci->db->table('role')->select('id','name')->get();
		}
		$this->display('Admin\AdminsList');
	}
	public function AdminsReg($request, $response, $args) {
		//会员注册
		//post
		$this->ci->view->render($response, 'AdminsReg.html');
	}
	public function AdminsRegDo($request, $response, $args) {
		//会员注册处理
		//post
		$admin=$request->getParsedBody();
		if(!$admin['role_id']) {
			$this->data['status']='error';
			$this->data['data']='请选择您的管理角色';
		}
		if($this->data['status']!='error') {
			if(!$role=$this->ci->db->table('role')->where('id',$admin['role_id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此管理角色';
			}
		}
		if($this->data['status']!='error') {
			if(!$admin['admin_name']) {
				$this->data['status']='error';
				$this->data['data']='请输入用于登陆的管理员账号';
			}
		}
		if($this->data['status']!='error') {
			if($this->ci->db->table('role_user')->where('admin_name',$admin['admin_name'])->first()) {
				$this->data['status']='error';
				$this->data['data']='你输入的用于登陆的账户名已经有管理员使用，请换一个试试。。';
			}
		}
		if($this->data['status']!='error') {
			if(!$admin['admin_pass']) {
				$this->data['status']='error';
				$this->data['data']='请您设置该管理员登陆密码';
			}
		}
		if($this->data['status']!='error') {
			if(!$admin['re_admin_pass']) {
				$this->data['status']='error';
				$this->data['data']='请重复输入登录密码';
			} else {
				if($admin['re_admin_pass']!=$admin['admin_pass']) {
					$this->data['status']='error';
					$this->data['data']='您输入的确认密码不一致。。请重新输入';
				} else {
					$admin['admin_pass']=md5(md5($admin['re_admin_pass']).'whjsys');
					$admin['role_name']=$role->name;
					unset($admin['re_admin_pass']);
				}
			}
		}
		if($this->data['status']!='error'){
			if($this->data['data'] = $this->ci->db->table('role_user')->insertGetId($admin)) {
				$this->data['status']='success';
				$this->data['data']='添加管理员成功！谢谢';
				$this->data['refer']=$this->ci->router->pathFor('AdminsList');
			} else {
				$this->data['status']='error';
				$this->data['data']='添加管理员失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function AdminsRoleAddDo($request, $response, $args) {
		//会员注册处理
		//post
		$admin=$request->getParsedBody();
		if($this->data['status']!='error') {
			if(!$admin['name']) {
				$this->data['status']='error';
				$this->data['data']='请输入角色名';
			}
		}
		if($this->data['status']!='error') {
			if($this->ci->db->table('role')->where('name',$admin['name'])->first()) {
				$this->data['status']='error';
				$this->data['data']='你输入角色名已经使用，请换一个试试。。';
			}
		}
		if($this->data['status']!='error'){
			$role_user['name']=$admin['name'];
			$role_user['remark']=$admin['remark'];
			$role_user['create_time']=time();
			if($role_id= $this->ci->db->table('role')->insertGetId($role_user)) {
				if(!$admin['roles']) {
					$this->data['status']='error';
					$this->data['data']='请选择该角色权限';
				} else {
					$admin['roles']=json_decode($admin['roles'],1);
					foreach($admin['roles'] as $roles) {
						if($auth_rule=$this->ci->db->table('auth_rule')->where('id',$roles)->first()) {
							$role['role_id']=$role_id;
							$role['mid']=$auth_rule->id;
							$role['pid']=$auth_rule->pid;
							$role['rule_url']=$auth_rule->url;
							$role['rule_method']=$auth_rule->method;
							$role['title']=$auth_rule->title;
							$role['display']=$auth_rule->display;
							$this->data['data'] = $this->ci->db->table('auth_access')->insertGetId($role);
						}
					}
					$this->data['status']='success';
					$this->data['data']='添加管理角色成功！谢谢';
					$this->data['refer']=$this->ci->router->pathFor('AdminsRoleAdd');
				}
			} else {
				$this->data['status']='error';
				$this->data['data']='添加管理角色失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function AdminsInfoUpdate($request, $response, $args) {
		//会员信息更新
		//put
		$admin=$request->getParsedBody();
		if(!$oldadmin = $this->ci->db->table('role_user')->where('id',$this->loguser->id)->first()) {
				$this->data['status']='error';
				$this->data['data']='还没有此管理员信息，请核对后再试';
		}
		if($this->data['status']!='error') {
			if($admin['old_admin_pass']) {
				if($oldadmin->admin_pass!=md5(md5($admin['old_admin_pass']).'whjsys')) {
					$this->data['status']='error';
					$this->data['data']='原密码不正确';
				} else {
					if(!$admin['re_admin_pass']) {
						$this->data['status']='error';
						$this->data['data']='请输入确认的登陆密码';
					} else {
						if($admin['re_admin_pass']!=$admin['admin_pass']) {
							$this->data['status']='error';
							$this->data['data']='您输入的确认登陆密码不一致。。请重新输入';
						} else {
							$admin['admin_pass']=md5(md5($admin['re_admin_pass']).'whjsys');
							unset($admin['re_admin_pass']);
							unset($admin['old_admin_pass']);
							$admin_update['admin_pass']=$admin['admin_pass'];
						}
					}
				}
			}
		}
		if($this->data['status']!='error'){
			if($this->data['data'] = $this->ci->db->table('role_user')->where('id',$this->loguser->id)->update($admin_update)) {
				$this->data['status']='success';
				$this->data['data']='更新管理员信息成功！';
			} else {
				$this->data['status']='error';
				$this->data['data']='更新管理员信息失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function AdminsInfoDelete($request, $response, $args) {
		//会员删除
		//delete
		if($args['id']==1) {
			$this->data['status']='error';
			$this->data['data']='此用户为最高管理员，不允许删除';
		} else {
			if($this->data['data'] = $this->ci->db->table('role_user')->where('id',$args['id'])->delete()) {
				$this->data['status']='success';
				$this->data['data']='删除成功！';
			} else {
				$this->data['status']='error';
				$this->data['data']='删除失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function RolesDelete($request, $response, $args) {
		//会员删除
		//delete
		if($args['id']==1) {
			$this->data['status']='error';
			$this->data['data']='此角色为超级管理员，不允许删除';
		} else {
			if($this->ci->db->table('role_user')->where('role_id',$args['id'])->first()) {
					$this->data['status']='error';
					$this->data['data']='您选择的用户角色下还有管理员，请删除管理员后再删除角色。。';
			} else {
				if($this->data['data'] = $this->ci->db->table('role')->where('id',$args['id'])->delete()) {
					$this->data['data'] = $this->ci->db->table('auth_access')->where('role_id',$args['id'])->delete();
					$this->data['status']='success';
					$this->data['data']='删除成功！';
				} else {
					$this->data['status']='error';
					$this->data['data']='删除失败，请重试！';
				}
			}
		}
		$response->withJson($this->data);
	}
}