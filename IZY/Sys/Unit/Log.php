<?php
/**
 * 2014-10-29
 * zhengyin <zhengyin.name@gmail.com>
 * 日志记录工具
 */
namespace IZY\Sys\unit;
class Log
{
	/*** 日志级别相关常量  ***/
	
	//调试
	const DEBUG = 'debug';
	//提示
	const INFO = 'info';
	//警告
	const WARN = 'warn';
	//错误
	const ERROR = 'error';
	//致命
	const FATAL = 'fatal';
		
	public static function debug($logFile, $content , $isOutPut=false){
		self::writeLog($logFile, $content ,self::DEBUG , $isOutPut);
	}
	public static function info($logFile, $content , $isOutPut=false){
		self::writeLog($logFile, $content ,self::INFO , $isOutPut);
	}
	public static function warn($logFile, $content , $isOutPut=false){
		self::writeLog($logFile, $content ,self::WARN , $isOutPut);
	}
	public static function error($logFile, $content , $isOutPut=false){
		self::writeLog($logFile, $content ,self::ERROR , $isOutPut);
	}
	public static function fatal($logFile, $content , $isOutPut=false){
		self::writeLog($logFile, $content ,self::FATAL , $isOutPut);
	}
	/**
	 * 写入日志
	 * @param  $logFile	   日志文件
	 * @param  $content  日志内容
	 * @param  $level	   日志级别
	 * @param  $isOutPut 是否输出到控制台
	 * @return boolean
	 */
	public static function writeLog($logFile, $content ,$level=null,$isOutPut=false)
	{
		if($level === null){
			$level = self::INFO;	//默认未 info 级别
		}
		//日志文件路径
		$logFile = LOG_DIR.SITE.'/'.trim($logFile,'/').'_'.$level.'.log';
		
		if(is_file($logFile)) {
			$path = realpath(dirname($logFile));
		} else {
			$path = dirname($logFile);
		}
		if(!is_dir($path)) {
			mkdir($path, 0755, TRUE);
		}
		$dateTime = date("Y-m-d H:i:s");
		$logData = $dateTime.' '.$content.PHP_EOL;
		$state = file_put_contents($logFile, $logData, FILE_APPEND);
		if($isOutPut){
			echo $logData;
		}
		if($state === FALSE) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
}