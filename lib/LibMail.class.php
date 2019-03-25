<?php
/**
 * 邮件发送通用类
 * Created by PhpStorm.
 * User: john
 * Date: 2015/10/18
 * Time: 22:54
 *
 * $demo=new LibMail();
 * $demo->send();
 */
class LibMail{
    static private $libLoaded = false;
    static private $config;
    private $smtpServer = '';
    public $error;
    public $serverUser;
    public  function __constrcut(){
        if (self::$libLoaded) {
            return;
        }
        self::$libLoaded = true;
        include PATH_CORE_EXTEND . "/phpmailer/class.phpmailer.php";
    }
    private function connect($configName){
        $mailConfigPath=PATH_CUR_APP."/config/IncMail.class.php";
        if(!is_file($mailConfigPath)){
            //优先以用户配置的为准
            $mailConfigPath =PATH_ROOT."/config/IncMemcache.class.php";
        }
        if(is_file($mailConfigPath)){
            self::$config=IncMail::getConfig($configName);
        }else{
            throw new RuntimeException("memcahe配置文件找不到",CONFIG_ERROR);
        }
        $this->serverUser = self::$config['smtp_auth_user'];
        $this->smtpServer = new PHPMailer();
        $this->smtpServer->IsSMTP();
        $this->smtpServer->Host       = self::$config['smtp_host'];
        $this->smtpServer->Port       = self::$config['smtp_port'] ? self::$config['smtp_port'] : 25;
        $this->smtpServer->CharSet = self::$config['charset'] ?self::$config['charset']:'utf-8';
        if(self::$config['smtp_host'] == 'smtp.gmail.com'){
                $this->smtpServer->SMTPSecure = "ssl";
                $this->smtpServer->SMTPAuth = true;
                $this->smtpServer->CharSet = "UTF-8";
                $this->smtpServer->Encoding = "base64";
        }
        if(self::$config['secure']){
          $this->smtpServer->SMTPSecure = self::$config['secure'];
        }
        if(self::$config['smtp_auth_user'] && self::$config['smtp_auth_password']){
                $this->smtpServer->SMTPAuth   = true;
                $this->smtpServer->Username   = self::$config['smtp_auth_user'];
                $this->smtpServer->Password   = self::$config['smtp_auth_password'];
        }
    }

    /**
     * 发送邮件的函数
     * @param $to
     * @param $from
     * @param $title
     * @param $body
     * @param array $attachments
     * @param string $config
     * @return bool
     */
    public  function send($to, $from, $title, $body, $attachments = array(),$config='default'){
        if(empty($this->smtpServer)){
            $this->connect($config);
        }
        $this->smtpServer->ClearAddresses();
        $this->smtpServer->AddAddress($to);
        $this->smtpServer->From = $this->serverUser;
        $this->smtpServer->FromName = $from ? $from : $this->serverUser;
        $this->smtpServer->Subject = $title;
        $this->smtpServer->Body = $body;
        $this->smtpServer->WordWrap = 50; // set word wrap
        $this->smtpServer->IsHTML(true); // send as HTML

        if(empty($attachments)){
            foreach($attachments as $attachment){
                $this->smtpServer->AddAttachment($attachment);      // attachment
            }
        }
        try{
            $re = $this->smtpServer->Send();
            return $re;
        } catch (phpmailerException $e) {
            $this->error = $e->errorMessage();
            return false;
        }

    }

}
