<?php
/**
 *  2015-8-30 下午1:23:52
 *  @author zhengyin <zhengyin.name@gmail.com>
 *  @email zhengyin.name@gmail.com
 *  Yaf 自定义视图,采用 Smarty处理视图
 */
namespace IZY\Syc\Unit;

class SmartyView extends \Yaf\View_interface
{
	private $smarty = null;
	
	public function __construct(){
		
		$this->smarty = \Smarty\init::getSmarty();
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Yaf\View_Interface::assign()
	 */
	public function assign($name, $value){
		$this->smarty->assign($name, $value);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Yaf\View_Interface::display()
	 */
	public function display($tpl, array $tpl_vars = null){
		if(is_array($tpl_vars)){
			$this->smarty->assign($tpl_vars);
		}
		$this->smarty->display($tpl);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Yaf\View_Interface::getScriptPath()
	 */
	public function getScriptPath(){
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Yaf\View_Interface::render()
	 */
	public function render($tpl, array $tpl_vars = null){
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Yaf\View_Interface::setScriptPath()
	 */
	public function setScriptPath($template_dir){
		
	}
	
}