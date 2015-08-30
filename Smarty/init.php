<?php
/**
 *  2015-8-30 下午1:36:03
 *  @author zhengyin <zhengyin.name@gmail.com>
 */
namespace Smarty;
class Init{
	
	
	public function __construct(){
	 	\Yaf\Loader::import(__DIR__.'/Smarty.class.php');
	}
	
	private static $smatry = null;
	
	public static function getSmarty(){
		if(self::$smatry === null){
			self::$smatry = new \Smarty();
		}
		return self::$smatry;
	}
}