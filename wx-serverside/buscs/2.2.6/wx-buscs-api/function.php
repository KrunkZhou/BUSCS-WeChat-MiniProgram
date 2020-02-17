<?php
/**
 * KRUNK.CN BU课程表 微信小程序API
 */

include('config.php');

// Token Check
function token_check($token){
	global $kapi_token;
	$code=array('code' => 0);
	if ($token == $kapi_token){
		return true;
	}else{
		return false;
	}
}


?>