<?php
use Illuminate\Database\Capsule\Manager as DB;
class OrderAdmin extends AdminController {

	public function OrderInfo($request, $response, $id) {
		//获取单个Member信息
		//get
		if($this->data['status']!='error') {
			
			$incharge= $this->ci->db->table('incharge')
				->sum('incharge_money');
			$upgrade= $this->ci->db->table('upgrade')
				->sum('grade_money');
			$cash= $this->ci->db->table('cash')
				->sum('cash_money');
			$freeze_money= $this->ci->db->table('members')
				->sum('freeze_money');
			$flow_money= $this->ci->db->table('members')
				->sum('flow_money');
			$this->data['incharge']=$incharge;
			$this->data['upgrade']=$upgrade;
			$this->data['cash']=$cash;
			$this->data['reward']=$freeze_money+$flow_money;
			$results = DB::select("SELECT COUNT(whj) as ww FROM(
				SELECT i.`real_name` AS `name`,i.`incharge_time` AS `time`,1 AS whj FROM `member_incharge` AS i WHERE i.`incharge_status`=1 
				UNION ALL
				SELECT c.`real_name` AS `name`,c.`cash_time` AS `time`,2 AS whj FROM `member_cash` AS c WHERE c.cash_status=1 
				UNION ALL
				SELECT u.`user_name` AS `name`,u.`time` AS `time`,3 AS whj FROM `member_upgrade` AS u 
				UNION ALL
				SELECT r.`accept_name` AS `name`,r.`time` AS `time`,4 AS whj FROM `member_reward` AS r
				UNION ALL
				SELECT p.`accept_name` AS `name`,p.`time` AS `time`,4 AS whj FROM `member_prepay` AS p
				UNION ALL
				SELECT r.`accept_name` AS `name`,r.`time` AS `time`,7 AS whj FROM `member_reward` AS r
				UNION ALL
				SELECT t.`accept_name` AS `name`,t.`transfer_time` AS `time`,5 AS whj FROM `member_transfer` AS t 
				) c
			");
			$count=$results[0]->ww;
			$page=$this->page($count,2);
			$this->data['data'] = DB::select("SELECT i.`id`,i.`real_name` AS `name`,i.`incharge_time` AS `time`,i.`incharge_money` AS money,1 AS whj FROM `member_incharge` AS i 
				WHERE i.`incharge_status`=1 
				UNION ALL
				SELECT c.`id`,c.`real_name` AS `name`,c.`cash_time` AS `time`,c.`cash_money` AS money,2 AS whj FROM `member_cash` AS c 
					WHERE c.cash_status=1
				UNION ALL
				SELECT u.`id`,u.`user_name` AS `name`,u.`time` AS `time`,u.`grade_money` AS money,3 AS whj FROM `member_upgrade` AS u 
				UNION ALL 
				SELECT r.`id`,r.`accept_name` AS `name`,r.`time` AS `time`,r.`flow_money` AS money,4 AS whj FROM `member_reward` AS r 
				UNION ALL
				SELECT p.`id`,p.`accept_name` AS `name`,p.`time` AS `time`,p.`money` AS money,8 AS whj FROM `member_prepay` AS p 
				UNION ALL
				SELECT r.`id`,r.`accept_name` AS `name`,r.`time` AS `time`,r.`freeze_money` AS money,7 AS whj FROM `member_reward` AS r 
				UNION ALL
				SELECT t.`id`,t.`accept_name` AS `name`,t.`transfer_time` AS `time`,t.`transfer_money` AS money,5 AS whj FROM `member_transfer` AS t 
				UNION ALL
				SELECT t.`id`,t.`send_name` AS `name`,t.`transfer_time` AS `time`,t.`transfer_money` AS money,6 AS whj FROM `member_transfer` AS t 
				ORDER BY TIME DESC LIMIT ".$page->firstRow.",".$page->listRows."
			");
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		// $this->data['get']=$get;
		// OrderInfo
		$this->display('Admin\OrderInfo');
		// $response->withJson($this->data);
	}
}