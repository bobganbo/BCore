<?php
/**
 * 模型基类
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/16
 * Time: 13:55
 */
class Model{
    function __get($name) {
        // 自动加载db,单例 $this->dbUser->getOne($sql);
        if(substr($name, 0,2) =='db'){
            $config = strtolower(substr($name, 2));
            // 只初始化一个LibDatabase实例
            if(empty($this->objDb)){
                $this->objDb = new LibMysql($config);
            }else{
                $this->objDb->config($config);
            }
            return $this->objDb;
        }
        throw new LogicException("变量{$name}不被支持,请预先在Model中定义",CORE_ERROR);
    }





}