<?php
/**
 * 二维码生成类
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/18
 * Time: 22:46
 */

class LibQrcode{
    static private $libLoaded = false;

    /**
     * 加载二维码生成库函数
     */
    private static function loadLib(){
        if (self::$libLoaded) {
            return;
        }
        self::$libLoaded = true;
        include PATH_CORE_EXTEND . "/phpqrcode/phpqrcode.php";
    }

    /**
     * 生成图片二维码
     * @param $data string  生成二维码的参数
     */
    public static function png($data){
        self::loadLib();
        QRcode::png($data,false,QR_ECLEVEL_L,4);
    }

}