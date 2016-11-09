<?php
class CashAdmin extends AdminController {

	public function Cashs($request, $response, $args) {
		//用户提现列表
		//post  /cashs
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('cash')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('cash')->orderBy('cash_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		// $response->withJson($this->data);
		$this->display('Admin\Cashs');
	}
	public function NoCashs($request, $response, $args) {
		//用户提现列表
		//post  /cashs
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('cash')->where('cash_status', 0)->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('cash')->where('cash_status', 0)->orderBy('cash_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		// $response->withJson($this->data);
		$this->display('Admin\NoCashs');
	}
	public function GetCash($request, $response, $args) {
		//单个提现信息
		//get  /cash/id
		if($this->data['status']!='error'){
			if(!$this->data['data']= $this->ci->db->table('cash')->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此提现信息。。';
			} else {
				$this->data['status']='success';
			}
		}
		$response->withJson($this->data);
	}
	public function CheckCash($request, $response, $args) {
		//单个提现信息
		//get  /cash/id
		if($this->data['status']!='error'){
			if(!$cash= $this->ci->db->table('cash')->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此提现信息。。';
			} else {
				if(!$member= $this->ci->db->table('members')->where('id',$cash->user_id)->first()) {
					$this->data['status']='error';
					$this->data['data']='暂时没有此用户。。。您再试试？';
				} else {
					$cash_money=$cash->cash_money+$cash->cash_process;
					if($member->flow_money<$cash_money) {
						$this->data['status']='error';
						$this->data['data']='此用户流动资金不足，不允许提现。。';
					} else {
						$check_cash['cash_status']=1;
						$check_cash['censor_id']=$this->loguser->id;
						$check_cash['censor_name']=$this->loguser->admin_name;
						$check_cash['censor_time']=time();
						if($resule = $this->ci->db->table('cash')->where('id',$args['id'])->update($check_cash)) {
							$member_update['flow_money']=$member->flow_money-$cash_money;
							if($resule = $this->ci->db->table('members')->where('id',$cash->user_id)->update($member_update)) {
								$this->data['status']='success';
								$this->data['data']='提现审核成功。。成功扣除该用户账户'.$cash_money.'元';
							} else {
								$check_cash['cash_status']=0;
								$resule = $this->ci->db->table('cash')->where('id',$args['id'])->update($check_cash);
								$this->data['status']='error';
								$this->data['data']='用户数据错误，提现失败，重试一下吧？';
							}
						} else {
							$this->data['status']='error';
							$this->data['data']='提现失败，重试一下吧？';
						}
					}
				}
			}
		}
		$response->withJson($this->data);
	}
}