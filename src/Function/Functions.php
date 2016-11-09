<?php
	function rand_num($n=5){
	$str = "0123456789";
		$len = strlen($str)-1;
		$s='';
			for($i=0 ; $i<$n; $i++){
				$s .= $str[rand(0,$len)];
			}
			return $s;
	}
	function isMobile($mobile) {
		if (!is_numeric($mobile)) {
			return false;
		}
		return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
	}
function get_http()
{
	return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
}
function get_domain()
{
	/* 协议 */
	$protocol = get_http();

	/* 域名或IP地址 */
	if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	{
		$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
	}
	elseif (isset($_SERVER['HTTP_HOST']))
	{
		$host = $_SERVER['HTTP_HOST'];
	}
	else
	{
		/* 端口 */
		if (isset($_SERVER['SERVER_PORT']))
		{
			$port = ':' . $_SERVER['SERVER_PORT'];

			if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
			{
				$port = '';
			}
		}
		else
		{
			$port = '';
		}

		if (isset($_SERVER['SERVER_NAME']))
		{
			$host = $_SERVER['SERVER_NAME'] . $port;
		}
		elseif (isset($_SERVER['SERVER_ADDR']))
		{
			$host = $_SERVER['SERVER_ADDR'] . $port;
		}
	}

	return $protocol . $host;
}
function get_host()
{


	/* 域名或IP地址 */
	if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	{
		$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
	}
	elseif (isset($_SERVER['HTTP_HOST']))
	{
		$host = $_SERVER['HTTP_HOST'];
	}
	else
	{
		if (isset($_SERVER['SERVER_NAME']))
		{
			$host = $_SERVER['SERVER_NAME'];
		}
		elseif (isset($_SERVER['SERVER_ADDR']))
		{
			$host = $_SERVER['SERVER_ADDR'];
		}
	}
	return $host;
}
function app_redirect($url,$time=0,$msg='')
{
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);    
    if(empty($msg))
        $msg    =   "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if(0===$time) {
        	if(substr($url,0,1)=="/")
        	{        		
        		header("Location:".get_domain().$url);
        	}
        	else
        	{
        		header("Location:".$url);
        	}
            
        }else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0)
            $str   .=   $msg;
        exit($str);
    }
}
function msubstr($str, $start=0, $length=15, $charset="utf-8", $suffix=true)
{
  if(function_exists("mb_substr"))
    {
        $slice =  mb_substr($str, $start, $length, $charset);
        if($suffix&$slice!=$str) return $slice;
      return $slice;
    }
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix&&$slice!=$str) return $slice;
    return $slice;
}
function repair_sock($url) {
  $host = parse_url($url,PHP_URL_HOST);
  $port = parse_url($url,PHP_URL_PORT);
  $port = $port ? $port : 80;
  $scheme = parse_url($url,PHP_URL_SCHEME);
  $path = parse_url($url,PHP_URL_PATH);
  $query = parse_url($url,PHP_URL_QUERY);
  if($query) $path .= '?'.$query;
  if($scheme == 'https') {
    $host = 'ssl://'.$host;
  }

  $fp = fsockopen($host,$port,$error_code,$error_msg,1);
  if(!$fp) {
    return array('error_code' => $error_code,'error_msg' => $error_msg);
  }
  else {
    stream_set_blocking($fp,true);//开启了手册上说的非阻塞模式
    stream_set_timeout($fp,1);//设置超时
    $header = "GET $path HTTP/1.1\r\n";
    $header.="Host: $host\r\n";
    $header.="Connection: close\r\n\r\n";//长连接关闭
    fwrite($fp, $header);
    usleep(1000); // 这一句也是关键，如果没有这延时，可能在nginx服务器上就无法执行成功
    fclose($fp);
    return array('error_code' => 0);
  }
}
function gettime($type,$i) {
	switch ($type) {
		case 'month':
			$data['start']=strtotime("-".$i." months",mktime(0, 0, 0, date("m"), 1, date("Y")));
			$data['end']=strtotime("-".$i." months",mktime(23, 59, 59, date("m"), date("t"), date("Y")));
		break;
		case 'day':
			$data['start']=strtotime("-".$i." days",mktime(0, 0, 0, date("m"), date("d"), date("Y")));
			$data['end']=strtotime("-".$i." days",mktime(23, 59, 59, date("m"), date("d"), date("Y")));
		break;
		case 'year':
			$data['start']=strtotime("-".$i." years",mktime(0, 0, 0, 1, 1, date("Y")));
			$data['end']=strtotime("-".$i." years",mktime(23, 59, 59, 12, date("t"), date("Y")));
		break;
	}
	return $data;
}
function getIp(){
	$ip='未知IP';
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		return is_ip($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
	}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		return is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;
	}else{
		return is_ip($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
	}
}
function is_ip($str){
	$ip=explode('.',$str);
	for($i=0;$i<count($ip);$i++){ 
		if($ip[$i]>255){ 
			return false; 
		} 
	} 
	return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$str);
}
