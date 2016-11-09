<?php
class Index extends BaseController {
	public function __construct($ci) {
		parent::__construct($ci);
	}
	public function Index($request, $response, $args) {
		//获取Admins列表
		//get
		if($this->data['status']!='error') {
			if((!$this->loguser['user_name']=$this->ci->session->get('user_name'))||(!$this->loguser['user_id']=$this->ci->session->get('user_id'))) {
				$this->data['status']='error';
				$this->data['data']='您还没有登陆，请先登录。。谢谢';
				app_redirect($this->ci->router->pathFor('MembersLogin'));
			} else {
				app_redirect($this->ci->router->pathFor('PersonalInfo'));
			}
		}
		// $response->withJson($this->data);
		// $this->display('Announcement');
	}
}