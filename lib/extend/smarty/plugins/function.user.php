<?php
/**
 * 自定义用户信息插件
 * Created by PhpStorm.
 * User: john
 * Date: 2016/1/5
 * Time: 23:21
 * 使用方法
 *
 *
 */

function smarty_function_user($params, $template)
{
    $uid=$params['uid'];
    $type=$params['type'];

    switch($type){
        case 'avatar':
            $str='/home/static/images/userPicture.png';
            break;
        case 'nickname':
            $str='cjd19911611111';
            break;
        case 'profile':
            $str='分享快乐';
        case 'medal':
            $str=5;//勋章
        case 'note':
            $str=10;//帖子
        case 'fans':
            $str=68;
        case 'attention':
            $str=15;
        default:
            $str='';
    }
    return $str;
}