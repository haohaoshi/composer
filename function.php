<?php
/**
 * Created by PhpStorm.
 * User: Lujz
 * Date: 2018\10\16 0016
 * Time: 13:25
 */

//开启报错提示
ini_set("display_errors","on");
error_reporting(E_ALL | E_STRICT);

$result = '';
/**
 * 格式化输出
 * @param $data
 */
function dump(){
    echo "<pre>";
    var_dump($GLOBALS['result']);
    die;
}


//日志输出
function log_ljz($data,$file_name='log_ljz')
{
    if(is_array($data))$data = var_export($data,true);
    $file=dirname(__FILE__)."/{$file_name}.log";
    $res=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $log = "";
    $log .= "执行日期：".strftime("%Y-%m-%d %H:%M:%S",time())."\n ";
    $log .= "文件所在位置：".$res[0]['file'].",位于第".$res[0]['line']."行\n";
    $log .= $data."\n\n";
    file_put_contents($file,$log,FILE_APPEND|LOCK_EX);
}

/**
 * 数组键的大小写转换（只对一级有效）
 * @param array $data
 * @param int $type 默认小写，1大写，0小写
 * @return array
 */
function ary_dx($data,$type=0){
    if(is_array($data)){
        $res = array_change_key_case($data,$type? CASE_UPPER :CASE_LOWER);
        return $res;
    }else{
        return $data;
    }
}

/**
 * 可选择式传参
 */
function uncertainParam() {
    $numargs = func_num_args();    //获得传入的所有参数的个数
    echo "参数个数: $numargs\n";
    $args = func_get_args();       //获得传入的所有参数的数组
    var_dump($args);
    foreach($args as $key=>$value){
        //echo '<BR><BR>'.func_get_arg($key);   //获取单个参数的值
        echo '<BR>'.$value;        //单个参数的值
    }
    //var_export($args);
}

//获取客户端ip
function get_client_ip() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return($ip);
}

// 建立递归目录
function Directory( $dir ){
    return  is_dir ( $dir ) or Directory(dirname( $dir )) and  mkdir ( $dir , 0777);
}

//中文日期转换时间戳
function utfTime($str){
    $arr = date_parse_from_format('Y年m月d日',$str);
    $time = mktime(0,0,0,$arr['month'],$arr['day'],$arr['year']);
    return $time;
}



//返回时间 当天至月末
function dmTime($nowTime){
    $timeAry = array();
    $endThismonth=(int)date('Ymd',mktime(23,59,59,date('m'),date('t'),date('Y')));
    $nowTime = intval($nowTime);
    for($i=$nowTime;$i<=$endThismonth;$i++){
        if($nowTime<=$endThismonth){
            $timeAry[] = $nowTime++;
        }
    }
    return $timeAry;
}

//返回时间 当天后的30天内的数据
function dmyTime($nowTime){
    $timeAry = array();
    for($i=1;$i<30;$i++){
        $timeAry[] = date('Ymd',strtotime('+'.$i.' day',strtotime($nowTime)));
    }
    return $timeAry;
}

//数组编码转换
function array_iconv($in_charset,$out_charset,$arr){
    return eval('return '.iconv($in_charset,$out_charset,var_export($arr,true).';'));
}

/**
 * 获取客户端IP地址
 *
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function ip_address($type = 0)
{
    $type = $type ? 1 : 0;
    static $ip =  NULL;

    if($ip !== NULL) return $ip[$type];

    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);

        if(FALSE !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    }
    else if(isset($_SERVER['HTTP_CLIENT_IP']))
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    else if(isset($_SERVER['REMOTE_ADDR']))
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    /* IP地址合法验证 */
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);

    return $ip[$type];
}
//获取https内容
function curl_gets($url,$referer = '',$ishttp=1)
{
    $ip = array('220.138.60.40','183.60.15.173','120.43.72.20','112.110.20.11','140.50.112.58','128.110.90.23','140.28.100.40','120.58.60.74','200.57.20.114','89.110.11.2','10.57.112.2','10.58.112.3','113.10.21.5');

    $rand = $ip[rand(0,count($ip)-1)];
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    //$cookie = 'JSESSIONID=1ABA8CECECD54692F54FB6C1E6CE8344';
    //curl_setopt ($curl, CURLOPT_COOKIE , $cookie);
    if($ishttp == 1)
    {
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER , array(
        "CLIENT-IP:{$rand}",
        "X-FORWARDED-FOR:{$rand}"));
    curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36");
    //$referer ? curl_setopt($curl, CURLOPT_REFERER, $referer) : '';
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $result['data'] = curl_exec($curl);
    if(curl_errno($curl))
    {
        echo 'Curl error: ' . curl_error($curl);
    }
    $result['code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return $result;
}
/**
 * 执行curl
 *
 * @param string $url       请求地址
 * @param array $param      请求参数
 * @param string $type      请求类型
 * @param string $dataType  请求的数据类型
 * @param int $timeout      超时时间
 *
 * @return mixed 返回请求响应内容
 *
 */
function _curl($url, $param = array(), $type = 'post', $dataType = '', $timeout = 5) {

    $curl = curl_init();                                            // 初始化
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  // 设置返回值
    if(is_numeric($timeout) && $timeout >= 0) curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);    // 设置超时时间
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  // 检测服务器的域名与证书上的是否一致，关闭
    $type = strtolower($type);
    if($type == 'get' && $param) { // get请求
        $url .= (strpos($url, '?') === false? '&': '?') .  http_build_query($param);
    }
    else if($type == 'post') { // post请求
        curl_setopt($curl, CURLOPT_POST, 1);   // 开启post
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param); // 设置post数据
    }
    curl_setopt($curl, CURLOPT_URL, $url); // 设置url
    $result = curl_exec($curl);   // 执行
    curl_close($curl);   // 关闭
    // 如果请求的数据类型是json 自动转义
    return (strtolower($dataType) == 'json')? json_decode($result, true) : $result;
}

/**
 * @param string $url 跳转地址
 * @param string $msg 提示信息
 * @param int $sleep 等待毫秒数
 */
function _exit($url = '', $msg = '', $sleep = 0) {

    header("Content-Type:text/html;charset=utf-8");

    $url = addslashes($url);
    $msg = addslashes($msg);

    exit("<html>
页面跳转中...
<script>
    if('{$msg}') alert('{$msg}');
    setTimeout(function() {

        const url = '{$url}';
        if(url) {
            
            window.location.href = url;
        }
        else {
            
            history.go(-1);
        }
    }, {$sleep});
</script>        
</html>");
}

/**
 * 检查参数
 *
 * @param $param
 * @param $require
 * @return bool
 *
 */
function _checkParam($param, $require) {

    foreach ($require as $item) {

        if(!isset($param[$item])) {

            $res = "[{$item}]必填";
            var_dump($res);
            return false;
        }
    }

    return true;
}

/**
 * 过滤不需要的参数
 *
 * @param array $param 进行过滤的参数数组
 * @param array $filter 指定需要的参数名
 * @return array
 */
function _param_filter($param, $filter) {

    $filter = array_fill_keys($filter, '');
    return array_intersect_key($param, $filter);
}

/**
 * 返回多久以前
 * @param $the_time  传入的时间  Ymd
 * @return string
 */
function timeStr($the_time) {
    $now_time = date("Y-m-d H:i:s", time());
    //echo $now_time;
    $now_time = strtotime($now_time);
    $show_time = strtotime($the_time);
    $dur = $now_time - $show_time;
    if ($dur < 0) {
        return $the_time;
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 259200) {//3天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        return $the_time;
                    }
                }
            }
        }
    }
}

//加密函数
function lock_url($txt,$key='www.jb51.net')
{
    $txt = $txt.$key;
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $nh = rand(0,64);
    $ch = $chars[$nh];
    $mdKey = md5($key.$ch);
    $mdKey = substr($mdKey,$nh%8, $nh%8+7);
    $txt = base64_encode($txt);
    $tmp = '';
    $i=0;$j=0;$k = 0;
    for ($i=0; $i<strlen($txt); $i++) {
        $k = $k == strlen($mdKey) ? 0 : $k;
        $j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;
        $tmp .= $chars[$j];
    }
    return urlencode(base64_encode($ch.$tmp));
}
//解密函数
function unlock_url($txt,$key='www.jb51.net')
{
    $txt = base64_decode(urldecode($txt));
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $ch = $txt[0];
    $nh = strpos($chars,$ch);
    $mdKey = md5($key.$ch);
    $mdKey = substr($mdKey,$nh%8, $nh%8+7);
    $txt = substr($txt,1);
    $tmp = '';
    $i=0;$j=0; $k = 0;
    for ($i=0; $i<strlen($txt); $i++) {
        $k = $k == strlen($mdKey) ? 0 : $k;
        $j = strpos($chars,$txt[$i])-$nh - ord($mdKey[$k++]);
        while ($j<0) $j+=64;
        $tmp .= $chars[$j];
    }
    return trim(base64_decode($tmp),$key);
}


/*
$array:需要排序的数组
$keys:需要根据某个key排序
$sort:倒叙还是顺序
*/
function arraySort($array,$keys,$sort='asc') {
    $newArr = $valArr = array();
    foreach ($array as $key=>$value) {
        $valArr[$key] = $value[$keys];
    }
    ($sort == 'asc') ?  asort($valArr) : arsort($valArr);//先利用keys对数组排序，目的是把目标数组的key排好序
    reset($valArr); //指针指向数组第一个值
    foreach($valArr as $key=>$value) {
        $newArr[$key] = $array[$key];
    }
    return $newArr;
}






