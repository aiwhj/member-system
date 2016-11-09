<?php
class BaseController{
	protected $ci;
	protected $data;
	protected $loguser;
	protected $role;
	protected $menus;
	public function __construct($ci) {
       $this->ci = $ci;
	   if(!$this->ci->member_config = $this->ci->db->table('config')->where('id',1)->first()) {
			$this->data['status']='error';
			$this->data['data']='读取配置信息出错。。';
	   }
	}
	function __call($function_name,$arguments) {
		if($function_name=='display') {
			if(count($arguments)==1&&$arguments[0]) {
				if($this->data['status']=='error') {
					$this->ci->response->withJson($this->data);
				} else {
					$this->ci->view->render($this->ci->response, $arguments[0].'.html', $this->data);
				}
			} else {
				$this->ci->response->withJson($this->data);
			}
		} elseif($function_name=='jump') {
			if($this->data['status']=='error') {
				$this->data['emoji']=':-(';
			} else {
				$this->data['emoji']=':-)';
			}
			$this->data['waittime']=3;
			$this->data['jumpurl']=$arguments[0];
			if(count($arguments)==2&&$arguments[1]) {
				$this->data['waittime']=$arguments[0];
			}
			$this->ci->view->render($this->ci->response, 'jump.html', $this->data);
		} elseif($function_name=='page') {
			$pagest=new Pagest($this->ci, $arguments[0], $arguments[1]);
			return $pagest;
		}
	}
}