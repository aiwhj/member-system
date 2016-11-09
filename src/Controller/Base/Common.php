<?php
class Common {
	protected $ci;
	public function __construct($ci) {
       $this->ci = $ci;
	}
	public function Captcha($request, $response, $args) {
		//获取Members列表
		//get
		$builder = new Gregwar\Captcha\CaptchaBuilder;
        //可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();
		$this->ci->session->set('captcha', $phrase);
        //把内容存入session
        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
	}
	public function GetRegions($request, $response, $id) {
		//获取Members列表
		//get
		$this_region = $this->ci->db->table('region_conf')->where('id',$id['id'])->first();
		if($this_region->region_level==2)  {
			$shi = $this->ci->db->table('region_conf')->where('pid',$id['id'])->select('id','name')->get();
			foreach($shi as $xian) {
				$str_shi.='<option value ="'.$xian->id.'">'.$xian->name.'</option>';
			}
			$this->data['data']['regions']['shi']=$str_shi;
			$first = $this->ci->db->table('region_conf')->where('pid',$id['id'])->first();
			$xians = $this->ci->db->table('region_conf')->where('pid',$first->id)->select('id','name')->get();
			foreach($xians as $xian) {
				$str.='<option value ="'.$xian->id.'">'.$xian->name.'</option>';
			}
			$this->data['data']['regions']['xian']=$str;
		} else {
			$xians = $this->ci->db->table('region_conf')->where('pid',$id['id'])->select('id','name')->get();
			foreach($xians as $xian) {
				$str.='<option value ="'.$xian->id.'">'.$xian->name.'</option>';
			}
			$this->data['data']['regions']['xian']=$str;
		}
		$response->withJson($this->data);
	}
	public function GetRegionName($request, $response, $id) {
		//获取Members列表
		//get
		$this_region = $this->ci->db->table('region_conf')->where('id',$id['id'])->first();
		if($this_region) {
			$this->data['data']=$this_region->name;
			$this->data['status']='success';
		} else {
			$this->data['data']='未知';
			$this->data['status']='success';
		}
		$response->withJson($this->data);
	}
	public function RegionName($request, $response, $id) {
		//获取Members列表
		//get
		$this_region = $this->ci->db->table('region_conf')->where('id',$id['id'])->first();
		if($this_region) {
			return $this_region->name;
		} else {
			return '未知';
		}
	}
	public function Verity($request, $response, $id) {
		//获取Members列表
		//get
		$phone=$request->getParsedBody();
		if(!$phone) {
			$user_info = $this->ci->db->table('members')->where('id',$this->ci->session->get('user_id'))->select('phone')->first();
			if($user_info->phone) {
				$phone['phone']=$user_info->phone;
			} else {
				$this->data['status']='error';
				$this->data['data']='请输入手机号22'.$user_info->phone.$this->loguser->id;
			}
		}
		if($this->data['status']!='error'){
			if(isMobile($phone['phone'])){
				$sendSms = $this->ci->alidayu;
				$str=rand_num();
				$sendSms->setSmsParam(["number" => $str]);
				$sendSms->setRecNum($phone['phone']);
				$result = $sendSms->send();
				if (!$result) {
					$this->data['status']='error';
					$this->data['data']=$sendSms->getErrorMessage().'错误代码：'.$sendSms->getErrorCode();
				} else {
					$this->ci->session->set('phone_verity', $str);
					$this->data['status']='success';
					$this->data['data']='发送成功！请注意短信通知';
				}
			} else {
				$this->data['status']='error';
				$this->data['data']='您输入手机号码格式错误';
			}
		}
		$response->withJson($this->data);
	}
}