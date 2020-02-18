<?php
/**
 * KRUNK.CN 微信客服API
 * @ Date: 2020/02/18
 */

include('kdb.class.php');
include('config.php');
$db = new kdb([
    'dir'       => $kdb_kefu_dir,
    'extension' => "kdb",
    'encrypt'   => false,
]);

function get_access_token($wx_appid,$wx_appsecret){
    $access_token_get=json_decode(file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$wx_appid."&secret=".$wx_appsecret),true);
    if ($access_token_get['errcode']==0){
        $access_token=$access_token_get['access_token'];
        return $access_token;
    }else{
        return false;
    }
}

function send_user($send_url,$send_data){
    $curl = curl_init();
    ignore_user_abort(true);
    curl_setopt($curl, CURLOPT_URL, $send_url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($send_data)) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $send_data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($send_data))
    );  
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

function upload_image($token,$image_path){
    $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$token."&type=image";
    $file_data = array("media"  => new \CURLFile($image_path)); 
    $ch = curl_init();
    ignore_user_abort(true);
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch , CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$file_data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;      
}

//Access Token
$access_token=get_access_token($wx_appid,$wx_appsecret);
$send_url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;

if ($send_url!=false){

    //获取用户发送消息到 postArr
	$postStr = file_get_contents('php://input');if (!$postStr) die('false');
	$postArr = json_decode($postStr, true);if (!isset($postArr['MsgType']) || !isset($postArr['FromUserName'])|| !isset($postArr['MsgId'])) die('false');

    //防止微信五秒三次post 存入msgid到数据库
    $user1 = $db->find_one($kdb_kefu_msgid_db,array('MsgId' => $postArr['MsgId']));
    $data = array(
                'MsgId' => $postArr['MsgId']
    );
    $db->insert($kdb_kefu_msgid_db,$data);

    
    if (empty($user1)){

        echo "success";

        if($postArr['Content']=='1'){
            $content="https://krunk.cn/kblog1136.html";
            $send_data = json_encode(array("touser"=>$postArr['FromUserName'],"msgtype"=>"text","text"=>array("content"=>$content)),JSON_UNESCAPED_UNICODE);
            $output=send_user($send_url,$send_data);
        }else if($postArr['Content']=='2'){
            $content='<a href="https://github.com/KrunkZhou/BUSCS-WeChat-MiniProgram" >点击前往GitHub</a>';
            $send_data = json_encode(array("touser"=>$postArr['FromUserName'],"msgtype"=>"text","text"=>array("content"=>$content)),JSON_UNESCAPED_UNICODE);
            $output=send_user($send_url,$send_data);
        }else if($postArr['Content']=='3'){
            $content="分享课表例子\n<a href=\"http://www.qq.com\" data-miniprogram-appid=\"wx66cfb10ff2bff81d\" data-miniprogram-path=\"pages/share/share?id=krunk\">点击跳小程序</a>
";
            $send_data = json_encode(array("touser"=>$postArr['FromUserName'],"msgtype"=>"text","text"=>array("content"=>$content)),JSON_UNESCAPED_UNICODE);
            $output=send_user($send_url,$send_data);
        }else if($postArr['Content']=='4'){
            $image_opt=json_decode(upload_image($access_token,'image/buscs.jpg'),true);
            $qr_id=$image_opt['media_id'];
            $send_data_pic = json_encode(array("touser"=>$postArr['FromUserName'],
                                            "msgtype"=>"image",
                                            "image"=>array("media_id"=>$qr_id)),JSON_UNESCAPED_UNICODE);
            $output_pic=send_user($send_url,$send_data_pic);
        }else{

            //发送文字
            $content="BU课程表小程序\n请添加开发者微信号: shsd86\n\n回复1了解小程序\n回复2了解开源详情\n回复3查看分享例子\n回复4获取小程序码\n\n( KAPI客服系统自动回复 )";
        	$send_data = json_encode(array("touser"=>$postArr['FromUserName'],
                                            "msgtype"=>"text",
                                            "text"=>array("content"=>$content)),JSON_UNESCAPED_UNICODE);
        	$output=send_user($send_url,$send_data);

            //发送图片
            $image_opt=json_decode(upload_image($access_token,'image/qr.jpeg'),true);
            $qr_id=$image_opt['media_id'];
            $send_data_pic = json_encode(array("touser"=>$postArr['FromUserName'],
                                            "msgtype"=>"image",
                                            "image"=>array("media_id"=>$qr_id)),JSON_UNESCAPED_UNICODE);
            $output_pic=send_user($send_url,$send_data_pic);

        }

    }
	
}else{
	die('false');
}

?>