<?php
/**
 * 数据库配置
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/17
 * Time: 10:47
 */
class IncDb{
    /**
     * 其它数据库配置在此添加即可
     * @var array
     */
    private static $config=array(
         'default'=>array(
             'db' => 'bubble_sms',
             'master' => array(
                 'host' => '127.0.0.1:3306',
                 'user' => 'root',
                 'passwd' => 'gb1990'
             ),
             'slave' => array(
                 'host' => array('127.0.0.1:3306'),
                 'user' => 'root',
                 'passwd' => 'gb1990'
             ),
          ),
    );

    /**
     * 返回配置信息
     * @return array
     */
    public static function getConfig($dbConfig){
         return self::$config[$dbConfig];
    }

}