<?php 
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
//use \GatewayWorker\Lib\Gateway;
use \Workerman\Worker;
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;
use \GatewayWorker\Lib\Db;
require_once __DIR__.'/Function/functions.php';  
// 自动加载类
require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);
$task = new Worker('Text://127.0.0.1:8281');
$task -> name = 'DBWorker';
$task ->count = 8;
$task->onMessage = function($connection, $task_data)
{
    // 根据task_data去调用相应的业务逻辑，比如读数据库、redis、memcache等
	$task_data = json_decode($task_data, true);
	switch($task_data['function'])
	{
		//获取/消耗积分
		case 'score':
			$res = Db::instance('db')->insert('tbl_member_point')->cols($task_data['args'])->query();
			if($res==0)
			{
				//收藏成功
				$ret['code']	=		10014;
				$ret['mod']		=		'User';
				$ret['act']		=		'score';
				$task_result	=		array('ret'=>$ret,'data'=>'');
				$connection->send(json_encode($task_result));				
			}
			else
			{
				//收藏失败
				$ret['code']	=		10015;
				$ret['mod']		=		'User';
				$ret['act']		=		'score';			
				$task_result	=		array('ret'=>$ret,'data'=>'');
				$connection->send(json_encode($task_result));				
				
			}			
		break;
		//我的收藏列表
		case 'mycoll':
		$userid		 =	$task_data['args']['userid'];
		$sql="select * from tbl_member_fav where userid='$userid'";
		$collections =  Db::instance('db')->query($sql);
		echo json_encode($collections);
		break;
		//收藏
		case 'collection':
		$userid		=	$task_data['args']['userid'];
		$orderid	=	$task_data['args']['orderid'];
		$sql="select id from tbl_member_fav where userid='$userid' and orderid='$orderid'";
		$orderinfo	=	Db::instance('db')->single($sql);
		if($orderinfo)
		{
				//此信息已经被此用户收藏过
				$ret['code']	=		10013;
				$ret['mod']		=		'User';
				$ret['act']		=		'collection';			
				$task_result	=		array('ret'=>$ret,'data'=>'');
				$connection->send(json_encode($task_result));			
			
		}
		else
		{
			$res = Db::instance('db')->insert('tbl_member_fav')->cols($task_data['args'])->query();
			if($res==0)
			{
				//收藏成功
				$ret['code']	=		10012;
				$ret['mod']		=		'User';
				$ret['act']		=		'collection';
				$task_result	=		array('ret'=>$ret,'data'=>'');
				$connection->send(json_encode($task_result));				
			}
			else
			{
				//收藏失败
				$ret['code']	=		10013;
				$ret['mod']		=		'User';
				$ret['act']		=		'collection';			
				$task_result	=		array('ret'=>$ret,'data'=>'');
				$connection->send(json_encode($task_result));				
				
			}			
			
		}
				
		break;
		//信息浏览
		case 'ordervisit':
		$res = Db::instance('db')->insert('tbl_order_visit')->cols($task_data['args'])->query();
		if($res==0)
		{
				$ret['code']	=		10010;
				$ret['mod']		=		'User';
				$ret['act']		=		'ordervisit';
				$task_result	=		array('ret'=>$ret,'data'=>'');
				$connection->send(json_encode($task_result));				
		}
		else
		{
				$ret['code']	=		10011;
				$ret['mod']		=		'User';
				$ret['act']		=		'ordervisit';			
				$task_result	=		array('ret'=>$ret,'data'=>'');
				$connection->send(json_encode($task_result));				
			
		}		
		break;
		//发布消息 
		case 'publish':
		$res = Db::instance('db')->insert('tbl_order_content')->cols($task_data['args'])->query();
		if($res==0)
		{
				$ret['code']	=		10006;
				$ret['mod']		=		'User';
				$ret['act']		=		'publish';
				$task_result	=		array('ret'=>$ret,'data'=>'');
				$connection->send(json_encode($task_result));				
		}
		else
		{
				$ret['code']	=		10007;
				$ret['mod']		=		'User';
				$ret['act']		=		'publish';			
				$task_result	=		array('ret'=>$ret,'data'=>'');
				$connection->send(json_encode($task_result));				
			
		}
		
		break;
		//用户登录
		case 'login':
		$username 	=	$task_data['args']['username'];
		$password	=	md5($task_data['args']['password']);	
		$sql		=	"select id from tbl_members_base where username='$username' and password='$password'";
		$userinfo	=	Db::instance('db')->query($sql);
		if($userinfo)
		{
				//记录log开始
				$log['id']		=		MakeDbID();
				$log['userid']	=		$userinfo[0]['id'];
				$log['username']=		$task_data['args']['username'];
				$logtime		=		date('Y-m-d H:i:s',time());
				$log['logtime']	=		$logtime;
				$log['title']	=		'用户登录';
				$log['logtype']	=		1;
				$client_ip		=		$task_data['client_ip'];
				$log['content']	=		$task_data['args']['username'].',登录,ip:'.$client_ip.',登录时间:'.$logtime;
				Db::instance('db')->insert('tbl_members_log')->cols($log)->query();
				//记录log结束
				$ret['code']	=		10004;
				$ret['mod']		=		'User';
				$ret['act']		=		'login';
				$task_result	=		array('ret'=>$ret,'data'=>$userinfo[0]['id']);
				$connection->send(json_encode($task_result));
				
		}else
		{
				$ret['code']	=		10005;
				$ret['mod']		=		'User';
				$ret['act']		=		'login';
				$task_result	=		array('ret'=>$ret,'data'=>'');
				$connection->send(json_encode($task_result));				
		}		
		
		break;
		//用户注册
		case 'reg':
		$name		=		$task_data['args']['username'];
		$sql		=		"select id from tbl_members_base where username='$name'";
		$info=Db::instance('db')->query($sql);
		if($info)
		{
			$ret['code']	=		10001;
			$ret['mod']		=		'User';
			$ret['act']		=		'reg';			
			$task_result	=		array('ret'=>$ret,'data'=>'');
			$connection->send(json_encode($task_result));
			
		}
		else
		{
			$res = Db::instance('db')->insert('tbl_members_base')->cols($task_data['args'])->query();
			if($res==0)
			{
				//记录tbl_members_log
				$log['id']		=		MakeDbID();
				$log['userid']	=		$task_data['args']['id'];
				$log['username']=		$task_data['args']['username'];
				$logtime		=		date('Y-m-d H:i:s',time());
				$log['logtime']	=		$logtime;
				$log['title']	=		'用户注册';
				$log['logtype']	=		0;
				$client_ip		=		$task_data['client_ip'];
				$log['content']	=		$task_data['args']['username'].',注册,ip:'.$client_ip.',注册时间:'.$logtime;
				Db::instance('db')->insert('tbl_members_log')->cols($log)->query();
				//记录tbl_members_log结束
				$ret['code']	=		10002;
				$ret['mod']		=		'User';
				$ret['act']		=		'reg';	
				$task_result	=		array('ret'=>$ret,'data'=>'');
			    $connection->send(json_encode($task_result));		
				
			}
			else
			{
				$ret['code']	=		10003;
				$ret['mod']		=		'User';
				$ret['act']		=		'reg';
				$task_result	=		array('ret'=>$ret,'data'=>'');
			    $connection->send(json_encode($task_result));				
							
			}
		}
		break;
		//审核用户的发布信息
		case 'verify':
			$id=$task_data['args']['id'];
			$onlinetime=$task_data['args']['onlinetime'];
			if(empty($onlinetime))//审核时间为空时，默认审核，通知给所有在线用户
			{
				 $sql="update tbl_order_content set isactive=1 where id='$id'";
				 $res=Db::instance('db')->query($sql);
				 if($res)
				 {
					$sql="select title,content,price,img from tbl_order_content where id='$id'";
					$data=Db::instance('db')->query($sql);
					$ret['code']	=		10008;
					$ret['mod']		=		'Admin';
					$ret['act']		=		'verify';
					$task_result	=		array('ret'=>$ret,'data'=>$data[0]);
					$connection->send(json_encode($task_result));
				 }
				 else
				 {
					$ret['code']	=		10009;
					$ret['mod']		=		'Admin';
					$ret['act']		=		'verify';
					$task_result	=		array('ret'=>$ret,'data'=>'');
					$connection->send(json_encode($task_result));
					 
				 }
			}
			else
			{
				//审核时间不为空时，定时审核，修改审核状态和onlintime值
				$sql="update tbl_order_content set isactive=1,onlinetime='$onlinetime' where id='$id'";
				$res=Db::instance('db')->query($sql);
				if($res)
				 {
					$ret['code']	=		10008;
					$ret['mod']		=		'Admin';
					$ret['act']		=		'verify';
					$task_result	=		array('ret'=>$ret,'data'=>'');
					$connection->send(json_encode($task_result));
				 }
				 else
				 {
					$ret['code']	=		10009;
					$ret['mod']		=		'Admin';
					$ret['act']		=		'verify';
					$task_result	=		array('ret'=>$ret,'data'=>'');
					$connection->send(json_encode($task_result));
					 
				 }
				
			}
		
		break;
		
		
		
	}


};
// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

