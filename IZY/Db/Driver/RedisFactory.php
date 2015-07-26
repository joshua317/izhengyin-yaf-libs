<?php
/**
 * @date 2014-6-19
 * @author zhengyin
 * @email zhengyin.name@gmail.com
 * redis 工厂驱动
 */
namespace IZY\Db\Driver;
class RedisFactory
{
	private  static $redis = array();
	/**
	 * redis 实例化
	 * @param  $name		 连接那个redis
	 * @param  $pconnect 是否进行长连接
	 * @param  $ping     是否进行 ping 检测
	 */
	public static function instance($name,$pconnect=false,$ping=false)
	{
		$redisConf = self::getRedisConf($name);
		
		if(empty($redisConf)){
			throw new \Exception('redis config ['.$name.'] not defiend!');
		}
		
		$dbIndex = isset($redisConf['dbIndex'])?intval($redisConf['dbIndex']):0;
		
		$redis = !empty(self::$redis[$name][$dbIndex])?self::$redis[$name][$dbIndex]:null;
		
		//检查 redis 是否可用
		if($ping === true && $redis !== null){
			
			try {
				if(!$redis->ping()){
					throw new \RedisException('PING Error');
				}
			}catch (\Exception $e){
				$redis = null;
			}
		}
		
		//redis 对象为空连接redis
		if($redis === null){
			try {
				$redis = self::connect($redisConf,$dbIndex,$pconnect);
				$redis->select($dbIndex);
			}catch (\RedisException $e){
				throw new \RedisException('Unable to connect to Redis ['.$redisConf['host'].':'.$redisConf['prot'].'] errMsg:'.$e->getMessage());
			}
			self::$redis[$name][$dbIndex] = $redis;
		}
		return $redis;
	}
	
	/**
	 * 连接 redis
	 * @param  $redisConf
	 */
	private static function connect($redisConf,$pconnect)
	{
		
		$redis = new \Redis();
		
		if($pconnect){
			$redis->pconnect($redisConf['host'],$redisConf['prot']);
		}else{
			$redis->connect($redisConf['host'],$redisConf['prot']);
		}
		return $redis;
	}
	
	/**
	 * 获取redis配置项
	 * @param  $name		redis节点名称
	 */
	private static function getRedisConf($name)
	{
		$conf = \Yaf\Application::app()->getConfig()->redis->toArray();
		if(!empty($conf[$name]) && is_array($conf[$name]))
		{
			return $conf[$name];
		}
		throw new \RedisException('reidsConf['.$name.'] is not defined!');
	}
}