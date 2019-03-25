<?php
/**
 * 文件打包压缩和解压类
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/17
 * Time: 11:12
 */
class LibZip{
    /**
     * 压缩
     * @param $dir 需要压缩的目录名称
     * @param $zipName 压缩包的名字
     * @param $replacePath 需要替换为空的路径字符串
     * @desc  压缩文件数过多，可能会导致慢的问题
     * ------demo---------
     * $dir=WEB_ROOT.'/logs/admpublish.demo.com';
     * $replacePath=WEB_ROOT.'/logs';
     * $zipName='test';
     * LibZip::zip($dir,$zipName,$replacePath);
     * ------demo---------
     */
     public static function zip($dir,$zipName,$replacePath){
         $zip = new ZipArchive;
         if(is_dir($dir)){
             $dir .= DIRECTORY_SEPARATOR;
             $dir = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $dir);
             $nodes = glob($dir."*");//返回匹配指定模式的文件名或目录,这里匹配所有
             foreach($nodes as $node){
                 if(is_dir($node))self::zip($node,$zipName,$replacePath);
                 else{
                     if($zip->open($zipName, ZipArchive::CREATE)){
                         $zip->addFile($node, str_replace($replacePath, "", $node));//第一个参数：文件绝对路径，第二个参数：相对压缩包根路径的文件路径
                     }
                 }
             }
         }else{
             if($zip->open($zipName, ZipArchive::CREATE)){
                 $zip->addFile($dir, str_replace($replacePath, "", $dir));
             }
         }
         $zip->close();//关闭
     }

    /**
     * 解压缩
     * @param $zipFile 压缩文件路径
     * @param $dstDir  解压到的目标目录
     */
     public static function unZip($zipFile,$dstDir){
         $zip = new ZipArchive;//新建一个ZipArchive的对象
         if($zip->open($zipFile)===true){
              return $zip->extractTo($dstDir);//将Zip文件解压到指定的目录,返回true or false
         }
         return false;
     }

}
