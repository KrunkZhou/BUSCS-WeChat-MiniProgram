<?php
/**
 * KRUNK.CN BU课程表 微信小程序API - 获取openid
 * @ GitHub: https://github.com/KrunkZhou/BUSCS-WeChat-MiniProgram
 */

include('function.php');
include('kdb.class.php');

$db = new kdb([
    'dir'       => $kdb_dir,
    'extension' => $kdb_ex,
    'encrypt'   => $kdb_encrypt,
]);

$code=array('code' => 0,
			'openid'=>'',
			'session_key'=>'');

$token_checked=false;
if (isset($_POST['token'])){
	$token_checked=token_check($_POST['token']);
}

if ($token_checked && isset($_POST['code'])){
	$wx_return=@file_get_contents('https://api.weixin.qq.com/sns/jscode2session?appid='.$wx_appid.'&secret='.$wx_appsecret.'&js_code='.$_POST['code'].'&grant_type=authorization_code');
	//var_dump($wx_return);
	if (isset($wx_return)&&json_decode($wx_return,true)['errcode']==0){
		$code['code'] = 1;
		$code['openid'] = json_decode($wx_return,true)['openid'];
		$code['session_key'] = json_decode($wx_return,true)['session_key'];
	}

	//存入数据库
	$user = $db->find_one($openid_db_name,array('openid' => $code['openid']));
	if (empty($user)){
		$data = array(
	        'openid' => $code['openid'],
	        'time' => date('Y/m/d H:i:s a', time()),
	        'ip'  => $_SERVER['REMOTE_ADDR'],
	        'count'  => 1
	    );
	    $db->insert($openid_db_name,$data);
	}else{
	    $data = array(
	        'time' => date('Y/m/d H:i:s a', time()),
	        'ip'  => $_SERVER['REMOTE_ADDR'],
	        'count'  => $user[key($user)]['count']+1
	    );
	    $db->update($openid_db_name,$data,key($user));
	}

}else{
	$code['code'] = 0;
}

echo json_encode($code);
exit(0);

?>