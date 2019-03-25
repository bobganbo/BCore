<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */
 
/**
 * Smarty security_show modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     security_show<br>
 * 
 */
function smarty_modifier_security_show($string)
{
    $_p = strpos($string, '@');
    $emailLeft = '';
    if($_p){
        $emailLeft = substr($string, $_p);
        $string = substr($string, 0,$_p);
    }
    $length = strlen($string);
    if ($length == 0){
        return '';
    }
    $start = ceil(0.2*$length);
    $repeatTime = ceil(0.5*$length);
    $return = substr($string,0,$start);
    $return.=str_repeat("*", $repeatTime);
    $left = $length - strlen($return);
    if($left){
       $return.=substr($string, -1*$left); 
    }
    $return .=$emailLeft;
    
    return $return;
} 

?>