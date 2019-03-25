<?php
/**
 * @name xxx
 * Created by PhpStorm.
 * User: bo
 * Date: 2016/7/13
 * Time: 19:52
 */
class IncMail{
    private static $config=array(
        'default'=>array(
            'smtp_host'          =>'',//认证主机
            'smtp_port'          =>'',//认证端口
            'smtp_auth_user'     =>'',//认证用户
            'smtp_auth_password' =>'',//认证密码
            'from_name'          =>'',//发件人姓名
            'from_email'         =>'',//发件人邮箱
            'charser'            =>'utf-8',
            'secure'             =>'',
        ),
    );

    /**
     * 返回配置信息
     * @return array
     */
    public static function getConfig($mailConfig){
        return self::$config[$mailConfig];
    }
}