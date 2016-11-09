<?php
class LoggedController extends BaseController {
	public function __construct($ci) {
		parent::__construct($ci);
		if((!$this->loguser['user_name']=$this->ci->session->get('user_name'))||(!$this->loguser['user_id']=$this->ci->session->get('user_id'))) {
			$this->data['status']='error';
			$this->data['data']='您还没有登陆，请先登录。。谢谢';
			app_redirect($this->ci->router->pathFor('MembersLogin'));
		} else {
			if(!$this->loguser = $this->ci->db->table('members')->where('id',$this->loguser['user_id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有您的用户信息。。。请检查登陆状态。。';
				$this->jump($this->ci->router->pathFor('Cashs'));
			}
			$title=$this->ci->db->table('auth_rule')->where('url','like','/system/'.$ci->request->getUri()->getPath())->select('title')->first();
			$this->data['title']=$title->title;
			$this->data['user']=$this->loguser;
		}
	}
}