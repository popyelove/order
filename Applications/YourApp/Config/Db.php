<?php
namespace Config;
/**
 * mysql����
 * @author walkor
 */
class Db
{
    /**
     * ���ݿ��һ��ʵ�����ã���ʹ��ʱ����������ʹ��
     * $user_array = Db::instance('user')->select('name,age')->from('users')->where('age>12')->query();
     * �ȼ���
     * $user_array = Db::instance('user')->query('SELECT `name`,`age` FROM `users` WHERE `age`>12');
     * @var array
     */
    public static $db = array(
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'user'    => 'root',
        'password' => 'lichao',
        'dbname'  => 'lichao',
        'charset'    => 'utf8',
    );

}