<?php
/**
 * KRUNK.CN BU课程表 微信小程序API - 请求删除
 * @ GitHub: https://github.com/KrunkZhou/BUSCS-WeChat-MiniProgram
 */

include('function.php');
include('kdb.class.php');

$db = new kdb([
    'dir'       => $kdb_dir,
    'extension' => $kdb_ex,
    'encrypt'   => $kdb_encrypt,
]);

$code=array('code' => 0);

$token_checked=false;
if (isset($_POST['token'])){
    $token_checked=token_check($_POST['token']);
}

if ($token_checked && isset($_POST['un'])){
	if (substr($_POST['un'], -10) == '@brocku.ca'){
        $brock_username = htmlspecialchars($_POST['un']);
    }else{
        $brock_username = htmlspecialchars($_POST['un']."@brocku.ca");
    }

    //Demo User
	if (($_POST['un'] == $demo_user_l)){
		$brock_username = $demo_user;
	}

	//从数据库中删除
	$user = $db->find_one($user_db_name,array('campusid' => $brock_username));
	if (empty($user)){
		$code['code'] = 2;
	}else{
		//Delete User
		$db->delete($user_db_name,key($user));
		$code['code'] = 1;
	}

}else{
	$code['code'] = 0;
}

echo json_encode($code);
exit(0);

?>