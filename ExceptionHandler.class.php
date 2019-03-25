<?php
/**
 * 异常处理类
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/16
 * Time: 16:28
 */
class ExceptionHandler extends Controller{

    /**
     * 异常处理函数
     * @param $e
     */
    public function handler($e){
        if($e->getCode() == 404 && !DEBUG){
            header("HTTP/1.0 404 Not Found");//直接输出404找不到
            exit;
        }
        $msg=$e->getMessage();//错误消息
        $code=$e->getCode();//错误码
        if ($e instanceof RuntimeException) {
            // DEBUG时显示调试详细信息
            if(DEBUG){
                $detail['_exception_detail']['trace'] = $e->getTrace();//溯源消息
                $detail['_exception_detail']['file'] = $e->getFile();
                $detail['_exception_detail']['line'] = $e->getLine();
            }
            // 逻辑错误记录日志,在正式环境中显示"系统错误"
            else {
                $this->out['_msg'] = '系统错误';
            }
        }
        $this->assign('_code',$code);
        $this->assign('_msg',$msg);
        if(DEBUG){
            $this->assign('_exception_detail',$detail);
        }

        $this->display(PATH_CORE_EXTEND.'/smarty/msg.tpl');
    }




}