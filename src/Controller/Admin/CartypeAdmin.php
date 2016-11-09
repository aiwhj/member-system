<?php
class CartypeAdmin extends AdminController {

	public function Cartypes($request, $response, $args) {
		//获取Admins列表
		//get
		if($this->data['status']!='error') {
			$this->data['status']='success';
			$count= $this->ci->db->table('cartype')->count();
			$page=$this->page($count,2);
			$this->data['data']= $this->ci->db->table('cartype')->orderBy('id','desc')->skip($page->firstRow)->take($page->listRows)->get();
			$this->data['page']=$page->show();
			$this->data['count']=$count;
		}
		$this->display('Admin\Cartypes');
	}
	public function Cartype($request, $response, $args) {
		//获取Admins列表
		//get
		$cartype=$request->getParsedBody();
		if($this->data['status']!='error') {
			$this->data['status']='success';
			$this->data['data']= $this->ci->db->table('cartype')->where('id',$cartype['id'])->first();
		}
		$response->withJson($this->data);
	}
	public function CartypeAdd($request, $response, $args) {
		//充值申请
		//post
		$cartype=$request->getParsedBody();
		if($this->data['status']!='error') {
			if(!$cartype['name']) {
				$this->data['status']='error';
				$this->data['data']='请输入车型。。';
			}
		}
		if($this->data['status']!='error') {
			if(!$cartype['money']) {
				$this->data['status']='error';
				$this->data['data']='请输入价格';
			}
		}
		if($this->data['status']!='error'){
			if($this->data['data'] = $this->ci->db->table('cartype')->insertGetId($cartype)) {
				$this->data['status']='success';
				$this->data['data']='发起车型成功，谢谢';
				$this->data['refer']=$this->ci->router->pathFor('Cartypes');
			} else {
				$this->data['status']='error';
				$this->data['data']='发起车型失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function CartypeUpdate($request, $response, $args) {
		//充值申请
		//post
		$cartype=$request->getParsedBody();
		if(!$oldcartype = $this->ci->db->table('cartype')->where('id',$args['id'])->first()) {
				$this->data['status']='error';
				$this->data['data']='还没有此车型信息，请核实一下您输入的公共ID';
		}
		if($this->data['status']!='error'){
			$cartype_update['title']=$cartype['title']?$cartype['title']:$oldcartype->title;
			$cartype_update['contants']=$cartype['contants']?$cartype['contants']:$oldcartype->contants;
			if($this->data['data'] = $this->ci->db->table('cartype')->where('id',$args['id'])->update($cartype_update)) {
				$this->data['status']='success';
				$this->data['data']='更新车型信息成功！';
			} else {
				$this->data['status']='error';
				$this->data['data']='更新车型信息失败，请重试！';
			}
		}
		$response->withJson($this->data);
	}
	public function CartypeDelete($request, $response, $args) {
		//会员删除
		//delete
		if($this->data['data'] = $this->ci->db->table('cartype')->where('id',$args['id'])->delete()) {
			$this->data['status']='success';
			$this->data['data']='删除车型成功！';
		} else {
			$this->data['status']='error';
			$this->data['data']='删除车型失败，请重试！';
		}
		$response->withJson($this->data);
	}
}