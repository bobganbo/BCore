<?php
/**
 * 核心代码
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/16
 * Time: 11:27
 */
header("Content-type: text/html; charset=utf-8");

// 定义关键常量
if(!defined('APP')){
    // 取/xtest/index.php xtest部分
    define('APP',  substr($_SERVER['PHP_SELF'],1,strlen($_SERVER['PHP_SELF'])-11));
}

define('PATH_ROOT', dirname(dirname(__FILE__)));
//核心库路径
defined("PATH_CORE") or define("PATH_CORE",PATH_ROOT."/BCore");//核心库路径
defined("PATH_CORE_LIB") or define("PATH_CORE_LIB",PATH_ROOT."/BCore/lib");//核心库lib路径
defined("PATH_CORE_EXTEND") or define("PATH_CORE_EXTEND",PATH_ROOT."/BCore/lib/extend");//核心库扩展路径
defined("PATH_CORE_PUBLIC") or define("PATH_CORE_PUBLIC",PATH_ROOT."/BCore/public");//公共库路径

//应用路径
defined("PATH_APP") or define('PATH_APP',PATH_ROOT.'/app');        // /root/app
defined("PATH_CUR_APP") or define('PATH_CUR_APP',PATH_APP.'/'.APP);        // -> /root/app/appA
defined("PATH_TEMPLATE") or define('PATH_TEMPLATE',PATH_CUR_APP.'/template');   // -> /root/app/appA/template
defined("PATH_MODEL") or define('PATH_MODEL',PATH_CUR_APP.'/model');      // -> /root/app/appA/model
defined("PATH_CONTROLLER") or define('PATH_CONTROLLER',PATH_CUR_APP.'/controller');     // -> /root/app/appA/controller
defined("PATH_CONFIG") or define('PATH_CONFIG', PATH_CUR_APP . '/config'); // ->/root/app/appA/config
defined("PATH_CACHE") or define('PATH_CACHE', PATH_CUR_APP . '/cache'); // ->/root/app/appA/cache,模板缓存目录
//异常常量定义
define('CORE_ERROR',-10001);//内核出错
define('CONFIG_ERROR',-10002);//加载配置出错
define('DB_ERROR',-10003);//数据库出错

defined("DEBUG") or define("DEBUG",false);//默认关闭调试状态

//时区设置
date_default_timezone_set("PRC");

//自定义类加载函数
spl_autoload_register("autoload");



//设置下异常处理函数
set_exception_handler("exception_handler");

//设置下出错处理函数
//set_error_handler("error_handler");

if(DEBUG){
    //调试状态
    error_reporting(E_ERROR|E_NOTICE);//错误报告级别,error和notice错误
}else{
    error_reporting(0);
}

/**
 * 字符串命名风格转换
 * @param $name
 * @param int $type
 * 2 C风格转Java风格，但不转首字母
 * 1 C风格转Java风格
 * 0 Java风格转C风格
 */
function parseName($name, $type=1){
    if($type==1){
        //-e选项 如果设置这个修饰符,preg_replace()将在替换值里进行正常的涉及到的替换
        return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
    }elseif($type==2){

    }else{
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));//Java风格转C风格
    }
}
/**
 * 路由
 * @param $controller 控制器类
 * @param $action 控制器方法
 * @param string $defaultOutType 默认输出类型
 */
function route($controller,$action,$defaultOutType='smarty'){
    $ctlClass="Ctl".parseName($controller,1);
    $method=parseName($action,2);
    $_obj=new $ctlClass;

    //禁止直接访问基类下的方法
    $reflect=new ReflectionClass("Controller");//得到一个反射实例对象
    if($reflect->hasMethod($method)){
        //直接访问Controller下的方法，返回异常
        throw new RuntimeException('Forbidden',403);
    }
    //判断下控制器类方法是否存在
    if(!method_exists($_obj,$action)){
        //方法不存在，抛出异常
        throw new RuntimeException("Methid Not Exist",404);
    }
    $_obj->$action();//执行方法
    return true;
}

/**
 * 类自动加载函数
 */
function autoload($className){
   $_file='';
   if($className =='Controller' || $className =='Model' || $className == 'ExceptionHandler'){
       $_file = PATH_CORE . '/' .$className . '.class.php';
   }else{
       //lib public model controller config目录下的类或者文件自动加载
       $mod=array('Lib'=>PATH_CORE_LIB,'Ctl'=>PATH_CONTROLLER,'Mod'=>PATH_MODEL,'Inc'=>PATH_CONFIG);
       $classType=substr($className,0,3);
       if(isset($mod[$classType])){
           $_file="{$mod[$classType]}/".basename($className).".class.php";
       }
   }
   if($_file){
       if(!is_file($_file)){
           exception_handler(new RuntimeException("File Not Found({$className})",404));//错误消息和错误码
           exit;
       }
       include $_file;
       return true;
   }
}

/**
 * 异常处理函数
 * @param Exception $e
 */
function exception_handler(Exception $e){
    $handler=new ExceptionHandler();
    $handler->handler($e);//直接抛出模板异常
}

/**
 * 错误处理函数
 */
function error_handler(){
}



