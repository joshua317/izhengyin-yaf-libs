<?php
/**
 *  2015-7-23 下午10:26:46
 *  @author zhengyin <zhengyin.name@gmail.com>
 *  Http 工具类
 */
namespace IZY\Sys\unit;
class Http{
	/**
	 * 是否为一个异步的请求，此验证需要jQuery支持
	 * @return boolean
	 */
	public static function isAjax() {
		if (isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest')
			return true;
		return false;
	}
	/**
	 * 响应头
	 * @param
	 *    $type
	 */
	public static function setReponseHead($type = '') {
		switch ($type) {
			case 'json' :
				header ( 'Content-Type:application/json;charset=utf-8' );
				header ( 'Pragma: no-cache' );
				header ( 'Cache-Control: no-cache, no-store, max-age=0' );
				header ( 'Expires: 1L' );
				break;
			default :
				header ( 'Content-type:text/html;charset=utf-8' );
				break;
		}
	}
	/**
	 * 客户端ip
	 * @return String
	 */
	public static function clientIp(){
		$ipaddress = '0.0.0.0';
		if (array_key_exists ( 'HTTP_CLIENT_IP', $_SERVER ))
			$ipaddress = $_SERVER ['HTTP_CLIENT_IP'];
		else if (array_key_exists ( 'HTTP_X_FORWARDED_FOR', $_SERVER ))
			$ipaddress = $_SERVER ['HTTP_X_FORWARDED_FOR'];
		else if (array_key_exists ( 'HTTP_X_FORWARDED', $_SERVER ))
			$ipaddress = $_SERVER ['HTTP_X_FORWARDED'];
		else if (array_key_exists ( 'HTTP_FORWARDED', $_SERVER ))
			$ipaddress = $_SERVER ['HTTP_FORWARDED'];
		else if (array_key_exists ( 'REMOTE_ADDR', $_SERVER ))
			$ipaddress = $_SERVER ['REMOTE_ADDR'];
		if (! preg_match ( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ipaddress )) {
			$ipaddress = '0.0.0.0';
		}
		return trim ( $ipaddress );
	}
	/**
	 * 获取代理ip
	 *
	 * @return string
	 */
	public static function controllerIp() {
		if (array_key_exists ( 'HTTP_FORWARDED_FOR', $_SERVER ))
			return $_SERVER ['HTTP_FORWARDED_FOR'];
		else
			return '';
	}
	/**
	 * 客户端 userAgent
	 */
	public static function clientAgent() {
		return $_SERVER ['HTTP_USER_AGENT'];
	}
	/**
	 * 当前请求时间
	 */
	public static function requestDate() {
		return date ( 'Y-m-d H:i:s', $_SERVER ['REQUEST_TIME'] );
	}
	/**
	 * 当前请求时间
	 */
	public static function requestUri() {
		return $_SERVER ['REQUEST_URI'];
	}
	/**
	 * 解析URL
	 * @param $url 需要解析的URL
	 * @param $field 需要获取的字段
	 */
	public static function parseUrl($url, $field = null) {
		$urlInfo = parse_url ( $url );
		$urlInfo ['args'] = array ();
		if (! empty ( $urlInfo ['query'] )) {
			$string = $urlInfo ['query'];
			$arr1 = explode ( '&', $string );
				
			if (is_array ( $arr1 )) {
				foreach ( $arr1 as $v ) {
					$arr2 = explode ( '=', $v );
					if (is_array ( $arr2 ) && count ( $arr2 ) == 2) {
						$urlInfo ['args'] [trim ( $arr2 [0] )] = trim ( $arr2 [1] );
					}
				}
			}
		}
		return is_null ( $field ) ? $urlInfo : $urlInfo [$field];
	}
}