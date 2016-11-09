<?php
class Grade extends LoggedController {
	protected $foot_num;
	protected $freeze_money;
	protected $flow_money;
	protected $repay;
	public function Graded($request, $response, $args) {
		//升级
		//get
		if($this->data['status']!='error') {
			$member= $this->ci->db->table('members')->where('id',$this->loguser->id)->first();
			$grade=json_decode($this->ci->member_config->grade,1);
			$grademoney=json_decode($this->ci->member_config->grademoney,1);
			$max_num=(pow(3,$grade[$member->user_grade-1]+1)-1)/2-1;
			if($member->foot_num<$max_num) {
				$this->data['status']='error';
				$this->data['data']='您的推荐的人数不够，请您先推荐再升级。。';
			} else {
				if($member->freeze_money<$grademoney[$member->user_grade-1]) {
					$this->data['status']='error';
					$this->data['data']='您的余额不足，不允许升级。。';
				} else {
					if($member->user_grade>=3) {
						$this->data['status']='error';
						$this->data['data']='您已经达到3级，不允许再升级了';
					} else {
						$this->freeze_money=$member->freeze_money;
						$this->repay=$member->repay;
						$this->flow_money=$member->flow_money;
						$this->foot_num=$member->foot_num;
						$this->ToGrade($request, $response, $this->loguser->id ,1 ,$grade[$member->user_grade-1] ,$grade[$member->user_grade], $this->loguser->id); //遍历升级
						//当前用户ID，层数：1，当前等级控制层数，下级等级控制层数
						$member_update['freeze_money']=$this->freeze_money-$grademoney[$member->user_grade-1];
						$member_update['flow_money']=$this->flow_money;
						$member_update['repay']=$this->repay;
						$member_update['foot_num']=$this->foot_num;
						$member_update['user_grade']=$member->user_grade+1;
						$upgrade_log['user_id']=$this->loguser->id;
						$upgrade_log['user_name']=$this->loguser->real_name;
						$upgrade_log['grade_money']=$grademoney[$member->user_grade-1];
						$upgrade_log['old_grade']=$member->user_grade;
						$upgrade_log['new_grade']=$member_update['user_grade'];
						$upgrade_log['grade_num']=$member->foot_num;
						$upgrade_log['time']=time();
						$this->ci->db->table('members')->where('id',$this->loguser->id)->update($member_update);
						
						$this->ci->db->table('upgrade')->insertGetId($upgrade_log);
						$this->data['status']='success';
						$this->data['data']='升级成功！谢谢。。';
					}
				}
			}
			$this->jump($this->ci->router->pathFor('PersonalInfo'));
		}
	}
	public function ToGrade($request, $response, $id ,$floor ,$pre ,$next , $grader ) {
		//升级
		//get
		//当前用户ID，层数：1，当前等级控制层数，下级等级控制层数
		if($floor<=$next) { //下级控制内的会员
			$nexts= $this->ci->db->table('members')->where('super_id',$id)->get();
			$new_member= $this->ci->db->table('members')->where('id',$id)->first();
			$reward=json_decode($this->ci->member_config->reward,1);
			$prepay=$this->ci->member_config->prepay;
			$floor_this=$floor;
			$floor++;
			foreach($nexts as $next_boy) {
				if($floor_this>$pre) { //是不是升级后需要的
					if($floor_this<=$this->ci->member_config->to_flow) {
						$this->freeze_money=$this->freeze_money+$reward[$floor_this-1]*0.5;
						$this->flow_money=$this->flow_money+$reward[$floor_this-1]*0.5;
						$reward_log['freeze_money']=$reward[$floor_this-1]*0.5;
						$reward_log['flow_money']=$reward[$floor_this-1]*0.5;
					} else {
						$this->flow_money=$this->flow_money+$reward[$floor_this-1];
						$reward_log['flow_money']=$reward[$floor_this-1];
					}
					$reward_log['from_id']=$next_boy->id;
					$reward_log['from_name']=$next_boy->real_name;
					$reward_log['recommender_id']=$next_boy->recommender_id;
					$reward_log['recommender_name']=$next_boy->recommender_name;
					$reward_log['accept_id']=$this->loguser->id;
					$reward_log['accept_name']=$this->loguser->real_name;
					$reward_log['type']='会员升级';
					$ww=$floor+1;
					$reward_log['floor']=$ww;
					$reward_log['time']=time();
					$this->ci->db->table('reward')->insertGetId($reward_log);
					if($this->repay>0) {    //是否还有未支付的首付
						$prepay_update=$reward_log;
						if($this->repay>($this->freeze_money*$prepay)) {    //看扣除是否超额
							$prepay_update['money']=$this->freeze_money*$prepay;   //不超额则按冻结的百分比
						} else {
							$prepay_update['money']=$this->repay;    //超额则扣完
						}
						if($this->freeze_money>=$prepay_update['money']) {  //用户资金不足的不用扣
							$this->freeze_money-=$prepay_update['money'];  //上级会员的冻结资金扣除
							$this->repay-=$prepay_update['money'];
							unset($prepay_update['flow_money']);
							unset($prepay_update['freeze_money']);  //复制过来的，去掉那两个字段
							$this->ci->db->table('prepay')->insertGetId($prepay_update);  //首付扣款日志
						}
					}
					
					$this->foot_num++;
				}
				$this->ToGrade($request, $response, $next_boy->id ,$floor ,$pre ,$next , $grader);
			}
		}
	}
}