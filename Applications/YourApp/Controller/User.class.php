<?php
use \GatewayWorker\Lib\Gateway;
use \Workerman\Worker;
use \Workerman\Connection\AsyncTcpConnection;
use \GatewayWorker\Lib\Store;
use \Applications\YourApp\Controller\MyWm;
require_once __DIR__.'/../Function/functions.php';  
class User extends MyWm{
	/******************
	**注册
	******************/
	public function reg($args,$client_id)//注册接口
	{
		require(__DIR__.'/../Config/members_base.php');
		$args['id']			=		MakeDbID();
		$args['password']	=		md5($args['password']);
		$args['usertype']	=		$tbl_members_base['usertype'];
		$args['money']		=		$tbl_members_base['money'];
		$args['point']		=		$tbl_members_base['point'];
		$client_ip			=		ClinentIp();
		$task = new AsyncTcpConnection('Text://127.0.0.1:8281');
		$task->connect();
		$task_data		=	array(
			'function'=>'reg',
			'args'=>$args,
			'client_ip'=>$client_ip,
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
	/******************
	**登录
	******************/
	public	function login($args,$client_id)
	{
		$task = new AsyncTcpConnection('Text://127.0.0.1:8281');
		$task->connect();
		$client_ip			=		ClinentIp();
		$task_data		=	array(
			'function'=>'login',
			'args'=>$args,
			'client_ip'=>$client_ip,
		 );
		 			 
		$task -> send(json_encode($task_data));
		$task -> onMessage = function ($task , $task_result) use($client_id)
		 {
			$send_result=json_decode($task_result,true);
			if($send_result['ret']['code']==10004)
			{
				 Gateway::bindUid($client_id,$send_result['data']);
				 $store = Store::instance('user');
				 $key	= 'wm_userid_'.$client_id;
				 $store->select(1);
				 $store->set($key, $send_result['data']); 
				 
			}	
			
			echo  json_encode($send_result['ret']);
			$task -> close();
			
		};

			
		
	}
	/******************
	**发布消息
	******************/
	public function publish($args,$client_id)
	{
		require(__DIR__.'/../Config/order_content.php');
		$store = Store::instance('user');
		$key	= 'wm_userid_'.$client_id;
		$store->select(1);
		$publishuserid			=	    $store->get($key);
		$id						=		MakeDbID();		
		$args['id']				=		$id;
		$args['publishuserid']	=		$publishuserid;		
		$args['publishtime']	=		$tbl_order_content['publishtime'];
		$args['onlinetime']		=		$tbl_order_content['onlinetime'];
		$args['publishurl']		=		$tbl_order_content['publishurl'];
		$args['sourceurl']		=		$tbl_order_content['sourceurl'];
		$args['oururl']			=		$tbl_order_content['oururl'];
		$args['propid']			=		$tbl_order_content['propid'];
		$args['pvnum']			=		$tbl_order_content['pvnum'];		
		$args['joinnum']		=		$tbl_order_content['joinnum'];		
		$args['favnum']			=		$tbl_order_content['favnum'];		
		$args['isactive']		=		$tbl_order_content['isactive'];		
		$args['isontop']		=		$tbl_order_content['isontop'];
		$args['sortby']			=		$tbl_order_content['sortby'];
		
		$task = new AsyncTcpConnection('Text://127.0.0.1:8281');
		$task->connect();
		$task_data		=	array(
			'function'=>'publish',
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
	public function say($args,$client_id)
	{
		
		 Gateway::sendToUid('2015100417383451511','i love you');
		
	}
	/******************
	**浏览消息
	******************/
	public function ordervisit($args,$client_id)
	{
		$store=Store::instance('user');
		$key		=	'wm_userid_'.$client_id;
		$store->select(1);
		$args['id']			=	MakeDbID();	
		$args['userid']		=	$store->get($key); 
		$args['visittime']	=	date('Y-m-d H:i:s',time());
		
		$task = new AsyncTcpConnection('Text://127.0.0.1:8281');
		$task->connect();
		$task_data		=	array(
			'function'=>'ordervisit',
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
	/******************
	**收藏
	******************/
	public function collection($args,$client_id)
	{
		$store=Store::instance('user');
		$key		=	'wm_userid_'.$client_id;
		$store->select(1);
		$args['id']			=	MakeDbID();	
		$args['userid']		=	$store->get($key); 
		$args['orderid']	=	$args['orderid'];
		
		$task = new AsyncTcpConnection('Text://127.0.0.1:8281');
		$task->connect();
		$task_data		=	array(
			'function'=>'collection',
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
	/******************
	**我的收藏列表
	******************/
	public function mycoll($args,$client_id)
	{
		$store=Store::instance('user');
		$key		=	'wm_userid_'.$client_id;
		$store->select(1);
		$args['userid']		=	$store->get($key); 	
		$task = new AsyncTcpConnection('Text://127.0.0.1:8281');
		$task->connect();
		$task_data		=	array(
			'function'=>'mycoll',
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
	/******************
	**获取，消耗积分
	******************/
	public function score($args,$client_id)
	{
		require(__DIR__.'/../Config/tbl_member_point.php');
		$store=Store::instance('user');
		$key		=	'wm_userid_'.$client_id;
		$store->select(1);
		$args['id']					=	MakeDbID();	
		$args['userid']		=	$store->get($key);
		$args['pointtime']	=	date('Y-m-d H:i:s',time());
		$args['pointvalue']	=	$tbl_member_point['pointvalue'];
		$args['isactive']	=	$tbl_member_point['isactive'];
        		
		$task = new AsyncTcpConnection('Text://127.0.0.1:8281');
		$task->connect();
		$task_data		=	array(
			'function'=>'score',
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
