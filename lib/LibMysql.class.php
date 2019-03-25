<?php
/**
 * mysql数据库封装类
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/16
 * Time: 17:17
 */
class LibMysql{

    private $config;
    private $configName;
    private $masterIgnore;
    private $masterForce = false;

    private $query;
    private $queryCount;

    private static $connections;

    public function __destruct(){
        if(is_resource($this->config))mysql_close($this->connection);
    }

    public function config($dbConfig){
        if($dbConfig==''){
             $dbConfig='default';
        }
        if (isset($GLOBALS['config']['db'][$dbConfig])) {
            $this->config = $GLOBALS['config']['db'][$dbConfig];
            $this->configName = $dbConfig;
            return;
        }

        $dbConfigPath=PATH_CUR_APP."/config/IncDb.class.php";
        if(!is_file($dbConfigPath)){
            $dbConfigPath=PATH_INC."/IncDb.class.php";
        }
        if(is_file($dbConfigPath)){
            $this->config=$GLOBALS['config']['db'][$dbConfig]=IncDb::getConfig($dbConfig);
            $this->configName = $dbConfig;
        }else{
            throw new RuntimeException("{$dbConfig} Not Found",CONFIG_ERROR);
        }

    }

    private function connect($isMaster = false){

        if( isset($this->config['master'])){
            $this->masterIgnore = false;
        }
        else{
            $this->masterIgnore = true;
        }
        if($this->masterIgnore){
            if (empty(self::$connections[$this->configName])) {

                self::$connections[$this->configName] = mysql_connect($this->config['host'], $this->config['user'], $this->config['password']);
            }
            return self::$connections[$this->configName];
        }
        else{
            if ($this->masterForce || $isMaster) {
                if (empty(self::$connections[$this->configName . 'w'])) {
                    self::$connections[$this->configName . 'w'] = mysql_connect($this->config['master']['host'], $this->config['master']['user'], $this->config['master']['password']);
                }
                return self::$connections[$this->configName . 'w'];
            } else {
                if (empty(self::$connections[$this->configName . 'r'])) {
                    $_host = $this->config['slave']['host'][ip2long($_SERVER['REMOTE_ADDR']) % count($this->config['slave']['host'])];
                    self::$connections[$this->configName . 'r'] = mysql_connect($_host, $this->config['slave']['user'], $this->config['slave']['password']);
                }
                return self::$connections[$this->configName . 'r'];
            }
        }
    }

    public function query($sql){
        if (substr(strtoupper($sql), 0, 1) === 'S') {
            return $this->getAll($sql);
        }
        else{
            return $this->excute($sql);
        }
    }

    public function getOne($sql){
        if (substr(strtoupper(trim(preg_replace('/\s{2,}/', ' ', $sql))), -7, 7) != 'LIMIT 1') {
            $sql .=' LIMIT 1';
        }
        $query = $this->excute($sql);
        return mysql_fetch_array($query, MYSQL_ASSOC);
    }

    public function getAll($sql,$is_master=false,$type=MYSQL_ASSOC){
        $query = $this->excute($sql, $is_master);
        $row = $rows = array();
        while ($row = mysql_fetch_array($query, $type)) {
            $rows[] = $row;
        }
        mysql_free_result($query);
        return $rows;
    }

    public function excute($sql){
        $sql = trim($sql);
        if(!$this->masterIgnore){

            if (substr(strtoupper($sql), 0, 1) === 'S') {
                $link = $this->connect();
            } else {
                $link = $this->connect(true);
            }
        }
        else{
            $link = $this->connect();
        }

        if (isset($this->config['db'])) {
            if (!mysql_select_db($this->config['db'], $link)) {
                throw new RuntimeException('数据库' . $this->config['db'] . '配置找不到',DB_ERROR);
            }
        }

        mysql_set_charset('utf8',$link);
        $this->query = mysql_query($sql, $link);
        if ($this->query === false) {
            throw new RuntimeException('sql语句出错:' . mysql_error() . ':' . $sql, DB_ERROR);
        }
        return $this->query;

    }

    public function getAffectedRows(){
        return mysql_affected_rows($this->connect(true));
    }

    public function getLastInsertId() {
        return mysql_insert_id($this->connect(true));
    }
}