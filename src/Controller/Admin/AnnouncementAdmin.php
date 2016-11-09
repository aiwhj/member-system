<?php
class AnnouncementAdmin extends AdminController {

	public function Announcements($request, $response, $args) {
		//获取Admins列表
		//get
		if($this->data['status']!='error') {
			$this->data['status']='success';
			$count= $this->ci->db->table('announcement')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('announcement')->orderBy('send_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		$this->display('Admin\Announcements');
	}
	public function Announcement($request, $response, $args) {
		//获取Admins列表
		//get
		$announcement=$request->getParsedBody();
		if($this->data['status']!='error') {
			$this->data['status']='success';
			$this->data['data']= $this->ci->db->table('announcement')->where('id',$announcement['id'])->first();
		}
		$response->withJson($this->data);
	}
	public function AnnouncementAdd($request, $response, $args) {
		//充值申请
		//post
		$announcement=$request->getParsedBody();
		if($this->data['status']!='error') {
			if(!$announcement['title']) {
				$this->data['status']='error';
				$this->data['data']='请输入公告标题。。';
			}
		}
		if($this->data['status']!='error') {
			if(!$announcement['contants']) {
				$this->data['status']='error';
				$this->data['data']='请输入公告内容。。';
			}
		}
		if($this->data['status']!='error'){
			$announcement['send_id']=$this->loguser->id;
			$announcement['send_name']=$this->loguser->admin_name;
			$announcement['send_time']=time();
			if($this->data['data'] = $this->ci->db->table('announcement')->insertGetId($announcement)) {
				$this->data['status']='success';
				$this->data['data']='发起公告成功，谢谢';
			} else {
				$this->data['status']='error';
				$this->data['data']='发起公告失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function AnnouncementUpdate($request, $response, $args) {
		//充值申请
		//post
		$announcement=$request->getParsedBody();
		if(!$oldannouncement = $this->ci->db->table('announcement')->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='还没有此公告信息，请核实一下您输入的公共ID';
		}
		if($this->data['status']!='error'){
			$announcement_update['title']=$announcement['title']?$announcement['title']:$oldannouncement->title;
			$announcement_update['contants']=$announcement['contants']?$announcement['contants']:$oldannouncement->contants;
			if($this->data['data'] = $this->ci->db->table('announcement')->where('id',$args['id'])->update($announcement_update)) {
				$this->data['status']='success';
				$this->data['data']='更新公告信息成功！';
			} else {
				$this->data['status']='error';
				$this->data['data']='更新公告信息失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function AnnouncementDelete($request, $response, $args) {
		//会员删除
		//delete
		if($this->data['data'] = $this->ci->db->table('announcement')->where('id',$args['id'])->delete()) {
			$this->data['status']='success';
			$this->data['data']='删除公告成功！';
		} else {
			$this->data['status']='error';
			$this->data['data']='删除公告失败，请重试！';
		}
		$response->withJson($this->data);
	}
}