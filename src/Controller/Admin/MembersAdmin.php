<?php
class MembersAdmin extends AdminController {

	public function CheckMember($request, $response, $args) {
		if($this->data['status']!='error') {
			if(!$member = $this->ci->db->table('members')->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='暂时没有此用户信息。。';
			}
		}
		if($this->data['status']!='error') {
			if($member->user_grade>0) {
				$this->data['status']='error';
				$this->data['data']='此用户已经激活，无需再次激活。。';
			}
		}
		if($this->data['status']!='error') {
			if(!$member->recommender_id) {
				$this->data['status']='error';
				$this->data['data']='推荐人出错。。';
			} else {
				if(!$recommender=$this->ci->db->table('members')->where('id',$member->recommender_id)->first()) {
					$this->data['status']='error';
					$this->data['data']='没有此推荐人';
				}
/*				else {
					$grade=json_decode($this->ci->member_config->grade,1);
					$max_num=(pow(3,$grade[$recommender->user_grade-1]+1)-1)/2;
					if($recommender->foot_num>=$max_num) {
						$this->data['status']='error';
						$this->data['data']='您的等级不够，请您先升级再进行推荐。。';
					}
				}
*/
			}
		}
		if($this->data['status']!='error') {
			if(!$hang_id=$this->GetHang($request, $response, $member->recommender_id, $member->recommender_id,$member->recommender_id,0,0)) {
				$this->data['status']='error';
				$this->data['data']='推荐人出错。。22';
			}
		}
		if($this->data['status']!='error') {
			if(!$super=$this->ci->db->table('members')->where('id',$hang_id)->first()) {
				$this->data['status']='error';
				$this->data['data']='没有此推荐人。。'.$hang_id;
			}
		}
		if($this->data['status']!='error') {
			$user_update['pre_brother']=$this->GetPreId($request, $response, $hang_id,$args['id']);
			$user_update['next_brother']=$this->GetNextId($request, $response, $hang_id,$args['id']);
			$user_update['active_time']=time();
			$user_update['super_id']=$hang_id;
			$user_update['super_name']=$hang_id;
			$user_update['user_grade']=1;
			if(!$this->data['data'] = $this->ci->db->table('members')->where('id',$args['id'])->update($user_update)) {
				$this->data['status']='error';
				$this->data['data']='激活会员出错';
			} else {
				$update_super['next_num']=$super->next_num+1;
				if(!$this->ci->db->table('members')->where('id',$super->id)->update($update_super)) {
					$this->data['status']='error';
					$this->data['data']='激活会员出错';
					$user_update['pre_brother']=0;
					$user_update['next_brother']=0;
					$user_update['active_time']=0;
					$user_update['super_id']=0;
					$user_update['super_name']=0;
					$user_update['user_grade']=0;
					$this->data['data'] = $this->ci->db->table('members')->where('id',$args['id'])->update($user_update);
				} else {
					$this->ToReward($request, $response, $args['id']);
					$this->data['status']='success';
					$this->data['data']='激活会员成功';
				}
			}
		}
		$response->withJson($this->data);
	}
	public function ToReward($request, $response, $id) {
		$new_member=$this->ci->db->table('members')->where('id',$id)->select('super_id','id','real_name','recommender_id','recommender_name')->first();
		$reward=json_decode($this->ci->member_config->reward,1);
		$prepay=$this->ci->member_config->prepay;
		$grade=json_decode($this->ci->member_config->grade,1);
		for($i=0; $i<8; $i++) {   //最大八级。。
			$my=$this->ci->db->table('members')->where('id',$id)->select('super_id')->first();
			$super=$this->ci->db->table('members')->where('id',$my->super_id)->first(); //上级用户 看是否奖励
			$user_grade=$super->user_grade-1;  //上级用户等级
			if($i<$grade[$user_grade]) {   //判断上级用户可控制数
				$super_update['foot_num']=$super->foot_num+1;
				if($i<$this->ci->member_config->to_flow) {
					$super_update['freeze_money']=$super->freeze_money+$reward[$i]*0.5;
					$super_update['flow_money']=$super->flow_money+$reward[$i]*0.5;
					$reward_log['freeze_money']=$reward[$i]*0.5;
					$reward_log['flow_money']=$reward[$i]*0.5;
				} else {
					$super_update['flow_money']=$super->flow_money+$reward[$i];
					$super_update['freeze_money']=$super->freeze_money;
					$reward_log['flow_money']=$reward[$i];
				}
				$reward_log['from_id']=$new_member->id;
				$reward_log['from_name']=$new_member->real_name;
				$reward_log['recommender_id']=$new_member->recommender_id;
				$reward_log['recommender_name']=$new_member->recommender_name;
				$reward_log['accept_id']=$super->id;
				$reward_log['accept_name']=$super->real_name;
				$reward_log['type']='推荐会员';
				$ww=$i+1;
				$reward_log['floor']=$ww;
				$reward_log['time']=time();
				$this->ci->db->table('reward')->insertGetId($reward_log);  //写入奖励日志
				if($super->repay>0) {    //是否还有未支付的首付
					$prepay_update=$reward_log;
					if($super->repay>($super_update['freeze_money']*$prepay)) {    //看扣除是否超额
						$prepay_update['money']=($super_update['freeze_money']*$prepay);   //不超额则按冻结的百分比
					} else {
						$prepay_update['money']=$super->repay;    //超额则扣完
					}
					if($super_update['freeze_money']>=$prepay_update['money']) {  //用户资金不足的不用扣
						$super_update['freeze_money']-=$prepay_update['money'];  //上级会员的冻结资金扣除
						$super_update['repay']=$super->repay-$prepay_update['money'];  //上级会员的冻结资金扣除
						unset($prepay_update['flow_money']);
						unset($prepay_update['freeze_money']);  //复制过来的，去掉那两个字段
						$this->ci->db->table('prepay')->insertGetId($prepay_update);  //首付扣款日志
					}
				}
				$this->ci->db->table('members')->where('id',$super->id)->update($super_update);  //更新上级用户资金
				unset($super_update);
				unset($reward_log);
			}
			echo $my->super_id;
			if(!$my->super_id) {
				break;   //没有上级则退出循环
			} else {
				$id=$my->super_id;
			}
		}
	}
	public function GetHang($request, $response, $id ,$big_barther,$recommender_id, $next_brother_num,$next_floor_num) {  //获取应该挂的父节点 (推荐人id，老大节点)
		$member = $this->ci->db->table('members')->where('id',$id)->orderBy('active_time','asc')->first();
		if($member->next_num<3) {
			return $id;
		} else {
			if(($member->next_brother!=0)&&($recommender_id!=$member->id)&&($next_brother_num<(pow(3,($next_floor_num))-1))) {
				return $this->GetHang($request, $response, $member->next_brother, $big_barther,$recommender_id,++$next_brother_num,$next_floor_num);
			} else {
				$member_boy = $this->ci->db->table('members')->where('super_id', $big_barther)->select('id')->orderBy('active_time','asc')->first();
				return $this->GetHang($request, $response, $member_boy->id, $member_boy->id,$recommender_id,0,++$next_floor_num);
			}
		}
	}
	public function GetPreId($request, $response, $id, $thisid) { //获取前一个节点  （当前要加入的会员的父节点）
		$pre = $this->ci->db->table('members')->where('super_id', $id)->select('id')->orderBy('active_time','desc')->first();
		if(!$pre) {
			$pre_farther = $this->ci->db->table('members')->where('next_brother',$id)->select('id')->orderBy('active_time','desc')->first();
			$pre = $this->ci->db->table('members')->where('super_id',$pre_farther->id)->select('id')->orderBy('active_time','desc')->first();
			if(!$pre) {
				return 0; //没有上级
			} else {
				$pre_update['next_brother']=$thisid;
				$this->ci->db->table('members')->where('id',$pre->id)->update($pre_update);
				return $pre->id; //堂哥哥id
			}
		} else { //亲哥哥id
			$pre_update['next_brother']=$thisid;
			$this->ci->db->table('members')->where('id',$pre->id)->update($pre_update);
			return $pre->id;
		}
	}
	public function GetNextId($request, $response, $id, $thisid) { //获取next节点 (当前要加入的会员的父节点)
		$borther_count = $this->ci->db->table('members')->where('super_id',$id)->select('id')->orderBy('active_time','desc')->count();
		if($borther_count==2) {
			$father = $this->ci->db->table('members')->where('id',$id)->select('next_brother')->orderBy('active_time','desc')->first();
			if($father->next_brother) {
				$next = $this->ci->db->table('members')->where('super_id',$father->next_brother)->select('id')->orderBy('active_time','asc')->first();
				if($next->id) {
					$next_update['pre_brother']=$thisid;
					$this->ci->db->table('members')->where('id',$next->id)->update($next_update);
					return $next->id;
				} else {
					return 0;
				}
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}


	public function Members($request, $response, $args) {
		//获取Members列表
		//get
		$ser=$request->getQueryParams();
		if($this->data['status']!='error') {
			$query=$this->ci->db->table('members');
			if($ser['key']) {
				if($ser['ser']=='id') {
					if(!is_numeric($ser['key'])) {
						$ser['key']=(int)str_replace('jc','',$ser['key']);
					}
					$query=$query->where('id',$ser['key']);
				}
				if($ser['ser']=='phone') {
					$query=$query->where('phone',$ser['key']);
				}
			}
			$this->data['status']='success';
			$count= $query->count();
			$page=$this->page($count,2);
			$this->data['data']= $query->orderBy('reg_time','desc')->orderBy('active_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
			$this->data['cartype']= $this->ci->db->table('cartype')->get();
		}
		//$response->withJson($this->data);
		$this->display("Admin\Members");
	}
	public function Grade($request, $response, $args) {
		//获取Members列表
		//get
		$ser=$request->getQueryParams();
		if($this->data['status']!='error') {
			$query=$this->ci->db->table('upgrade');
			$this->data['status']='success';
			$count= $query->count();
			$page=$this->page($count,2);
			$this->data['data']= $query->orderBy('time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		//$response->withJson($this->data);
		$this->display("Admin\Grade");
	}
	public function MembersNCheck($request, $response, $args) {
		//获取Members列表
		//get
		$ser=$request->getQueryParams();
		if($this->data['status']!='error') {
			$query=$this->ci->db->table('members');
			if($ser['key']) {
				if($ser['ser']=='id') {
					$query=$query->where('id',$ser['key']);
				}
				if($ser['ser']=='phone') {
					$query=$query->where('phone',$ser['key']);
				}
			}
			$this->data['status']='success';
			$count= $query->where('user_grade', '<=', 0)->count();
			$page=$this->page($count,2);
			$this->data['data']= $query->where('user_grade', '<=', 0)->orderBy('reg_time','desc')->orderBy('active_time','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
			$this->data['cartype']= $this->ci->db->table('cartype')->get();
		}
		//$response->withJson($this->data);
		$this->display("Admin\MembersNCheck");
	}
	public function MembersTree($request, $response, $args) {
		//获取Members列表
		//get
		$first= $this->ci->db->table('members')->where('super_id',0)->first();
		$this->data['data']=$first->id;
		//$response->withJson($this->data);
		$this->display("Admin\MembersTree");
	}
	public function Member($request, $response, $id) {
		//获取单个Member信息
		//get
		$member=$request->getParsedBody();
		if($this->data['status']!='error') {
			$this->data['status']='success';
			if(!$this->data['data'] = $this->ci->db->table('members')->where('id',$member['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='暂时没有此用户信息。。';
			} else {
				$this->data['data']->yeji=$this->ci->db->table('members')->where('recommender_id',$member['id'])->count();
				$this->data['data']->id='jc'.sprintf("%06d", $this->data['data']->id);
				$this->data['data']->recommender_id='jc'.sprintf("%06d", $this->data['data']->recommender_id);
				$this->data['data']->cartypeid=$this->data['data']->cartype;
				if(is_numeric($this->data['data']->cartype)) {
					if($cartype=$this->ci->db->table('cartype')->where('id',$this->data['data']->cartype)->first()) {
						$this->data['data']->cartype=$cartype->name;
					} else {
						$this->data['data']->cartype='未填写';
					}
				}
			}
		}
		$response->withJson($this->data);
	}
	public function MemberUpdate($request, $response, $args) {
		//会员信息更新
		//put
		if(!$olduser = $this->ci->db->table('members')->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='还没有此会员信息';
		}
		$user=$request->getParsedBody();
		// print_r($user);
		if($this->data['status']!='error') {
			if($user['user_pass2']) {
				if(!$user['re_user_pass2']) {
					$this->data['status']='error';
					$this->data['data']='请输入确认的支付密码';
				} else {
					if($user['re_user_pass2']!=$user['user_pass2']) {
						$this->data['status']='error';
						$this->data['data']='您输入的确认支付密码不一致。。请重新输入';
					} else {
						$user['user_pass2']=md5(md5($user['re_user_pass2']).'whj');
						unset($user['re_user_pass2']);
						unset($user['old_user_pass2']);
						$user_update['user_pass2']=$user['user_pass2'];
					}
				}
			}
		}
		if($this->data['status']!='error') {
			if($user['user_pass']) {
				if(!$user['re_user_pass']) {
					$this->data['status']='error';
					$this->data['data']='请输入确认的登陆密码';
				} else {
					if($user['re_user_pass']!=$user['user_pass']) {
						$this->data['status']='error';
						$this->data['data']='您输入的确认登陆密码不一致。。请重新输入';
					} else {
						$user['user_pass']=md5(md5($user['re_user_pass']).'whj');
						unset($user['re_user_pass']);
						unset($user['old_user_pass']);
						$user_update['user_pass']=$user['user_pass'];
					}
				}
			}
		}
		if($this->data['status']!='error') {
			$user_update['car_money']=$user['car_money']?$user['car_money']:$olduser->car_money;
			$user_update['real_name']=$user['real_name']?$user['real_name']:$olduser->real_name;
			$user_update['email']=$user['email']?$user['email']:$olduser->email;
			$user_update['qq']=$user['qq']?$user['qq']:$olduser->qq;
			$user_update['idcard']=$user['idcard']?$user['idcard']:$olduser->idcard;
			$user_update['prepay']=$user['prepay']?$user['prepay']:$olduser->prepay;
			$user_update['repay']=$user['repay']?$user['repay']:$olduser->repay;
			// if(($olduser->repay===$olduser->prepay&&$olduser->prepay>0)||($olduser->prepay<=0&&$olduser->repay<=0)) {
				// $user_update['repay']=$user['prepay']?$user['prepay']:$olduser->repay;
			// }
			if($this->data['status']!='error') {
				if($user['cartype']<=0) {
					if(!$user['cartype2']) {
						$user_update['cartype']=$olduser->cartype;
					} else {
						$user_update['cartype']=$user['cartype2'];
					}
				} else {
					$user_update['cartype']=$user['cartype'];
				}
			}
			unset($user['cartype2']);
			$user_update['bank_id']=$user['bank_id']?$user['bank_id']:$olduser->bank_id;
			$user_update['bank']=$user['bank']?$user['bank']:$olduser->bank;
			$user_update['addres']=$user['addres']?$user['addres']:$olduser->addres;
		}
		// print_r($user_update);
		if($this->data['status']!='error'){
			if($this->data['data'] = $this->ci->db->table('members')->where('id',$args['id'])->update($user_update)) {
				$this->data['status']='success';
				$this->data['data']='更新会员信息成功！';
				$this->data['refer']=$this->ci->router->pathFor('Members');
			} else {
				$this->data['status']='error';
				$this->data['data']='更新会员信息失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function GetMemberList($request, $response, $args) {
		//用户充值列表
		//post  /Teams
		$a1[]=$this->ci->db->table('members')->where('id',$args['id'])->select('id','real_name as name','user_grade')->orderBy('active_time','asc')->first();
		// $this->data=array_merge($a1,$a2);
		$a1[0]->isParent=true;
		$a1[0]->name.='<span style="color:#c14444"> (ID编号：jc'.sprintf("%06d", $a1[0]->id).'，等级'.$a1[0]->user_grade.')</span>';
		$this->data=$a1;
		$response->withJson($this->data);
	}
	public function GetMemberListAsync($request, $response, $args) {
		//用户充值列表
		//post  /Teams
		$id=$request->getParsedBody();
		$a2=$this->ci->db->table('members')->where('super_id',$id['id'])->select('id','real_name as name','user_grade')->orderBy('active_time','asc')->get()->toArray();
		foreach($a2 as $key=>$whj) {
			$a2[$key]->isParent=true;
			$a2[$key]->name.='<span style="color:#c14444"> (ID编号：jc'.sprintf("%06d", $a2[$key]->id).'，等级'.$a2[$key]->user_grade.')</span>';
		}
		$this->data=$a2;
		$response->withJson($this->data);
	}
	public function Reward($request, $response, $id) {
		//获取单个Admin信息
		//get
		if($this->data['status']!='error'){
			$this->data['status']='success';
			$count= $this->ci->db->table('reward')->count();
			$page=$this->page($count,10);
			$this->data['data']= $this->ci->db->table('reward')->skip($page->firstRow)->take($page->listRows)->orderBy('time','desc')->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		$this->display('Admin\Reward');
	}
	public function MembersInfoDelete($request, $response, $args) {
		//会员删除
		//delete
		$response->withJson($this->data);
	}
}