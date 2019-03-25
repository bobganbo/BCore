<?php
/**
 * Memcache
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/16
 * Time: 17:17
 */
class LibMemcache{
    private static $connections=array();
    private static $config;

    private function __construct(){}
    private static function connect($configName){
        if(empty($configName)){
            $configName='default';
        }
        $memcacheConfigPath=PATH_CUR_APP."/config/IncMemcache.class.php";
        if(!is_file($memcacheConfigPath)){
            //优先以用户配置的为准
            $memcacheConfigPath =PATH_ROOT."/config/IncMemcache.class.php";
        }
        if(is_file($memcacheConfigPath)){
             self::$config=IncMemcache::getConfig($configName);
        }else{
             throw new RuntimeException("memcahe配置文件找不到",CONFIG_ERROR);
        }
        if(empty(self::$config)){
             throw new RuntimeException("mc配置项{$configName}找不到",CONFIG_ERROR);
        }
        if(empty(self::$connections[$configName])){
            self::$connections[$configName]=new Memcache;
            self::$connections[$configName]->addServer(self::$config[0],self::$config[1],self::$config[2]);
        }
        return self::$connections[$configName];
    }

    public static function set($key,$value,$ttl=3600,$configName='default'){
        if(empty($key))return false;
        return self::connect($configName)->set($key,$value,false,$ttl);//默认缓存1小时
    }

    public static function get($key,$configName='default'){
        if(empty($key))return false;
        return self::connect($configName)->get($key);
    }

    public static function delete($key,$configName='default'){
        if(empty($key))return false;
        return self::connect($configName)->delete($key);
    }

    public static function increment($key, $value = 1,$configName='default'){
        if(empty($key))return false;
        return self::connect($configName)->increment($key,$value);
    }

    public static function decrement($key, $value = 1,$configName='default'){
        if(empty($key))return false;
        return self::connect($configName)->decrement($key,$value);
    }

}