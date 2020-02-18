<?php
/**
 * KRUNK.CN BU课程表 微信小程序API
 */

include('function.php');
include('kdb.class.php');

$db = new kdb([
    'dir'       => $kdb_dir,
    'extension' => $kdb_ex,
    'encrypt'   => $kdb_encrypt,
]);

$code=array('code'         => 0,
            'course_share'  => array());

$token_checked=false;
if (isset($_POST['token'])){
    $token_checked=token_check($_POST['token']);
}

if ($token_checked && isset($_POST['share_id'])){
    if (substr($_POST['share_id'], -10) == '@brocku.ca'){
        $brock_username = htmlspecialchars($_POST['share_id']);
    }else{
        $brock_username = htmlspecialchars($_POST['share_id']."@brocku.ca");
    }

    //Demo User
    if (($_POST['share_id'] == $demo_user_l)){
        $brock_username = $demo_user;
    }

    $user = $db->find_one($user_db_name,array('campusid' => $brock_username));
    if (!empty($user)){
        $code['code'] = 1;
        $code['course_share'] = $user[key($user)]['course_index'];
    }
}else{
	$code['code'] = 0; //登录失败
}

echo json_encode($code);
exit(0);

?>