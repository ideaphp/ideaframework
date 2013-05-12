<?php
namespace idea\mvc;

use idea\ap\Application;
use idea\ap\ErrorException;
use idea\ap\HttpRequest;
use idea\ap\HttpResponse;
use idea\ap\Registry;

/**
 * Dispatcher class file
 * 发报器类，实例化Controller类，并调用Action方法
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: Dispatcher.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.mvc
 * @since 1.0
 */
class Dispatcher extends Application
{
	/**
	 * @var instance of idea\mvc\Dispatcher
	 */
	protected static $_instance = null;
	
	/**
	 * @var string Controller目录名
	 */
	protected $_controllerDirName = 'controller';
	
	/**
	 * @var string Module目录名
	 */
	protected $_moduleDirName = 'modules';
	
	/**
	 * 构造方法：禁止被实例化
	 */
	protected function __construct()
	{
	}
	
	/**
	 * 魔术方法：禁止被克隆
	 */
	private function __clone()
	{
	}
	
	/**
	 * 单例模式：获取本类的实例化对象
	 * @return idea\mvc\Dispatcher
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
			
		return self::$_instance;
	}
	
	/**
	 * 实例化Controller类，并调用Action方法
	 * @param idea\mvc\Router $router
	 * @return void
	 */
	public static function run(Router $router)
	{
		self::getInstance()->dispatch($router);
	}
	
	/**
	 * 实例化Controller类，并调用Action方法
	 * @param idea\mvc\Router $router
	 * @return void
	 */
	public function dispatch(Router $router)
	{
		$controller = $this->createController($router);
		$action = $controller->createAction($router->action);
		$controller->runAction($action);
	}
	
	/**
	 * 通过路由器获取Controller名，并创建Controller类
	 * @param idea\mvc\Router $router
	 * @return idea\mvc\Controller
	 * @throws ErrorException 如果Controller类不存在，抛出异常
	 * @throws ErrorException 如果获取的实例不是idea\mvc\Controller类的子类，抛出异常
	 */
	public function createController(Router $router)
	{
		$controller = $this->getController($router);
		if (!class_exists($controller)) {
			throw new ErrorException(sprintf(
				'Dispatcher is unable to find the requested controller "%s".', $controller
			));
		}

		$instance = new $controller();
		if (!$instance instanceof Controller) {
			throw new ErrorException(sprintf(
				'Dispatcher Class "%s" is not instanceof idea\mvc\Controller.', $controller
			));
		}

		return $instance;
	}
	
	/**
	 * 通过路由器获取Controller名
	 * @param idea\mvc\Router $router
	 * @return string
	 */
	public function getController(Router $router)
	{
		if (($module = trim((string) $router->module)) !== '') {
			$module = $this->_moduleDirName . '\\' . $module . '\\';
		}
		
		return $module . $this->_controllerDirName . '\\' . ucfirst($router->controller) . 'Controller';
	}
}
