<?php
class PrepayAdmin extends AdminController {

	public function Prepays($request, $response, $args) {
		//获取Admins列表
		//get
		if($this->data['status']!='error') {
			$this->data['status']='success';
			$count= $this->ci->db->table('prepay')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('prepay')->orderBy('time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		$this->display('Admin\Prepays');
	}
}