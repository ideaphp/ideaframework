<?php
namespace idea\mvc;

use idea\ap\ErrorException;

/**
 * Widget abstract class file
 * 页面装饰基类
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: Widget.php 1 2013-04-16 20:00:06Z huan.song $
 * @package idea.mvc
 * @since 1.0
 */
abstract class Widget
{
	/**
	 * @var instance of idea\mvc\View
	 */
	protected $_view = null;
	
	/**
	 * @var array 用于寄存渲染模板的变量名和值
	 */
	protected $_tplVars = array();
	
	/**
	 * @var array 用于模板处理的参数，可以寄存CSS名、JS名和模板文件名
	 */
	protected $_params = array();
	
	/**
	 * @var string 页面装饰模板所在的目录
	 */
	protected $_widgetDirectory;
	
	/**
	 * @var string 当没有指定CSS名、JS名和模板文件名时，默认的名称
	 */
	protected $_defaultName = 'default';
	
	/**
	 * 构造方法：初始化页面解析类、渲染模板的变量名和值、用于模板处理的参数
	 * @param idea\mvc\View $view
	 * @param array $tplVars
	 * @param array $params
	 */
	public function __construct(View $view, array $tplVars = array(), array $params = array())
	{
		$this->_view = $view;
		$this->_tplVars = $tplVars;
		$this->_params = $params;
		$this->_init();
	}

	/**
	 * 子类构造方法：子类调用此方法作为构造方法，避免重写父类构造方法
	 */
	protected function _init()
	{
	}

	/**
	 * 魔术方法：通过模板变量名获取模板变量值
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (isset($this->_tplVars[$key])) {
			return $this->_tplVars[$key];
		}
		return null;
	}

	/**
	 * 魔术方法：判定模板变量是否已经存在
	 * @param mixed $key
	 * @return boolean
	 */
	public function __isset($key)
	{
		return isset($this->_tplVars[$key]);
	}

	/**
	 * 解析模板文件，根据需求输出到浏览器
	 * @param string $tplName
	 * @param boolean $display
	 * @return string|void
	 * @throws ErrorException 如果模板文件不存在，抛出异常
	 */
	public function fetch($tplName = null, $display = false)
	{
		if ($tplName === null) {
			$tplName = $this->getTplName();
		}
		$tplPath = $this->getWidgetDirectory() . DIRECTORY_SEPARATOR . $tplName;
		if (is_file($tplPath)) {
			if ($display) {
				include_once $tplPath;
			}
			else {
				ob_start();
				ob_implicit_flush(false);
				include_once $tplPath;
				return ob_get_clean();
			}
		}
		else {
			throw new ErrorException(sprintf(
				'Widget tpl file "%s" is not a valid directory.', $tplPath
			));
		}
	}
	
	/**
	 * 将模板内容输出到浏览器
	 * @param string $tplName
	 * @return void
	 */
	public function display($tplName = null)
	{
		if ($tplName === null) {
			$tplName = $this->getTplName();
		}
		
		$this->fetch($tplName, true);
	}

	/**
	 * 获取页面解析类
	 * @return idea\mvc\View
	 */
	public function getView()
	{
		return $this->_view;
	}

	/**
	 * 获取页面辅助类
	 * @return idea\mvc\Html
	 */
	public function getHtml()
	{
		return $this->getView()->getHtml();
	}

	/**
	 * 获取JavaScript文件名，默认文件名：default.js
	 * @return string
	 */
	public function getJsName()
	{
		$jsName = isset($this->_params['jsName']) ? $this->_params['jsName'] : $this->_defaultName;
		return $jsName . '.js';
	}

	/**
	 * 获取Css文件名，默认文件名：default.css
	 * @return string
	 */
	public function getCssName()
	{
		$cssName = isset($this->_params['cssName']) ? $this->_params['cssName'] : $this->_defaultName;
		return $cssName . '.css';
	}
	
	/**
	 * 获取模板文件名，默认文件名：default.php
	 * @return string
	 */
	public function getTplName()
	{
		$tplName = isset($this->_params['tplName']) ? $this->_params['tplName'] : $this->_defaultName;
		return $tplName . $this->getView()->tplExtension;
	}
	
	/**
	 * 获取页面装饰模板所在的目录，默认目录：{viewDirectory}/widgets/{className}
	 * @return string
	 * @throws ErrorException 如果页面装饰模板所在的目录不存在，抛出异常
	 */
	public function getWidgetDirectory()
	{
		if ($this->_widgetDirectory !== null) {
			return $this->_widgetDirectory;
		}

		$className = strtolower(get_class($this));
		if (($pos = strrpos($className, '\\')) !== false) {
			$className = substr($className, $pos + 1);
		}
		$this->_widgetDirectory = $this->getView()->viewDirectory . DIRECTORY_SEPARATOR . $this->getView()->skinName . DIRECTORY_SEPARATOR . 'widgets' . DIRECTORY_SEPARATOR . $className;
		if (is_dir($this->_widgetDirectory)) {
			return $this->_widgetDirectory;
		}
		else {
			throw new ErrorException(sprintf(
				'View widgets directory "%s" is not a valid directory.', $this->_widgetDirectory
			));
		}
	}

	/**
	 * 执行Widget类，输出内容
	 * @return void
	 */
	abstract protected function run();
}
