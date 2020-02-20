<?php
/**
 * KRUNK.CN BU课程表 微信小程序API - 功能
 * @ GitHub: https://github.com/KrunkZhou/BUSCS-WeChat-MiniProgram
 */

include('config.php');

// Token Check
function token_check($token){
	global $kapi_token;
	if ($token == $kapi_token){
		return true;
	}else{
		return false;
	}
}

// Share Token Check
function share_token_check($token){
	global $share_token;
	if ($token == $share_token){
		return true;
	}else{
		return false;
	}
}

// 获取微信 Access Token
function get_access_token(){
	global $wx_appid,$wx_appsecret;
	$access_token_get=json_decode(file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$wx_appid."&secret=".$wx_appsecret),true);
	if ($access_token_get['errcode']==0){
		$access_token=$access_token_get['access_token'];
		return $access_token;
	}else{
		return false;
	}
}


?>