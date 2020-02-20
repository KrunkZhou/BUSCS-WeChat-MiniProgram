<?php
/**
 * KRUNK.CN Brock Calendar (KBC)
 * @ Version: 3.2
 * @ Date: 2020/02/20
 * @ Website: https://course.krunk.cn/
 * @ GitHub: https://github.com/KrunkZhou/BUSCS-WeChat-MiniProgram
 * * 目录下tmp/文件夹需可写
 */
class kbc {

    /*
     * 登录并返回课程表 array
     * 登录失败返回 false
     */
    public function get_brock_cal_array($brock_username,$brock_password){
        //登录并取得html
        $html_cal=$this->grab_brock_cal_adfs_login($brock_username,$brock_password);
        //检查登录状态
        if ($html_cal==false){return false;}
        //分析table
        $cources=$this->get_brock_cal_courses_2020($html_cal);
        return $cources;
    }

    /*
     * 返回课程表 array 参数为课程表的 HTML 页面
     */
    private function get_brock_cal_courses_2020($cal){
        $table_id="ctl00_Content_ScheduleControl1_ScheduleTable"; //表格id
        libxml_use_internal_errors(true);
        $cal=str_replace('&nbsp;','',$cal);
        $cal=str_replace('<br/>','-kbr-',$cal);
        $dom = new DOMDocument();
        $dom->loadHTML($cal);
        $xpath = new DOMXPath($dom);
        $table_rows = $xpath->query('//table[@id="'.$table_id.'"]/tr');
        $results = array();

        //获取数据
        foreach($table_rows as $row) {
            $result = array();
            $ok=false;
            //表格第一行星期
            $expression = './td[1]';
            $time = preg_replace('~[\r\n\s]+~u', '_', trim($xpath->query($expression, $row)->item(0)->nodeValue));
            $week=array('2'=>'Monday','3'=>'Tuesday','4'=>'Wednesday','5'=>'Thursday','6'=>'Friday','7'=>'Saturday',);

            //从第二行开始扫描 2-7行
            for ($i=2; $i <= 7; $i++) { 
                $expression = './td['.$i.']';
                $today = $xpath->query($expression, $row)->item(0)->nodeValue;
                if ($today!=null&&$today!=''&&$today!=$week[$i]){
                    $result[$week[$i]] = array($today=>$xpath->query($expression, $row)->item(0)->getAttribute('rowspan'));
                    $ok=true;
                }
            }

            //当td不为空时存入result
            if ($time!=''&&$time!=null&&$ok){ 
                $results[$time]=$result;
            }
        }

        //split课程名称与信息
        $course_new=array();
        foreach ($results as $time => $value) {
            foreach ($value as $date => $course) {
                if (is_array($course)){
                    $course_length=$course[key($course)];
                    $course_one=explode("-kbr-", key($course));
                    $course_name=$course_one[1].$course_one[2];
                    array_push($course_one, $course_length);
                    $course_one_o=array('type'=>$course_one[0],'classroom'=>$course_one[3],'ds'=>$course_one[4],'length'=>$course_one[5]);
                    //unset($course_one[1]);unset($course_one[2]);
                    $course_new[$date][$time][$course_name]=$course_one_o;
                }
                
            }
        }

        //按照日期排序
        $arr_weekday=array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        foreach($arr_weekday as $v){
            if(array_key_exists($v,$course_new)){
                $arr_sort[$v]=$course_new[$v]; 
            }
        }

        //修改格式
        $course_new=array();
        foreach ($arr_sort as $weekday => $value) {
            foreach ($value as $time => $course) {
                $course_name=trim(key($course));
                $course_time=trim($time);
                $course_time=trim($time%100==0?$time:$time+20);
                $course_length=trim($course[key($course)]['length']);
                $course_type=trim($course[key($course)]['type']);
                $course_ds=trim($course[key($course)]['ds']);
                $course_classroom=trim($course[key($course)]['classroom']);
                $course_weekday=date('N', strtotime($weekday));
                array_push($course_new,array('name'=>substr($course_name, 0,4),
                                            'code'=>substr($course_name, 4),
                                            'time'=>$course_time,
                                            'type'=>$course_type,
                                            'length'=>$course_length/2.0,
                                            'weekday'=>$course_weekday,
                                            'classroom'=>$course_classroom,
                                            'ds'=>$course_ds));
            }
        }
        
        //rowspan校准 beta
        foreach($table_rows as $row) {
            $expression = './td[1]';
            $time = preg_replace('~[\r\n\s]+~u', '_', trim($xpath->query($expression, $row)->item(0)->nodeValue));

            $week=array('2'=>'1','3'=>'2','4'=>'3','5'=>'4','6'=>'5','7'=>'6',);

            for ($i=2; $i <= 7; $i++) { 
                $expression = './td['.$i.']';
                $today = $xpath->query($expression, $row)->item(0)->nodeValue;
                if ($today!=null&&$today!=''&&$today!=$week[$i]){
                    $rowspan_a = array($today=>$xpath->query($expression, $row)->item(0)->getAttribute('rowspan'));
                    // echo $rowspan_a[key($rowspan_a)];
                    // echo $time;
                    // echo $week[$i];
                    $rowspan=(int)$rowspan_a[key($rowspan_a)];
                    //echo $rowspan;
                    $time_i=(int)$time;
                    for ($j=0; $j < $rowspan-1; $j++) {
                        if ($time_i/10%2!=0){
                            $time_i=$time_i-30+100;
                        }else{
                            $time_i+=30;
                        }
                        foreach ($course_new as $key => $value) {
                            if ($value['time']==$time_i&&$value['weekday']>($i-2)){
                                //echo $course_new[$key]['time'];
                                //echo $time;
                                //echo 'found ';
                                // echo $week[$i];echo " ";echo $time_i;echo " ";
                                // echo $j;echo " ";
                                // echo $value['weekday'];echo " ";
                                // echo $xpath->query($expression, $row)->item(0)->nodeValue;
                                $course_new[$key]['weekday']=$course_new[$key]['weekday']+1;
                            }
                        }
                    }
                }
            }
        }

        //var_dump($course_new);
        return $course_new;
    }

    /*
     * 获取hashid
     */
    private function get_unique_hash_id(){
        return md5($_SERVER['REQUEST_TIME'] + mt_rand(1000,9999));
    }

    /*
     * CURL - 还需寻找不用文件cookies的方法
     * 
     * $url = page to POST data
     * $ref_url = refer url
     * $login = true will make a clean cookie-file.
     * $proxy = proxy data
     * $proxystatus = do you use a proxy ? true/false
     * $id = cookie file name id
     */
    private function curl_grab_page($url,$ref_url,$data,$login,$proxy,$proxystatus,$id){
        if($login == 'true') {
            $fp = fopen("/tmp/kbc-cookie-".$id.".txt", "w") or die("Unable to open file!");
            fclose($fp);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEJAR, "tmp/kbc-cookie-".$id.".txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "tmp/kbc-cookie-".$id.".txt");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36");
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ($proxystatus == 'true') {
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $ref_url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        // curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        ob_start();
        return curl_exec ($ch);
        ob_end_clean();
        curl_close ($ch);
        unset($ch);
    }

    /*
     * 登录并返回课程表页面 HTML
     */
    private function grab_brock_cal_adfs_login($brock_username,$brock_password){
        $unique=$this->get_unique_hash_id();

        //时间var
        $adfs_time=htmlspecialchars("".date("Y")."-".date("m")."-".date("d")."T".date("h")."%3a".date("i")."%3a".date("s")."Z"); //2020-02-09T02%3a07%3a43Z

        //adfs地址
        $brock_url="https://adfs.brocku.ca/adfs/ls/?wa=wsignin1.0&wtrealm=https%3a%2f%2fmy.brocku.ca%2fBrockDB%2f&wctx=rm%3d0%26id%3dpassive%26ru%3d%252fBrockDB%252freg_StudentCourseLocations.aspx&wct=".$adfs_time."&wreply=https%3a%2f%2fmy.brocku.ca%2fBrockDB%2f";
        //adfsurl = https://adfs.brocku.ca/adfs/ls/?wa=wsignin1.0&wtrealm=https%3a%2f%2fmy.brocku.ca%2fBrockDB%2f&wctx=rm%3d0%26id%3dpassive%26ru%3d%252fBrockDB%252freg_StudentCourseLocations.aspx&wct=2020-02-09T07%3a09%3a55Z&wreply=https%3a%2f%2fmy.brocku.ca%2fBrockDB%2f

        //http refer url
        $brock_refer_url="https://my.brocku.ca/BrockDB/reg_StudentCourseLocations.aspx";

        //adfs登录
        $login_adfs=$this->curl_grab_page($brock_url, $brock_refer_url, "UserName=".$brock_username."&Password=".$brock_password."&AuthMethod=FormsAuthentication", "true", "null", "false",$unique);

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($login_adfs);
        $xpath = new DOMXPath($dom);

        //adfs callback javascript
        $find = $xpath->query('//input[@name="wa"]');
        if ($find["length"]!=0){
            $wa = htmlspecialchars(trim($find->item(0)->getAttribute('value')));
        }else{
            return false; //登录失败
        }
        $find = $xpath->query('//input[@name="wresult"]');
        $wresult = urlencode(html_entity_decode(htmlspecialchars( $find->item(0)->getAttribute('value'))));
        $find = $xpath->query('//input[@name="wctx"]');
        $wctx = urlencode(html_entity_decode(htmlspecialchars(trim($find->item(0)->getAttribute('value')))));
        $find = $xpath->query('//form[@name="hiddenform"]');
        $action_url = preg_replace('~[\r\n\s]+~u', '_', trim($find->item(0)->getAttribute('action')));
        $post_data="wa=".$wa."&wresult=".$wresult."&wctx=".$wctx;
        //echo $post_data."<br><br>";
        $post_to_portal=$this->curl_grab_page($action_url, $brock_url,$post_data , "true", "null", "false",$unique);
        //echo $post_to_portal;

        //substr to html content
        if (($tmp = strstr($post_to_portal, '<')) !== false) {
            $post_to_portal = substr($tmp, 1);
        }
        $html_cal = "<".$post_to_portal."";
        //$html_cal=strstr($html_cal, '<table id="ctl00_Content_ScheduleControl1_ScheduleTable');
        //$html_cal = substr($html_cal, 0, strpos($html_cal, "<table id=\"ctl00_Content_ScheduleControl1_rptrClassNoTime_ctl00_Table1"));
        //echo $html_cal;

        //删除 Cookie
        unlink("tmp/kbc-cookie-".$unique.".txt");

        return $html_cal;
    }
}
?>