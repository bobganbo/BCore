<?php
/**
 * 定义一些常用的工具函数
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/16
 * Time: 17:19
 */
class LibUtil{
    private static $start_time='';
    private static $end_time='';

    /**
     * cookie工具函数
     * @param $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param $domain
     */
    public static function cookie($name,$value='',$expire=0,$path='/', $domain=null){
        if(!$name){
            throw new Exception("cookie参数调用错误");
        }
        if(empty($value)){
            //取cookie
            return isset($_COOKIE[$name])?$_COOKIE[$name]:'';
        }
        if(empty($domain)){
            $domain=strstr($_SERVER['SERVER_NAME'],'.');//返回字符串剩余的部分
        }
        if($value===null){
            //清空cookie值
            return setcookie($name,"deleted", 1, '/',$domain);//清空某个域名下的cookie
        }
        if($expire){
            $expire+=time();
	    
        }

        return setcookie($name,$value,$expire,$path,$domain);//expire为0表示永不过期
    }

    /**
     * session处理函数
     * @param $key 键名
     * @param $value 键值
     * @return bool|string
     */
    public static function session($key,$value=''){
        if (!isset($_SESSION)) {
            session_start();//开启session
        }
        if (isset($value)&&!empty($value)) {
            $_SESSION[$key] = $value;//设置session
            return true;
        } else {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : '';
        }
    }

    /**
     * 获取客户端IP地址
     * @return string
     */
    public static function getClientIp(){
        $ip='未知IP';
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            return self::isIp($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            return self::isIp($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;
        }else{
            return self::isIp($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
        }
    }

    /**
     * 判断字符串是否为ip地址
     * @param $str
     * @return bool|int
     */
    public static function isIp($str){
        $ip=explode('.',$str);
        for($i=0;$i<count($ip);$i++){
            if($ip[$i]>255){
                return false;
            }
        }
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$str);
    }

    /**
     * 获取服务端Ip
     * @return string
     */
    public static function getServerIp(){
        static $serverip;
        if(!empty($serverip)){
            return $serverip;
        }
        if(isset($_SERVER)){
            $serverip=isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:'0.0.0.0';
        }else{
            $serverip=getenv('SERVER_ADDR');
        }
        return $serverip;
    }

    /**
     *
     * 判断ip所在的省市
     * @param $ip ip地址
     */
    public static function getIpLocation($ip){
        if(!$ip) return "未知";
        if(!empty($GLOBALS['ip'][$ip])){
            return $GLOBALS['ip'][$ip];
        }
        require_once PATH_EXTEND."/ip/class.ip.php";
        $ex=new IP();
        $GLOBALS['ip'][$ip]=$ex->getlocation($ip);//返回地区信息
        return $GLOBALS['ip'][$ip];
    }

    /**
     * 随机码生成
     * @param $len 产生一个长度为len的随机码
     */
    public static function randNum($len=4){
        $code='';
        for($i=0;$i<$len;$i++){
           $code.=chr(mt_rand(0,9));//mt_rand产生随机数，chr将ascii转成对应的字符
        }
        return $code;
    }

     /**
     * 36以下任意进制转换为10进制
     *
     * @param mixed $number 原始数字
     * @param int $l 进制
     * @return int
     */
     public static function HexToDec($number, $l){
        $base_char = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $num = 0;
        for($i=0;$i<=strlen($number)-1;$i++){
            //从末尾依次取得字符串
            $char = substr($number,-($i+1),1);
            //取得字符串的位置作为值
            $val = strpos($base_char, strtoupper($char));
            //取得字符所代表的数值
            $num = $num + $val*(pow($l,$i));
        }
        return $num;
    }

    /**
     * 邮箱验证
     * @param $email
     * @return boolean
     */
    public static function checkEmail($email){
        return filter_var($email,FILTER_VALIDATE_EMAIL);
    }

    /**
     * 安全过滤数据
     *
     * @param string|array $str 需要处理的字符或数组
     * @param string $type 返回的字符类型，支持，string,int,float,html
     * @param mixed $default 当出现错误或无数据时默认返回值
     * @return string|array|mixed 当出现错误或无数据时默认返回值
     */
    public static function getStr($str, $type = 'string', $default = '')
    {
        //如果为空则为默认值
        if ($str === '') {
            return $default;
        }

        if (is_array($str)) {
            $_str = array();
            foreach ($str as $key => $val) {
                $_str[$key] = self::getStr($val, $type, $default);
            }

            return $_str;
        }

        //转义
        if (!get_magic_quotes_gpc()) {
            $str = addslashes($str);
        }

        switch ($type) {
            case 'string':    //字符处理
                $_str = strip_tags($str);
                $_str = str_replace("'", '&#39;', $_str);
                $_str = str_replace("\"", '&quot;', $_str);
                $_str = str_replace("\\", '', $_str);
                $_str = str_replace("\/", '', $_str);
                $_str = str_replace("+/v", '', $_str);
                break;
            case 'int':    //获取整形数据
                $_str = intval($str);
                break;
            case 'float':    //获浮点形数据
                $_str = floatval($str);
                break;
            case 'html':    //获取HTML，防止XSS攻击
                $_str = self::reMoveXss($str);
                break;

            default:    //默认当做字符处理
                $_str = strip_tags($str);
        }

        return $_str;
    }

    /**
     * 计算最近联系时间
     * @param $expriestimes
     * @return string
     */
    public static function expriestime($expriestimes){
        //计算最近联系时间
        $expries = '';
        if(!empty($expriestimes)){
            $expriestime = floor((time() - $expriestimes)/3600);

            if($expriestime < 0){
                $expries = '刚刚';
            }else if($expriestime==0){
                $expries = floor((time() - $expriestimes)/60).'分前';
            }else if($expriestime<24){
                $expries = $expriestime.'小时前';
            }else{
                $day = floor($expriestime/24);
                if($day == 1){
                    $expries = '昨天';
                }else{
                    $expries = $day.'天前';
                }
            }
        }
        return $expries;
    }

    public static function startTime(){
        self::$start_time=microtime(true);
    }

    public static function endTime(){
        self::$end_time=microtime(true);
    }
    /**
     *
     * @param int $precision 精度 默认0，表示不保留小数
     */
    public static function excuteTime($precision=0){
        $take_time=round((self::$end_time-self::$start_time),$precision);
        return $take_time;
    }

    /**
     * 递归无限级分类【先序遍历算】，获取任意节点下所有子孩子
     * @param array $arrCate 待排序的数组
     * @param int $parent_id 父级节点
     * @param int $level 层级数
     * @return array $arrTree 排序后的数组
     */
    public static function getMenuTree($arrCat, $parent_id = 0, $level = 0)
    {
        static  $arrTree = array(); //使用static代替global
        if( empty($arrCat)) return FALSE;
        $level++;
        foreach($arrCat as $key => $value)
        {
            if($value['parent_id' ] == $parent_id)
            {
                $value[ 'level'] = $level;
                $arrTree[] = $value;
                unset($arrCat[$key]); //注销当前节点数据，减少已无用的遍历
                getMenuTree($arrCat, $value[ 'id'], $level);
            }
        }

        return $arrTree;
    }
}
