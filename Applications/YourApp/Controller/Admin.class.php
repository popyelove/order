<?php
use \GatewayWorker\Lib\Gateway;
use \Workerman\Worker;
use \Workerman\Connection\AsyncTcpConnection;
use \Applications\YourApp\Controller\MyWm;
use \Applications\YourApp\Database\MySql; 
require_once __DIR__.'/../Function/functions.php'; 
class Admin extends MyWm{
	public function verify($args,$client_id)
	{	
	     $task = new AsyncTcpConnection('Text://127.0.0.1:8281');
         $task->connect();
		 $task_data		=	array(
			'function'=>'verify',
			'args'=>$args,
		 );
         $task -> send(json_encode($task_data));
         $task -> onMessage = function ($task , $task_result) use($client_id)
		 {
			$send_result=json_decode($task_result,true);
			$new_message=$send_result['data'];
			if(!empty($new_message))
			{
			   Gateway::sendToAll(json_encode($new_message));
			}
			echo  json_encode($send_result['ret']);
			$task -> close();
			
         };

	}
	

}
?>
