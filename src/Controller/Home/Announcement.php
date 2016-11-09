<?php
class Announcement extends LoggedController {

	public function Announcements($request, $response, $args) {
		//获取Admins列表
		//get
		if($this->data['status']!='error') {
			$this->data['status']='success';
			$count= $this->ci->db->table('announcement')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('announcement')->orderBy('send_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
		}
		// $response->withJson($this->data);
		$this->display('Announcement');
	}
	public function AnnouncementInfo($request, $response, $args) {
		//获取Admins列表
		//get
		if($this->data['status']!='error') {
			$this->data['status']='success';
			$this->data['data']= $this->ci->db->table('announcement')->where('id',$args['id'])->first();
		}
		$this->display('AnnouncementInfo');
	}
}