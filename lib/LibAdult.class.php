<?php
/**
 * @name 判断是否成年
 * Created by PhpStorm.
 * User: bo
 * Date: 2016/12/15
 * Time: 10:37
 */
class LibAdult{
    /**
     * 通过身份证号码判断是否成年
     * @param $idnumber 身份证号
     */
    public static function isAdult($idnumber){
        define("MORE18",1);//成年
        define("LESS18",0);//未成年
        define("UNKOWN",-1);//未知
        if ($idnumber) {
            if (self::validIdNumber($idnumber)){
                $isAdult = self::getIsAdult($idnumber);
                if ($isAdult){
                    return MORE18;
                } else {
                    return LESS18;
                }
            }else {
                return LESS18;
            }
        } else {
            return UNKOWN;
        }
    }

    //验证身份证有效性
    private  static function validIdNumber($idnumber){
        $result = false;
        if (strlen($idnumber)== 15){
            $idnumber = self::idcard_15to18($idnumber);
        }
        if (strlen($idnumber)== 18){
            $idnumber_base = substr($idnumber, 0, 17);
            if (self::idcard_verify_number($idnumber_base) == strtoupper(substr($idnumber, 17, 1))){
                if (self::idcard_verify_province($idnumber)) {//验证省区编码
                    $birth_year = intval(substr($idnumber,6,4));
                    $birth_month = intval(substr($idnumber,10,2));
                    $birth_day = intval(substr($idnumber,12,2));
                    if (checkdate($birth_month,$birth_day,$birth_year)){  //验证出生年月
                        $result =true;
                    }
                }
            }
        }
        return $result;
    }

    //验证身份证后，再判断成年
    private  static function getIsAdult($idnumber){
        if (strlen($idnumber)== 15){
            $idnumber = self::idcard_15to18($idnumber);
        }
        $birthday = substr($idnumber,6,8);
        $before18year = date('Ymd',mktime(0, 0, 0,date("m"),date("d"),date("Y")-18));    //18年前
        if ( strtotime($birthday) <= strtotime($before18year) ){
            return true;
        }else {
            return false;
        }
    }

    //返回身份证最后一位校验码
    private static function idcard_verify_number($idcard_base){
        if (strlen($idcard_base) != 17){ return false; }
        // 加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

        // 校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++){
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }

        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];

        return $verify_number;
    }

    //将15位身份证升级到18位
    private static function idcard_15to18($idcard){
        if (strlen($idcard) != 15){
            return false;
        }else{
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){
                $idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);
            }else{
                $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
            }
        }

        $idcard = $idcard . idcard_verify_number($idcard);

        return $idcard;
    }

    //省区判断规则，本方法以身份证最后一位校验为前提
    //测试错误地区码身份证881111198310033218
    private static function idcard_verify_province($idcardnumber){
        $arrProvince = array(
            "11" => "北京",
            "12" => "天津",
            "13" => "河北",
            "14" => "山西",
            "15" => "内蒙古",
            "21" => "辽宁",
            "22" => "吉林",
            "23" => "黑龙江",
            "31" => "上海",
            "32" => "江苏",
            "33" => "浙江",
            "34" => "安徽",
            "35" => "福建",
            "36" => "江西",
            "37" => "山东",
            "41" => "河南",
            "42" => "湖北",
            "43" => "湖南",
            "44" => "广东",
            "45" => "广西",
            "46" => "海南",
            "50" => "重庆",
            "51" => "四川",
            "52" => "贵州",
            "53" => "云南",
            "54" => "西藏",
            "61" => "陕西",
            "62" => "甘肃",
            "63" => "青海",
            "64" => "宁夏",
            "65" => "新疆",
            "71" => "台湾",
            "81" => "香港",
            "82" => "澳门",
            "91" => "国外"
        );
        if (array_key_exists(substr($idcardnumber,0,2),$arrProvince)){
            return true;
        } else {
            return false;
        }
    }
}