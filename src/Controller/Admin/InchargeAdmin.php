<?php
class InchargeAdmin extends AdminController {

	public function Incharges($request, $response, $args) {
		//用户充值列表
		//post  /incharges
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('incharge')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('incharge')->orderBy('incharge_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		// $response->withJson($this->data);
		$this->display('Admin\Incharges');
	}
	public function NoIncharges($request, $response, $args) {
		//用户充值列表
		//post  /incharges
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('incharge')->where('incharge_status', 0)->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('incharge')->where('incharge_status', 0)->orderBy('incharge_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		// $response->withJson($this->data);
		$this->display('Admin\NoIncharges');
	}
	public function GetIncharge($request, $response, $args) {
		//单个充值信息
		//get  /incharge/id
		if($this->data['status']!='error'){
			if(!$this->data['data']= $this->ci->db->table('incharge')->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此充值信息。。';
			} else {
				$this->data['status']='success';
			}
		}
		$response->withJson($this->data);
	}
	public function CheckIncharge($request, $response, $args) {
		//单个充值信息
		//get  /incharge/id
		if($this->data['status']!='error'){
			if(!$incharge= $this->ci->db->table('incharge')->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此充值信息。。';
			} else {
				if(!$member= $this->ci->db->table('members')->where('id',$incharge->user_id)->first()) {
					$this->data['status']='error';
					$this->data['data']='暂时没有此用户。。。您再试试？';
				} else {
					$incharge_money=$incharge->incharge_money;
					$check_incharge['incharge_status']=1;
					$check_incharge['censor_id']=$this->loguser->id;
					$check_incharge['censor_name']=$this->loguser->admin_name;
					$check_incharge['censor_time']=time();
					if($resule = $this->ci->db->table('incharge')->where('id',$args['id'])->update($check_incharge)) {
						$member_update['flow_money']=$member->flow_money+$incharge_money;
						if($resule = $this->ci->db->table('members')->where('id',$incharge->user_id)->update($member_update)) {
							$this->data['status']='success';
							$this->data['data']='充值审核成功。。已经给用户账户打帐'.$incharge_money.'元';
						} else {
							$check_incharge['incharge_status']=0;
							$resule = $this->ci->db->table('incharge')->where('id',$args['id'])->update($check_incharge);
							$this->data['status']='error';
							$this->data['data']='用户数据错误，充值失败，重试一下吧？';
						}
					} else {
						$this->data['status']='error';
						$this->data['data']='充值失败，重试一下吧？';
					}
				}
			}
		}
		$response->withJson($this->data);
	}
}