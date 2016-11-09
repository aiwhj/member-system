<?php
class MatterAdmin extends AdminController {
	
	public function Matter($request, $response, $args) {
		//用户转账列表
		//post  /transfers
		if($this->data['status']!='error'){
			$this->data['data']['grade_money']=$this->ci->db->table('upgrade')->sum('grade_money');
			$this->data['data']['prepay']=$this->ci->db->table('prepay')->sum('money');
		}
		if($this->data['status']!='error'){
			$this->data['data']['members']['all']=$this->ci->db->table('members')->count('id');
			$this->data['data']['members']['today']=$this->ci->db->table('members')->where('active_time','>=',gettime('day',0)['start'])->count('id');
			$this->data['data']['members']['yesterday']=$this->ci->db->table('members')->where('active_time','>=',gettime('day',1)['start'])->where('active_time','<=',gettime('day',1)['end'])->count('id');
			$this->data['data']['members']['month']=$this->ci->db->table('members')->where('active_time','>=',gettime('month',0)['start'])->count('id');
			$this->data['data']['members']['nocheck']=$this->ci->db->table('members')->where('user_grade','<=',0)->count('id');
			$this->data['data']['cash']['nocheck']=$this->ci->db->table('cash')->where('cash_status','<=',0)->count('id');
			$this->data['data']['incharge']['nocheck']=$this->ci->db->table('incharge')->where('incharge_status','<=',0)->count('id');
		}
		// $response->withJson($this->data);
		$this->display("Admin\Matter");
	}
}