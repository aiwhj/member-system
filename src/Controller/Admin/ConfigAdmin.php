<?php
class ConfigAdmin extends AdminController {
	public function ConfigIndex($request, $response, $args) {
		//用户提现列表
		//post  /cashs
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$this->data['data']= $this->ci->db->table('config')->where('id',1)->first();
			$this->data['data']->reward=json_decode($this->data['data']->reward,1);
		}
		$this->display('Admin\ConfigIndex');
	}
	public function CashConfig($request, $response, $args) {
		//获取提现申请
		//post
		$cash=$request->getParsedBody();
		if($this->data['status']!='error') {
			$cash_update['cash_fee']=$cash['cash_fee']?$cash['cash_fee']:0.10;
			$cash_update['cash_max']=$cash['cash_max']?$cash['cash_max']:200000;
			$cash_update['cash_time']=$cash['cash_time']?$cash['cash_time']:'3到5个工作日';
			if($this->data['data'] = $this->ci->db->table('config')->where('id',1)->update($cash_update)) {
				$this->data['status']='success';
				$this->data['data']='提现配置设置成功！谢谢';
				$log['admin_id']=$this->loguser->id;
				$log['admin_name']=$this->loguser->admin_name;
				$log['type']='cash';
				$log['time']=time();
				$log['note']='提现配置设置成功！';
				$this->ci->db->table('confchange_log')->insertGetId($log);
			} else {
				$this->data['status']='error';
				$this->data['data']='提现配置设置失败！您再试试？';
			}
		}
		$response->withJson($this->data);
	}
	public function PrepayConfig($request, $response, $args) {
		//获取提现申请
		//post
		$prepay=$request->getParsedBody();
		if($this->data['status']!='error') {
			$prepay_update['prepay']=$prepay['prepay']?$prepay['prepay']:0.20;
			if($this->data['data'] = $this->ci->db->table('config')->where('id',1)->update($prepay_update)) {
				$this->data['status']='success';
				$this->data['data']='首付比例配置设置成功！谢谢';
				$log['admin_id']=$this->loguser->id;
				$log['admin_name']=$this->loguser->admin_name;
				$log['type']='prepay';
				$log['time']=time();
				$log['note']='首付比例配置设置成功！';
				$this->ci->db->table('confchange_log')->insertGetId($log);
			} else {
				$this->data['status']='error';
				$this->data['data']='首付比例配置设置失败！您再试试？';
			}
		}
		$response->withJson($this->data);
	}
	public function FlowConfig($request, $response, $args) {
		//获取提现申请
		//post
		$flow=$request->getParsedBody();
		if($this->data['status']!='error') {
			$flow_update['to_flow']=$flow['flow']?$flow['flow']:4;
			if($this->data['data'] = $this->ci->db->table('config')->where('id',1)->update($flow_update)) {
				$this->data['status']='success';
				$this->data['data']='流动资金返回层数配置设置成功！谢谢';
				$log['admin_id']=$this->loguser->id;
				$log['admin_name']=$this->loguser->admin_name;
				$log['type']='flow';
				$log['time']=time();
				$log['note']='流动资金返回层数配置设置成功！';
				$this->ci->db->table('confchange_log')->insertGetId($log);
			} else {
				$this->data['status']='error';
				$this->data['data']='流动资金返回层数配置设置失败！您再试试？';
			}
		}
		$response->withJson($this->data);
	}
	public function TransferConfig($request, $response, $args) {
		//获取提现申请
		//post
		$transfer=$request->getParsedBody();
		if($this->data['status']!='error') {
			$transfer_update['transfer_limit']=$transfer['transfer_limit']?$transfer['transfer_limit']:3600;
			if($this->data['data'] = $this->ci->db->table('config')->where('id',1)->update($transfer_update)) {
				$this->data['status']='success';
				$this->data['data']='同一个账户间相互转账时间限制设置成功！谢谢';
				$log['admin_id']=$this->loguser->id;
				$log['admin_name']=$this->loguser->admin_name;
				$log['type']='transfer';
				$log['time']=time();
				$log['note']='同一个账户间相互转账时间限制设置成功！';
				$this->ci->db->table('confchange_log')->insertGetId($log);
			} else {
				$this->data['status']='error';
				$this->data['data']='同一个账户间相互转账时间限制设置失败！您再试试？';
			}
		}
		$response->withJson($this->data);
	}
	public function GradeConfig($request, $response, $args) {
		$grade=$request->getParsedBody();
		if($this->data['status']!='error') {
			$grade_update[]=$grade['gradea']?(int)($grade['gradea']):2;
			$grade_update[]=$grade['gradeb']?(int)($grade['gradeb']):5;
			$grade_update[]=$grade['gradec']?(int)($grade['gradec']):8;	
			$grade_up['grade']=json_encode($grade_update);
			if($this->data['data'] = $this->ci->db->table('config')->where('id',1)->update($grade_up)) {
				$this->data['status']='success';
				$this->data['data']='会员升级配置设置成功！谢谢';
				$log['admin_id']=$this->loguser->id;
				$log['admin_name']=$this->loguser->admin_name;
				$log['type']='grade';
				$log['time']=time();
				$log['note']='会员升级配置设置成功！';
				$this->ci->db->table('confchange_log')->insertGetId($log);
			} else {
				$this->data['status']='error';
				$this->data['data']='会员升级配置设置失败！您再试试？';
			}
		}
		$response->withJson($this->data);
	}

	public function RewardConfig($request, $response, $args) {
		//获取提现申请
		//post
		$reward=$request->getParsedBody();
		if($this->data['status']!='error') {
			$reward_update[]=$reward['rewarda']?(int)($reward['rewarda']):4000;
			$reward_update[]=$reward['rewardb']?(int)($reward['rewardb']):6000;
			$reward_update[]=$reward['rewardc']?(int)($reward['rewardc']):3000;
			$reward_update[]=$reward['rewardd']?(int)($reward['rewardd']):4000;
			$reward_update[]=$reward['rewarde']?(int)($reward['rewarde']):5000;
			$reward_update[]=$reward['rewardf']?(int)($reward['rewardf']):6000;
			$reward_update[]=$reward['rewardg']?(int)($reward['rewardg']):7000;
			$reward_update[]=$reward['rewardh']?(int)($reward['rewardh']):8000;	
			$reward_up['reward']=json_encode($reward_update);
			if($this->data['data'] = $this->ci->db->table('config')->where('id',1)->update($reward_up)) {
				$this->data['status']='success';
				$this->data['data']='提现配置设置成功！谢谢';
				$log['admin_id']=$this->loguser->id;
				$log['admin_name']=$this->loguser->admin_name;
				$log['type']='reward';
				$log['time']=time();
				$log['note']='提现配置设置成功！';
				$this->ci->db->table('confchange_log')->insertGetId($log);
			} else {
				$this->data['status']='error';
				$this->data['data']='提现配置设置失败！您再试试？';
			}
		}
		$response->withJson($this->data);
	}
	public function Cashs($request, $response, $args) {
		//用户提现列表
		//post  /cashs
				echo $this->ci->session->get('admin_name');
		echo $this->ci->session->get('admin_id');
		print_r($this->data);
		// if($this->data['status']!='error'){
			// $this->data['status']='success';
			// $this->data['data']= $this->ci->db->table('cash')->where('user_id',$this->loguser->id)->get();
		// }
		// $response->withJson($this->data);
	}
	public function GetCash($request, $response, $args) {
		//单个提现信息
		//get  /cash/id
		if($this->data['status']!='error'){
			if(!$this->data['data']= $this->ci->db->table('cash')->where('user_id',$this->loguser->id)->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此提现信息。。';
			} else {
				$this->data['status']='success';
			}
		}
		$response->withJson($this->data);
	}
}