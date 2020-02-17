<?php
/**
 * KRUNK.CN BU课程表 微信小程序API
 */

include('function.php');
include("krunk-brock-get-cal.class.php");
include('kdb.class.php');

$kbc=new kbc;
$db = new kdb([
    'dir'       => $kdb_dir,
    'extension' => $kdb_ex,
    'encrypt'   => $kdb_encrypt,
]);

$code=array('code'         => 0,
            'course_list'  => array(),
			'course_index' => array());

$token_checked=false;
if (isset($_POST['token'])){
    $token_checked=token_check($_POST['token']);
}

if ($token_checked && isset($_POST['un']) && isset($_POST['pw'])){
    if (substr($_POST['un'], -10) == '@brocku.ca'){
        $brock_username = htmlspecialchars($_POST['un']);
    }else{
        $brock_username = htmlspecialchars($_POST['un']."@brocku.ca");
    }
	$brock_password = htmlspecialchars($_POST['pw']);

    //Demo User
	if (($_POST['un'] == $demo_user_l) && ($_POST['pw'] == $demo_pw_l)){
		$brock_username = $demo_user;
		$brock_password = $demo_pw;
	}

    //登录并获取课程
	$courses=$kbc->get_brock_cal_array($brock_username,$brock_password);

	if ($courses == false){
		$code['code'] = 0; //登录失败
	}else{
		$code['code'] = 1; //登录成功
		$code['course_index'] = json_encode($courses); //课程表首页

        //整理为列表 Array
        $course_list = array();
        foreach ($courses as $key => $value) {
            $course_list[$value['weekday']][$value['time']][$value['name'].$value['code']] = array('type' => $value['type'],
                        'length' => $value['length'],
                        'classroom' => $value['classroom'],
                        'ds' => $value['ds'],
                    );
        }
        $code['course_list'] = json_encode($course_list); //课程表列表
	}
}else{
	$code['code'] = 0; //登录失败
}

echo json_encode($code);
exit(0);

?>