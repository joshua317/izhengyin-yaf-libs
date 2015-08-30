<?php
/**
 * redis session处理机制
 * @date 2014-6-18 
 * @author 郑印 <zhengyin.name@gmail.com>
 * @blog http://izhengyin.com
 * 
 */
namespace IZY\Syc\Unit;
class RedisSession
{
	private $reids;
	private $sessionName;
	public function __construct($redis,$sessionName='')
	{
		$this->redis = $redis;
		session_set_save_handler(
			array($this,'open'),
			array($this,'close'),
			array($this,'read'),
			array($this,'write'),
			array($this,'destroy'),
			array($this,'gc')
		);
		if(!empty($sessionName)){
			session_name($sessionName);
		}
	}
	/**
	 * session_start()
	 * @param  $path
	 * @param  $sessionName
	 */
	public function open($path,$sessionName)
	{
		$this->sessionName = $sessionName;
	}
	
	public function close()
	{
		$this->redis->close();
		return true;
	}
	
	public function read($sessionId)
	{
		$value = $this->redis->hget($this->sessionName,$sessionId);
		return $value?$value:'';
	}
	
	public function write($sessionId,$value)
	{
		return $this->redis->hset($this->sessionName,$sessionId,$value);
	}
	public function destroy($sessionId)
	{
		return $this->redis->hDel($this->sessionName,$sessionId);
	}
	public function gc()
	{
		return true;
	}
	public function __destruct()
	{
		session_write_close();
	}
}