<?php
/**
 * 日志类
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/16
 * Time: 19:00
 */
include PATH_CORE_EXTEND."/firephp/class.firephp.php";
class LibLog{
    private static $arrLogMessage=array();
    private $out='';

    // 日志级别 从上到下，由低到高
    const EMERG     = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT     = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT      = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR       = 'ERR';  // 一般错误: 一般性错误
    const WARN      = 'WARN';  // 警告性错误: 需要发出警告的错误
    const NOTICE    = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO      = 'INFO';  // 信息: 程序输出信息
    const DEBUG     = 'DEBUG';  // 调试: 调试信息
    const SQL       = 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效



    /**
     * 记录当前脚本执行的日志消息
     * @param $message
     */
    public static function log($message){
        self::$arrLogMessage[]=$message;
    }

    /**
     * 获取上一次通过log打印进去的数据
     */
    public static function getLastMessage(){
        return end(self::$arrLogMessage);
    }

    /**
     * 记录日志 并且会过滤未经设置的级别
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param boolean $record  是否强制记录
     * @return void
     */
    public static function record($message,$level=self::ERR,$record=false) {
        if($record) {
            self::$logs[] =   "{$level}: {$message}\r\n";
        }elseif(DEBUG){
            //debug为true，开启调试
            switch($level){
                case self::ERR:
                    FirePHP::getInstance(true)->error($message);
                    break;
                case self::WARN:
                    FirePHP::getInstance(true)->warn($message);
                    break;
                case self::INFO:
                    FirePHP::getInstance(true)->info($message);
                    break;
                case self::DEBUG:
                    FirePHP::getInstance(true)->log($message);
                    break;
                default:
                    FirePHP::getInstance(true)->trace($message);
                    break;
            }
        }
    }

    public static function writeFileLog($messge){
        $body = "[ TIME ] : ".date('H:i:s')."\n";
        if(is_array($messge)){
            $messge=json_encode($messge);//数组的话，转成json存
        }
        $body .= "[ MSG ]  : ".$messge."\n";
        $handle = fopen(PATH_CACHE.'/exception_log/'.date('Y-m-d').'.log', 'a');
        fwrite($handle, $body."\n");
        fclose($handle);
        return true;
    }


}


