<?php
/**
 * smarty模板封装类
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/16
 * Time: 15:29
 */
require PATH_CORE_EXTEND.'/smarty/Smarty.class.php';
class LibTemplate
{
      private static $instance=null;
      public static function init (){
          if (self::$instance === null){
              self::$instance = new Smarty();
              self::$instance->template_dir = PATH_CUR_APP .'/template';//模板目录
              self::$instance->compile_dir = PATH_CACHE .'/tplCompile';
              self::$instance->cache_dir = PATH_CACHE .'/tplCache';
              self::$instance->left_delimiter = '{';
              self::$instance->right_delimiter = '}';
              self::$instance->caching = false;
              self::$instance->compile_check = true;
              self::$instance->plugins_dir[] = PATH_CORE_EXTEND . '/smarty/plugins';
          }
          return self::$instance;
      }

      public static function assign ($tpl_var, $value){
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
