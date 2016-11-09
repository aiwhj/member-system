<?php
use Illuminate\Database\Capsule\Manager as DB;
class Order extends LoggedController {

	public function OrderInfo($request, $response, $id) {
		//获取单个Member信息
		//get
		if($this->data['status']!='error') {
			$date_limit=gettime('day',0);
			$incharge= $this->ci->db->table('incharge')
				->where('user_id','=', $this->loguser->id)
				->where('incharge_time','>=', $date_limit['start'])
				->sum('incharge_money');
			$reward= $this->ci->db->table('reward')
				->where('accept_id','=', $this->loguser->id)
				->where('time','>=', $date_limit['start'])
				->sum('flow_money');
			$freeze_money= $this->ci->db->table('reward')
				->where('accept_id','=', $this->loguser->id)
				->where('time','>=', $date_limit['start'])
				->sum('freeze_money');
			$recorded=$freeze_money+$reward+$incharge;
			$results = DB::select("SELECT COUNT(whj) as ww FROM(
				SELECT i.`real_name` AS `name`,i.`incharge_time` AS `time`,1 AS whj FROM `member_incharge` AS i WHERE i.`incharge_status`=1 AND i.`user_id`=".$this->loguser->id."
				UNION ALL
				SELECT c.`real_name` AS `name`,c.`cash_time` AS `time`,2 AS whj FROM `member_cash` AS c WHERE c.cash_status=1 AND c.user_id=".$this->loguser->id."
				UNION ALL
				SELECT u.`user_name` AS `name`,u.`time` AS `time`,3 AS whj FROM `member_upgrade` AS u WHERE u.user_id=".$this->loguser->id."
				UNION ALL 
				SELECT p.`accept_name` AS `name`,p.`time` AS `time`,3 AS whj FROM `member_prepay` AS p WHERE p.accept_id=".$this->loguser->id."
				UNION ALL 
				SELECT r.`accept_name` AS `name`,r.`time` AS `time`,4 AS whj FROM `member_reward` AS r WHERE r.accept_id=".$this->loguser->id."
				UNION ALL
				SELECT r.`accept_name` AS `name`,r.`time` AS `time`,4 AS whj FROM `member_reward` AS r WHERE r.accept_id=".$this->loguser->id."
				UNION ALL
				SELECT t.`accept_name` AS `name`,t.`transfer_time` AS `time`,5 AS whj FROM `member_transfer` AS t WHERE t.accept_id=".$this->loguser->id."
				UNION ALL
				SELECT t.`send_name` AS `name`,t.`transfer_time` AS `time`,6 AS whj FROM `member_transfer` AS t 
					WHERE t.send_id=".$this->loguser->id."
				) c
			");
			$count=$results[0]->ww;
			$page=$this->page($count,2);
			$this->data['data'] = DB::select("SELECT i.`id`,i.`real_name` AS `name`,i.`incharge_time` AS `time`,i.`incharge_money` AS money,1 AS whj FROM `member_incharge` AS i 
				WHERE i.`incharge_status`=1 AND i.`user_id`=".$this->loguser->id."
				UNION ALL
				SELECT c.`id`,c.`real_name` AS `name`,c.`cash_time` AS `time`,c.`cash_money` AS money,2 AS whj FROM `member_cash` AS c 
					WHERE c.cash_status=1 AND c.user_id=".$this->loguser->id."
				UNION ALL
				SELECT u.`id`,u.`user_name` AS `name`,u.`time` AS `time`,u.`grade_money` AS money,3 AS whj FROM `member_upgrade` AS u 
					WHERE u.user_id=".$this->loguser->id."
				UNION ALL 
				SELECT r.`id`,r.`accept_name` AS `name`,r.`time` AS `time`,r.`flow_money` AS money,4 AS whj FROM `member_reward` AS r
					WHERE r.accept_id=".$this->loguser->id."
				UNION ALL
				SELECT p.`id`,p.`accept_name` AS `name`,p.`time` AS `time`,p.`money` AS money,8 AS whj FROM `member_prepay` AS p
					WHERE p.accept_id=".$this->loguser->id."
				UNION ALL
				SELECT r.`id`,r.`accept_name` AS `name`,r.`time` AS `time`,r.`freeze_money` AS money,7 AS whj FROM `member_reward` AS r
					WHERE r.accept_id=".$this->loguser->id."
				UNION ALL
				SELECT t.`id`,t.`accept_name` AS `name`,t.`transfer_time` AS `time`,t.`transfer_money` AS money,5 AS whj FROM `member_transfer` AS t 
					WHERE t.accept_id=".$this->loguser->id."
				UNION ALL
				SELECT t.`id`,t.`send_name` AS `name`,t.`transfer_time` AS `time`,t.`transfer_money` AS money,6 AS whj FROM `member_transfer` AS t 
					WHERE t.send_id=".$this->loguser->id."
				ORDER BY TIME DESC LIMIT ".$page->firstRow.",".$page->listRows."
			");
			$this->data['page']=$page->show();
		}
		$this->data['recorded']=$recorded;
		// $this->data['get']=$get;
		// OrderInfo
		$this->display('OrderInfo');
		// $response->withJson($this->data);
	}
}