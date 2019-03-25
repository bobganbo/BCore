<?php
/**
 * @name 自定义模板封装类
 * Created by PhpStorm.
 * User: bo
 * Date: 2016/7/18
 * Time: 15:07
 */
require PATH_CORE_EXTEND.'/bobtemplate/bobtemplate.class.php';
class LibMytemplate
{
    private static $instance=null;
    public static function init (){
        if (self::$instance === null){
            self::$instance = new bobtemplate();
            self::$instance->view_dir = PATH_CUR_APP .'/template';//模板目录
            self::$instance->compile_dir = PATH_CACHE .'/MyTplCompile';
            self::$instance->left_delimiter = '<{';
            self::$instance->right_delimiter = '}>';
            self::$instance->compile_check = true;
            self::$instance->plugins_dir[] = PATH_CORE_EXTEND . '/bobtemplate/plugins';
        }
        return self::$instance;
    }

    public static function assign ($tpl_var, $value=''){
        $instance = self::init();
        $instance->assign($tpl_var, $value);
    }

    public static function display ($tpl){
        $instance = self::init();
        $instance->display($tpl);
    }

    public static function fetch($tpl){
        $instance = self::init();
        return $instance->fetch($tpl);
    }
}
