<?php
namespace idea;

use idea\mvc;
use idea\log\LogStream;
use idea\log\Logger;
use idea\ap\Singleton;

/**
 * 为true时表示测试环境，会打印Debug日志，页面上展示调试信息
 */
defined('DEBUG') || define('DEBUG', false);

/**
 * 设置PHP报错级别
 */
error_reporting(DEBUG ? E_ALL : 0);

/**
 * 是否自动将GET、POST、COOKIE中的"'"、'"'、'\'加上反斜线
 */
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

require 'DirName.php';

/**
 * 设置公共框架和代码库目录、当前项目的公共代码库目录、当前项目的所有模块存放目录到PHP INI自动加载目录
 */
set_include_path('.' . PS . DIR_LIBRARIES . PS. DIR_APP . PS . trim(get_include_path(), '.' . PS)) 
|| exit('Request Error, your server configuration not allowed to change PHP include path');

/**
 * 自动加载PHP文件
 * @param string $className
 * @return void
 */
function spl_autoload($className)
{
	require $className;
}

/**
 * 注册__autoload方法
 */
spl_autoload_register('spl_autoload') 
|| exit('Request Error, unable to register autoload as an autoloading method');

/**
 * CoreBase abstract class file
 * 核心类管理器基类
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: CoreBase.php 1 2013-04-05 20:00:06Z huan.song $
 * @package idea
 * @since 1.0
 */
abstract class CoreBase
{
	/**
	 * @var string 模型名
	 */
	protected static $_moduleName;
	
	/**
	 * @var string 控制器名
	 */
	protected static $_controllerName;
	
	/**
	 * @var string 方法名
	 */
	protected static $_actionName;
	
	/**
	 * @var instance of idea\ap\HttpRequest
	 */
	protected static $_request = null;
	
	/**
	 * @var instance of idea\ap\HttpResponse
	 */
	protected static $_response = null;
	
	/**
	 * @var instance of idea\ap\HttpSession
	 */
	protected static $_session = null;
	
	/**
	 * @var instance of idea\mvc\interfaces\View
	 */
	protected static $_view = null;
	
	/**
	 * @var instance of idea\mvc\Router
	 */
	protected static $_router = null;
	
	/**
	 * @var instance of idea\log\Logger 
	 */
	protected static $_logger = null;
	
	/**
	 * @return string 框架的版本号
	 */
	public static function getVersion()
	{
		return '1.0.0';
	}
	
	/**
	 * 获取请求模式处理类
	 * @return idea\ap\HttpRequest
	 */
	public static function getRequest()
	{
		if (self::$_request === null) {
			self::setRequest();
		}
		
		return self::$_request;
	}
	
	/**
	 * 设置请求模式处理类
	 * @param idea\ap\HttpRequest $request
	 * @return void
	 */
	public static function setRequest(HttpRequest $request = null)
	{
		if ($request === null) {
			$request = Singleton::get('idea\\ap\\HttpRequest');
		}
		
		self::$_request = $request;
	}
	
	/**
	 * 获取响应模式发送类
	 * @return idea\ap\HttpResponse
	 */
	public static function getResponse()
	{
		if (self::$_response === null) {
			self::setResponse();
		}
		
		return self::$_response;
	}
	
	/**
	 * 设置响应模式发送类
	 * @param idea\ap\HttpResponse $response
	 * @return void
	 */
	public static function setResponse(HttpResponse $response = null)
	{
		if ($response === null) {
			$response = Singleton::get('idea\\ap\\HttpResponse');
		}
		
		self::$_response = $response;
	}
	
	/**
	 * 获取HTTP会话管理类
	 * @return idea\ap\HttpSession
	 */
	public static function getSession()
	{
		if (self::$_session === null) {
			self::setSession();
		}
	
		return self::$_session;
	}
	
	/**
	 * 设置HTTP会话管理类
	 * @param idea\ap\HttpSession $session
	 * @return void
	 */
	public static function setSession(HttpSession $session = null)
	{
		if ($session === null) {
			$session = Singleton::get('idea\\ap\\HttpSession');
		}
		
		self::$_session = $session;
	}
	
	/**
	 * 获取路由器
	 * @return idea\mvc\Router
	 */
	public static function getRouter()
	{
		if (self::$_router === null) {
			self::setRouter();
		}
	
		return self::$_router;
	}
	
	/**
	 * 设置路由器
	 * @param idea\mvc\Router $router
	 * @return void
	 */
	public static function setRouter(Router $router = null)
	{
		if ($router === null) {
			$router = Singleton::get('idea\\mvc\\Router');
		}
	
		self::$_router = $router;
	}
	
	/**
	 * 获取模板解析类
	 * @return idea\mvc\interfaces\View
	 */
	public static function getView()
	{
		if (self::$_view === null) {
			self::setView();
		}
	
		return self::$_view;
	}
	
	/**
	 * 设置模板解析类
	 * @param idea\mvc\interfaces\View $view
	 * @return void
	 */
	public static function setView(mvc\interfaces\View $view = null)
	{
		if ($view === null) {
			$view = new mvc\View();
			$file = DIR_CONF_APP . DS . 'view.php';
			$config = require_once $file;
			
			$view->viewDirectory = DIR_APP_VIEWS;
			isset($config['skin_name']) || $view->skinName = $config['skin_name'];
			isset($config['charset']) || $view->charset = $config['charset'];
			isset($config['tpl_extension']) || $view->tplExtension = $config['tpl_extension'];
			isset($config['version']) || $view->version = $config['version'];
		}
		
		$view->assign('app_name', APP_NAME);
		$view->assign('module_name', self::getModuleName());
		$view->assign('controller_name', self::getControllerName());
		$view->assign('action_name', self::getActionName());
		$view->assign('log_id', self::getLogger()->getId());
		self::$_view = $view;
	}
	
	/**
	 * 获取日志处理类
	 * @return idea\log\Logger
	 */
	public static function getLogger()
	{
		if (self::$_logger === null) {
			self::setLogger();
		}
		
		return self::$_logger;
	}
	
	/**
	 * 设置日志处理类
	 * @param idea\log\Logger
	 * @return void
	 */
	public static function setLogger(Logger $logger = null)
	{
		if ($logger === null) {
			$logger = new Logger();
			$file = DIR_LOG_APP . DS . APP_NAME . '.log.' . date('YmdH');
			$wfFile = DIR_LOG_APP . DS . APP_NAME . '.log.wf.' . date('Ymd');
			
			$writer = new LogStream($file);
			$logger->addWriter($writer, 'notice');
			
			$writer = new LogStream($wfFile);
			$logger->addWriter($writer, 'warning');
		}
		
		self::$_logger = $logger;
	}
	
	/**
	 * 打印日志，如果是测试并且是warning日志，则输出追溯调用的文件和代码行
	 * @param string $priority
	 * @param string $event
	 * @param string $method
	 * @param integer $errno
	 * @return void
	 */
	public static function log($priority, $event, $method = __METHOD__, $errno = 0)
	{
		$common = array(
			'app' => APP_NAME,
			'err_no' => $errno,
			'module' => self::getModuleName(),
			'controller' => self::getControllerName(),
			'action' => self::getActionName()
		);
		
		if (!is_array($event)) {
			$event = array('msg' => $event);
		}
		
		$events = array_merge($common, $event);
		self::getLogger()->write($events, $priority, $method);
	}
	
	/**
	 * 获取模型名
	 * @return string
	 */
	public static function getModuleName()
	{
		return self::$_moduleName;
	}
	
	/**
	 * 获取控制器名
	 * @return string
	 */
	public static function getControllerName()
	{
		return self::$_controllerName;
	}
	
	/**
	 * 获取方法名
	 * @return string
	 */
	public static function getActionName()
	{
		return self::$_actionName;
	}
	
	/**
	 * 加载并运行项目引导文件
	 * @return void
	 */
	public static function bootstrap()
	{
		$bootstrap = new \Bootstrap();
		$bootstrap->run();
		
		$router = self::getRouter()->route(self::getRequest());
		self::$_moduleName = $router->module;
		self::$_actionName = $router->action;
		self::$_controllerName = $router->controller;

		mvc\Dispatcher::run($router);
	}
	
	/**
	 * 测试打印数据，只有DEBUG或者强制的时候才输出
	 * @param mixed $expression
	 * @param boolean $coercion
	 * @return void
	 */
	public static function debug_dump($expression, $coercion = false)
	{
		if (DEBUG || $coercion) {
			echo '<pre>';
			var_dump($expression);
			echo '</pre>';
			exit;
		}
	}
}
