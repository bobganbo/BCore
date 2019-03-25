<?php
/**
 * 验证码生成通用类
 * Created by PhpStorm.
 * User: john
 * Date: 2015/10/18
 * Time: 22:45
 */

class LibVcode{
    private static $len=4;//默认二维码长度为4
    public static function make($len=0){
        self::$len = $len==0?self::$len:$len;
        $str = "ABCDEFGHIJKLNMPQRSTUVWXYZ123456789";//数字和英文大写字母生成的验证码
        $im = imagecreatetruecolor ( 80*2, 30*1);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $bgtxt = imagecolorallocate($im, 220, 220, 220);
        //随机调色板
        $colors = array(
            imagecolorallocate($im, 255, 0, 0),
            imagecolorallocate($im, 0, 200, 0),
            imagecolorallocate($im, 0, 0, 255),
            imagecolorallocate($im, 0, 0, 0),
            imagecolorallocate($im, 255, 128, 0),
            imagecolorallocate($im, 255, 208, 0),
            imagecolorallocate($im, 98, 186, 245),
        );
        //填充背景色
        imagefill($im, 0, 0, $bgc);
        //随机获取数字
        $verify = "";
        while (strlen($verify) < self::$len) {
            $i = strlen($verify);
            $random = $str[rand(0, strlen($str))];
            $verify .= $random;
            //绘制背景文字
            imagestring($im, 76, ($i*10)+9, rand(0,12), $random, $bgtxt);
            //绘制主文字信息
            imagestring($im, 76, ($i*10)+9, rand(0,12), $random, $colors[rand(0, count($colors)-1)]);
        }
        //添加随机杂色
        for($i=0; $i<100; $i++) {
            $color = imagecolorallocate($im, rand(50,220), rand(50,220), rand(50,220));
            imagesetpixel($im, rand(0,70), rand(0,20), $color);
        }
        //将验证码存入$_SESSION中
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['validate'] = strtolower($verify);//生成验证码
        //输出图片并释放缓存
        header('Content-type: image/png');
        imagepng($im);
        imagedestroy($im);
    }


    public static function vCode($num=4,$size=20, $width=0,$height=0){
        !$width && $width = $num*$size*4/5+5;
        !$height && $height = $size + 10;
        // 去掉了 0 1 O l 等
        $str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVW";
        $code = '';
        for ($i=0; $i<$num; $i++){
            $code.= $str[mt_rand(0, strlen($str)-1)];
        }
        // 画图像
        $im = imagecreatetruecolor($width,$height);
        // 定义要用到的颜色
        $back_color = imagecolorallocate($im, 235, 236, 237);
        $boer_color = imagecolorallocate($im, 118, 151, 199);
        $text_color = imagecolorallocate($im, mt_rand(0,200), mt_rand(0,120), mt_rand(0,120));

        // 画背景
        imagefilledrectangle($im,0,0,$width,$height,$back_color);
        // 画边框
        imagerectangle($im,0,0,$width-1,$height-1,$boer_color);
        // 画干扰线
        for($i=0;$i<5;$i++){
            $font_color = imagecolorallocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
            imagearc($im,mt_rand(-$width,$width),mt_rand(-$height,$height),mt_rand(30,$width*2),mt_rand(20,$height*2),mt_rand(0,360),mt_rand(0,360),$font_color);
        }
        // 画干扰点
        for($i=0;$i<50;$i++){
            $font_color = imagecolorallocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
            imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$font_color);
        }
        // 画验证码
        @imagefttext($im, $size , 0, 5, $size+3, $text_color, PATH_ROOT."/app/User/static/fonts/simsunb.ttf",$code);
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['validate'] = strtolower($code);//生成验证码
        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-type: image/png");
        imagepng($im);
        imagedestroy($im);
    }

    /**
     * 对用户输入的验证码进行校验
     * @param $code 用户输入的验证码
     * @return bool 验证返回值
     */
    public static function verify($code){
        if(!$code)return false;
        if (!isset($_SESSION)) {
            session_start();
        }
        $vCode=$_SESSION['validate'];
        return $vCode==strtolower($code)?true:false;
    }
}