<?php

use Illuminate\Database\Capsule\Manager as DB;
class Tests extends BaseController {
	public function Test($request, $response, $args) {
		// print_r($request->getUri()->getPath());
		// echo md5(md5('whj').'whjsys');
		// $this->ci->redis->set('foo', 'bar');
		// $value =$this->ci->redis->get('*');
		// $this->ci->redis->lpush("tutorial-list", "Redis");
		// $this->ci->redis->lpush("tutorial-list", "Mongodb");
		// $this->ci->redis->lpush("tutorial-list", "Mysql");
		// 获取存储的数据并输出
		// $arList = $this->ci->redis->lrange("tutorial-list", 0 ,5);
		// echo "Stored string in redis";
		
 
// print_r(get_class_methods($this->ci->router));
// print_r(get_class_vars($this->ci->router));
 
 
// $ref = new ReflectionClass($this->ci->router);
 
// print_r($ref->getMethods());
// print_r($ref->getProperties());
		 // $arList = $this->ci->redis->keys('*');
		 // $arList = $this->ci->redis->del('tutorial-list');
		 // $arList = $this->ci->redis->keys('*');
		  // $reward=json_decode($this->ci->member_config->reward,1);
				// $grade=json_decode($this->ci->member_config->grade,1);
		// print_r($reward);
		// print_r($grade);
		// echo $request->getUri();
		// echo $this->ci->router->pathFor('MembersLogin');
		
		// echo date('Y-m-d H:i:s',time());
		// echo date('Y-m-d H:i:s',gettime('day',0)['start']);
		// echo date('Y-m-d H:i:s',gettime('day',0)['end']);
		// echo date('Y-m-d H:i:s',gettime('month',0)['start']);
		// echo date('Y-m-d H:i:s',gettime('month',0)['end']);
		
		// $results = DB::select("SELECT i.`real_name` AS `name`,i.`incharge_time` AS `time`,i.`incharge_money` AS money,1 AS whj FROM `member_incharge` AS i 
	// WHERE i.`incharge_status`=1 AND i.`user_id`=4
// UNION ALL

// SELECT c.`real_name` AS `name`,c.`cash_time` AS `time`,c.`cash_money` AS money,2 AS whj FROM `member_cash` AS c 
	// WHERE c.cash_status=1 AND c.user_id=4
// UNION ALL

// SELECT u.`user_name` AS `name`,u.`time` AS `time`,u.`grade_money` AS money,3 AS whj FROM `member_upgrade` AS u 
	// WHERE u.user_id=4
// UNION ALL 

// SELECT r.`accept_name` AS `name`,r.`time` AS `time`,r.`flow_money` AS money,4 AS whj FROM `member_reward` AS r 
	// WHERE r.accept_id=4
// UNION ALL

// SELECT t.`accept_name` AS `name`,t.`transfer_time` AS `time`,t.`transfer_money` AS money,5 AS whj FROM `member_transfer` AS t 
	// WHERE t.accept_id=4
	
// ORDER BY TIME DESC LIMIT 2,2
		// ");
		// print_r($results);
		//echo $results;
		// print_r($results[0]->ww);
		// $ww="jc000004";
		// echo (int)str_replace('jc','',$ww);
		
		//$response->withJson($results);
		// echo $value;
		// $this->ci->violin->addRuleMessage('required', '密码格式{field}输入错误');

		// $this->ci->violin->addRule('whj', function($value, $input, $args) {
			// return $value > 6;
		// });
		// $this->ci->violin->validate([
			// '密码' => [0, 'required']
		// ]);
		// if($this->ci->violin->passes()) {
			// var_dump($this->ci->violin->errors());
		// } else {
			// var_dump($this->ci->violin->errors());
		// }
		// $sendSms = $this->ci->alidayu;
		// $sendSms->setSmsParam(["number" => "123321"]);
		// $sendSms->setRecNum("15238071275");
		// $result = $sendSms->send();
		// if (!$result) {
			// echo $sendSms->getErrorCode();
			// echo $sendSms->getErrorMessage();
		// }
		// print_r($result);
		echo phpinfo();
		// echo $this->ci->member_config->prepay;
	}
}