<?php
/**
 * 用户登录登录状态验证类
 * Created by PhpStorm.
 * User: john
 * Date: 2015/12/5
 * Time: 16:51
 */

class LibAuth{
    private static $cookieName = 'Cuser';
    private static $config=array(
        'authKey'=>'^%$gbvc123()[]#bc',
    );

    public static $userName = '';
    public static $userId = 0;

    /**
     * 校验登录状态
     * @return bool
     * @throws Exception
     */
    private static function checkAuth(){
        $_authCookie = LibUtil::cookie(self::$cookieName);//读取cookie
        if (empty($_authCookie)) {
            return false;
        }

        $_auth = explode('|', $_authCookie);
        $_sign = md5("{$_auth[0]}|{$_auth[1]}|{$_auth[2]}|" . self::$config['authKey']);
        $rs = isset($_auth[3]) && $_auth[3] == $_sign;
        if ($rs) {
            self::$userId = $_auth[0];//验证通过
            // 验证通过还要对是否超过了失效时间做一下校验
            $timeRs = self::checkAuthTime($_auth[2]);
            if ($timeRs) {
                self::$userName = $_auth[1];
                return true;
            } else {
                self::$userId = 0;
                LibUtil::cookie(self::$cookieName, '', -1);
                return false;
            }
        } else {
            LibUtil::cookie(self::$cookieName, '', -1);//未验证通过
            return false;
        }


    }


    /**
     * 检查cookie生成时间 和 cookie在平台的失效时间
     * @param int $time
     * @return bool
     */
    private static function checkAuthTime($time)
    {

        $expireTime=LibMemcache::get('cookieAuth_'."USER_" . md5(self::$userId));
        $curTime=time();
        return ($time+$expireTime)>$curTime;
    }


    public static function isLogin($autoGo=true){
        if (self::checkAuth()) {
            $_ckAccName = LibUtil::cookie('ck_username');//检测用户名
            if (empty($_ckAccName)) {
                LibUtil::cookie('ck_username', self::$userName, 0);//
            }
            $result = TRUE;
        } else {
            LibUtil::cookie("ck_userid", '');
            LibUtil::cookie("ck_username", '');
            LibUtil::cookie(self::$cookieName, '');
            $result = FALSE;
        }
        if ($autoGo) {
            //默认未登录跳转到
            if (!$result) {
                header('Location:/user/?ac=login&redirecturl=' . urlencode($_SERVER['REQUEST_URI']));//登录后进行跳转
                die;
            }
        } else {
            return $result;
        }

    }

    /**
     * 种植用户cookie
     * @param $userInfo
     */
    public static function setUserCookie($userInfo){
        $time=time();//当前时间
        $expireTime=3600*24;//过期时间,设置长一点，1天
        $sign=md5("{$userInfo['uid']}|{$userInfo['uname']}|{$time}|" . self::$config['authKey']);
        $ck_name=$userInfo['uid']."|".$userInfo['uname']."|".$time."|".$sign;
        LibUtil::cookie(self::$cookieName,$ck_name);
        LibUtil::cookie("ck_userid", $userInfo['uid']);
        LibUtil::cookie("ck_username", $userInfo['uname']);
        LibMemcache::set('cookieAuth_'."USER_" . md5($userInfo['uid']),$expireTime);
    }
    /**
    *
    *清空用户种下的cookie
    */ 	
    public static function clearUserCookie(){
        LibUtil::cookie(self::$cookieName,null);
        LibUtil::cookie("ck_userid",null);
        LibUtil::cookie("ck_username",null);
        LibMemcache::delete('cookieAuth_'."USER_" . md5(LibAuth::$userId));
    }


}