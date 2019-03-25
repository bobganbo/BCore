<?php
/**
 * 控制器基类
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/16
 * Time: 11:46
 */
class Controller{
    public $get;
    public $post;
    public function __construct(){
        $this->get=$_GET;
        $this->post=$_POST;
        unset($_GET);//释放掉
        unset($_POST);
    }

    /**
     * ajax请求json数据输出
     * @param $data
     */
    public function ajaxReturn($data){
        if (is_string($data))
            exit($data);
        exit(json_encode($data));
    }

    /**
     * 字符串输出
     * @param $data
     */
    public function textReturn($data){
        if(is_array($data)){
            exit(var_export($data));
        }else{
            exit($data);
        }
    }
    /**
     * 跨域json输出
     * @param $data array
     */
    public function jsonpReturn($data){
        $callback = $this->I('jsoncallback');
        if (empty($callback)){
            exit(json_encode($data));
        }else{
            exit($callback .'('.json_encode($data).')');
        }
    }

    /**
     * 模板变量赋值
     * @param $name 变量名
     * @param $value 变量值
     */
    public function assign($name,$value=''){
        LibMytemplate::assign($name,$value);
    }

    /**
     * 该函数用于输出模板
     * @param $tpl 模板路径
     */
    public function display($tpl){
        LibMytemplate::display($tpl);
    }


    /**
     * 读取外部输入参数
     * @param $name 参数名
     * @param $defaultValue 默认值
     * @param $varType 值类型
     * @param string $callback 参数默认执行的回调函数
     */
    public function I($name,$defaultValue='',$varType='string',$callback='Controller::_I'){
         //post参数优先,取不到任何参数即取默认值
         $tmp=isset($this->post[$name])?$this->post[$name]:(isset($this->get[$name])?$this->get[$name]:$defaultValue);
         if(get_magic_quotes_gpc()) {
             //如果php on这个开启的话，会将一些特殊字符转义
             $tmp = stripslashes($tmp);//删除由addslashes() 函数添加的反斜杠
         }
         if(!empty($callback)){
             $tmp=call_user_func($callback,$tmp);//通过用户自定义函数过滤用户输入参数
         }
         if(!empty($varType))settype($tmp,$varType);//强制类型转换
         return $tmp;
    }

    private static function _I($var){
       if(!is_array($var)){
           $_tmp=htmlspecialchars(addslashes($var));//这个两个函数对输入值进行过滤
       }else{
           foreach($var as $k=>$v){
                $_tmp[$k]=self::_I($v);//递归处理数组参数
           }
       }
       return $_tmp;
    }

}

