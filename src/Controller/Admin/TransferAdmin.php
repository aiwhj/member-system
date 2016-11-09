<?php
class TransferAdmin extends AdminController {
	
	public function Transfers($request, $response, $args) {
		//用户转账列表
		//post  /transfers
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('transfer')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('transfer')->orderBy('transfer_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		// $response->withJson($this->data);
		$this->display('Admin\Transfers');
	}
	public function GetTransfer($request, $response, $args) {
		//单个转账信息
		//get  /transfer/id
		if($this->data['status']!='error'){
			if(!$this->data['data']= $this->ci->db->table('transfer')->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此转账信息。。';
			} else {
				$this->data['status']='success';
			}
		}
		$response->withJson($this->data);
	}
}