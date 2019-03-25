<?php
/**
 * @name 简单的消息队列
 * Created by PhpStorm.
 * User: bo
 * Date: 2016/7/7
 * Time: 15:39
 */
/*
----------------------------------------------------------------------------------------------------------------
HTTP Simple Queue Service - httpsqs client class for PHP v1.7.1

Author: Zhang Yan (http://blog.s135.com), E-mail: net@s135.com
This is free software, and you are welcome to modify and redistribute it under the New BSD License
----------------------------------------------------------------------------------------------------------------
Useage:
<?php
include_once("httpsqs_client.php");
$httpsqs = new httpsqs($httpsqs_host, $httpsqs_port, $httpsqs_auth, $httpsqs_charset);
$result = $httpsqs->put($queue_name, $queue_data); //1. PUT text message into a queue. If PUT successful, return boolean: true. If an error occurs, return boolean: false. If queue full, return text: HTTPSQS_PUT_END
$result = $httpsqs->get($queue_name); //2. GET text message from a queue. Return the queue contents. If an error occurs, return boolean: false. If there is no unread queue message, return text: HTTPSQS_GET_END
$result = $httpsqs->gets($queue_name); //3. GET text message and pos from a queue. Return example: array("pos" => 7, "data" => "text message"). If an error occurs, return boolean: false. If there is no unread queue message, return: array("pos" => 0, "data" => "HTTPSQS_GET_END")
$result = $httpsqs->status($queue_name); //4. View queue status
$result = $httpsqs->status_json($queue_name); //5. View queue status in json. Return example: {"name":"queue_name","maxqueue":5000000,"putpos":130,"putlap":1,"getpos":120,"getlap":1,"unread":10}
$result = $httpsqs->view($queue_name, $queue_pos); //6. View the contents of the specified queue pos (id). Return the contents of the specified queue pos.
$result = $httpsqs->reset($queue_name); //7. Reset the queue. If reset successful, return boolean: true. If an error occurs, return boolean: false
$result = $httpsqs->maxqueue($queue_name, $num); //8. Change the maximum queue length of per-queue. If change the maximum queue length successful, return boolean: true. If  it be cancelled, return boolean: false
$result = $httpsqs->synctime($num); //9. Change the interval to sync updated contents to the disk. If change the interval successful, return boolean: true. If  it be cancelled, return boolean: false
?>
----------------------------------------------------------------------------------------------------------------
*/
class LibHttpsqs{












}