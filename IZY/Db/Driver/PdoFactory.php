<?php
/**
 * @date 2014-8-25
 * @author zhengyin <zhengyin.name@gmail.com>
 * PDO 工厂驱动
 */
namespace IZY\Db\Driver;
class PdoFactory
{
	private static $instanceObj = array();
	
	/**
	 * 取得实例
	 * @param  $name 实例名称
	 * @param  $pconnect 是否为长连接 [true,false]
	 * @param  $ping 是否进行连接检查
	 */
	
	public static function instance($name,$pconnect=false,$ping=false)
	{
		try {
			//检查是否可用
			if(self::ping($name,$ping))
				return self::$instanceObj[$name];
		}catch (\Exception $e){}
		
		//取得配置项
		$dbConf = self::getDbConf($name);
		$dsn = "mysql:host={$dbConf['host']};port={$dbConf['port']};dbname={$dbConf['database']}";
		$username = $dbConf['user'];
		$passwd = $dbConf['password'];
		$charset = $dbConf['charset'];
		
		$options = array(
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_PERSISTENT => (bool)$pconnect
		);
		$pdo = new \PDO($dsn,$username,$passwd,$options);
		$pdo->query('SET NAMES '.$charset);
		self::$instanceObj[$name] = $pdo;
		return self::$instanceObj[$name];
	}
	
	/**
	 * 检查连接是否有效
	 * @param  $name	 操作的库
	 */
	private static function ping($name,$ping)
	{
		if(isset(self::$instanceObj[$name])){
			if($ping && self::$instanceObj[$name]->ping()){
				return true;
			}else{
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 * 获取数据库配置项
	 * @param  $name		操作的库
	 */
	private static function getDbConf($name)
	{
		$conf = \Yaf\Application::app()->getConfig()->db->toArray();
		
		if(!empty($conf[$name]) && is_array($conf[$name]))
		{
			return $conf[$name];
		}
		throw new \PdoException('dbConf['.$name.'] is not defined!');
	}	
}