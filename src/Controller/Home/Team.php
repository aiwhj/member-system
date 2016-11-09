<?php
class Team extends LoggedController {
	
	protected $team;
	
	public function TeamOneInfo($request, $response, $args) {
		//充值申请
		//post
		$Team=$request->getParsedBody();
		if($this->data['status']!='error') {
			if(!$Team['id']) {
				$this->data['status']='error';
				$this->data['data']='错误';
			} else {
				$id=explode("\n",$Team['id'])[1];
				if(!is_numeric($id)) {
					$id=(int)str_replace('jc','',$id);
				}
				$this->data['status']='success';
				$boys=$this->ci->db->table('members')->where('super_id',$id)->orderBy('active_time','asc')->get()->toArray();
				$first=$this->ci->db->table('members')->where('id',$id)->orderBy('active_time','asc')->first();
				$i=1;
				if(!$boys) {
					$data['data']='暂无下级';
				}
				$grade=json_decode($this->ci->member_config->grade,1);
				$user_grade=($first->user_grade-1);
				foreach($boys as $boy) {
					if($i==1) {
						$data['data'].="左：".($this->GetNum($request, $response, $boy->id ,$grade[$user_grade],0)+1).' ';
					}
					if($i==2) {
						$data['data'].="中：".($this->GetNum($request, $response, $boy->id , $grade[$user_grade],0)+1).' ';
					}
					if($i==3) {
						$data['data'].="右：".($this->GetNum($request, $response, $boy->id , $grade[$user_grade],0)+1).' ';
					}
					$i++;
				}
				// $this->data['data']=$id[1];
			}
		}
		$response->withJson($data);
	}
	public function GetNum($request, $response, $id , $floor,$floor_num) {  //获取应该挂的父节点 (推荐人id，老大节点)
		if(($floor)>$floor_num) {
			$boys = $this->ci->db->table('members')->where('super_id',$id)->orderBy('active_time','asc')->get();
			$num = $this->ci->db->table('members')->where('super_id',$id)->orderBy('active_time','asc')->count();
			++$floor_num;
			foreach($boys as $boy) {
				$num+=$this->GetNum($request, $response, $boy->id , $floor,$floor_num);
			}
		}
		return $num;
	}
	public function TeamsTree($request, $response, $args) {
		//用户充值列表
		//post  /Teams
		$this->data['status']='success';
		$this->data['data']=$this->GetTeam($request, $response, $this->loguser->id);
		// $response->withJson($this->data);
		$this->display('TeamsTree');
	}
	public function TeamsList($request, $response, $args) {
		//用户充值列表
		//post  /Teams
		$this->data['status']='success';
		$this->data['data']=$this->GetTeamList($request, $response, $this->loguser->id);
		// $response->withJson($this->data);
		$this->display('TeamsList');
	}
	public function GetTeamsList($request, $response, $args) {
		//用户充值列表
		//post  /Teams
		$a1[]=$this->ci->db->table('members')->where('id',$args['id'])->select('id','real_name as name','user_grade')->first();
		// $this->data=array_merge($a1,$a2);
		$a1[0]->isParent=true;
		$a1[0]->name.='<span style="color:#c14444"> (ID编号：jc'.sprintf("%06d", $a1[0]->id).'，等级'.$a1[0]->user_grade.')</span>';
		$this->data=$a1;
		$response->withJson($this->data);
	}
	public function GetTeamsListAsync($request, $response, $args) {
		//用户充值列表
		//post  /Teams
		$id=$request->getParsedBody();
		$a2=$this->ci->db->table('members')->where('super_id',$id['id'])->select('id','real_name as name','user_grade')->get()->toArray();
		foreach($a2 as $key=>$whj) {
			$a2[$key]->isParent=true;
			$a2[$key]->name.='<span style="color:#c14444"> (ID编号：jc'.sprintf("%06d", $a2[$key]->id).'，等级'.$a2[$key]->user_grade.')</span>';
		}
		$this->data=$a2;
		$response->withJson($this->data);
	}
	public function TeamsListTree($request, $response, $args) {
		//用户充值列表
		//post  /Teams
		$this->data['data']=$this->loguser->id;
		$this->display('TeamsListTree');
	}
	public function GetTeam($request, $response, $id) {
		//单个充值信息
		//get  /Team/id
		$data['data']['info']= $this->ci->db->table('members')->where('id',$id)->select('id','real_name','user_grade')->orderBy('active_time','asc')->first();
		if($data['data']['next']['info']= $this->ci->db->table('members')->where('super_id',$id)->select('id','real_name','user_grade')->orderBy('active_time','asc')->get()) {
			foreach($data['data']['next']['info'] as $key=>$next) {
				$data['data']['next'][$key]['info']=$next;
				$data['data']['next'][$key]['next']['info']= $this->ci->db->table('members')->where('super_id',$next->id)->select('id','real_name','user_grade')->orderBy('active_time','asc')->get();
			}
		}
		return $data;
	}
	public function GetTeamList($request, $response, $id) {
		//单个充值信息
		//get  /Team/id
		$a1= $this->ci->db->table('members')->where('id',$id)->orderBy('active_time','asc')->get()->toArray();  //自己
		if($a2=$this->ci->db->table('members')->where('super_id',$id)->orderBy('active_time','asc')->get()->toArray()) {  //第二层
			$a1=array_merge($a1,$a2);
			foreach($a2 as $key=>$next) {
				$a3= $this->ci->db->table('members')->where('super_id',$next->id)->orderBy('active_time','asc')->get()->toArray(); //第三层
				if($a3) {
					$a1=array_merge($a1,$a3);
				}
			}
		}
		$data['info']=$a1;
		return $data;
	}
}