<?php
/**
 * mc缓存配置类
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/19
 * Time: 11:25
 */
class IncMemcache{
    private static $config=array(
        'default'=>array('127.0.0.1','11211',1),//第三个参数为权重
    );

    /**
     * 返回配置信息
     * @return array
     */
    public static function getConfig($dbMemcacheConfig){
        return self::$config[$dbMemcacheConfig];
    }




}