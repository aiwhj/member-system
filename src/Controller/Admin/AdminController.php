<?php
class AdminController extends BaseController {
	public function __construct($ci) {
		parent::__construct($ci);
		if((!$this->loguser['user_name']=$this->ci->session->get('admin_name'))||(!$this->loguser['admin_id']=$this->ci->session->get('admin_id'))) {
			$this->data['status']='error';
			$this->data['data']='您还没有登陆，请先登录。。谢谢';
			app_redirect($this->ci->router->pathFor('AdminsLogin'));
		} else {
			if(!$this->loguser = $this->ci->db->table('role_user')->where('id',$this->loguser['admin_id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有您的用户信息。。。请检查登陆状态。。';
			} else {
				$this->data['user']=$this->loguser;
				if(!$this->role = $this->ci->db->table('role')->where('id',$this->loguser->role_id)->first()) {
					$this->data['status']='error';
					$this->data['data']='您还不是管理员，请联系后台工作人员。。';
				} else {
					if(!$this->menu = $this->ci->db->table('auth_access')->where('role_id',$this->loguser->role_id)->get()) {
						$this->data['status']='error';
						$this->data['data']='您还不是管理员，请联系后台工作人员。。';
					} else {
						$title=$this->ci->db->table('auth_rule')->where('url','like',$ci->request->getUri()->getPath())->select('title')->first();
						$this->data['title']=$title->title;
						if($this->loguser->role_id!=1) {
							$this->data['menu']=$this->ci->db->table('auth_access')->where('role_id',$this->loguser->role_id)->where('pid',0)->get();
							foreach($this->data['menu'] as $k=>$boy) {
								$this->data['menu'][$k]->boy=$this->ci->db->table('auth_access')->where('role_id',$this->loguser->role_id)->where('pid',$boy->mid)->where('display',1)->get();
							}
							foreach($this->menu as $key => $menu) {
								if((stristr($ci->request->getUri()->getPath(),$menu->rule_url))&&($menu->rule_method==$ci->request->getMethod())) {
									$menu_auth='whj';
								}
							}
							if($menu_auth!='whj') {
								$this->data['status']='error';
								$this->data['data']='您没有权限访问此功能。。。';
								app_redirect($this->ci->router->pathFor('AdminsLogin'));
							}
						} else {
							$this->data['menu']=$this->ci->db->table('auth_rule')->where('pid',0)->get();
							foreach($this->data['menu'] as $k=>$boy) {
								$this->data['menu'][$k]->boy=$this->ci->db->table('auth_rule')->where('pid',$boy->id)->select('title','url as rule_url')->where('display',1)->get();
							}
						}
					}
				}
			}
		}

	}
}