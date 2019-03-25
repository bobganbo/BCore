<?php

/**
 * 用户信息相关扩展
 * @param $uid 用户id
 * @param $type 类型
 * @param $params 附加参数
 * @return mixed
 */						      	
function smarty_modifier_user($uid,$type,$params = array()){
    include dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/BCore/core.php';//加载核心文件
    $mod=new LibUser();
    switch($type){
        case 'avatar':
            $str=$mod->userBaseInfo($uid,'avatar_id');
            break;
        case 'nickname':
            $str=$mod->userBaseInfo($uid,'nickname');
            break;
        case 'profile':
            $str=$mod->userBaseInfo($uid,'profile');
	    break;

        case 'medal':
            $str=$mod->userMedal($uid);//勋章
	    break;

        case 'note':
            $str=$mod->userNotes($uid);

	    break;

        case 'fans':
            $str=$mod->userFans($uid);
	    break;

        case 'attention':
            $str=$mod->userAttentions($uid);
	    break;

        case 'pic':
            $str=$mod->userPics($uid);
            break;
        default:
            $str='';
    }
	return $str;
}

?>
