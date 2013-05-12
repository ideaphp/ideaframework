<?php
namespace idea\mvc\routes;

use idea\ap\HttpRequest;

/**
 * RouteSupervar class file
 * 默认路由
 * <pre>
 * 一.默认路由例子：
 * URL：http://domain.com/index.php?r=archive/show
 * $route = new RouteSupervar('r');
 * $value = array(
 *     'module'     => '默认模型',
 *     'controller' => 'archive',
 *     'action'     => 'show'
 * );
 * 
 * 二.默认路由例子：
 * URL：http://domain.com/index.php?r=main/archive/show
 * $route = new RouteSupervar('r');
 * $value = array(
 *     'module'     => 'main',
 *     'controller' => 'archive',
 *     'action'     => 'show'
 * );
 * 
 * 三.默认路由例子：
 * URL：http://domain.com/index.php?r=archive
 * $route = new RouteSupervar('r');
 * $value = array(
 *     'module'     => '默认模型',
 *     'controller' => 'archive',
 *     'action'     => '默认方法'
 * );
 * </pre>
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: RouteSupervar.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.mvc.routers
 * @since 1.0
 */
class RouteSupervar extends Route
{
	/**
	 * @var string 用于从Request中获取路径Path的键
	 */
	protected $_routeVar = 'r';
	
	/**
	 * 构造方法：初始化用于从Request中获取路径Path的键
	 * @param string $routeVar
	 */
	public function __construct($routeVar = null)
	{
		if (is_string($routeVar) && ($routeVar = trim($routeVar)) != '') {
			$this->_routeVar = $routeVar;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see idea\mvc\routers.Route::match()
	 */
	public function match(HttpRequest $request)
	{
		$path = $request->getParam($this->_routeVar, '');
		$path = trim($path, self::URI_DELIMITER . ' ');

		$bits = explode(self::URI_DELIMITER, $path);
		switch (count($bits)) {
			case 2:
				$this->setController($bits[0]);
				$this->setAction($bits[1]);
				break;
			case 1:
				$this->setController($bits[0]);
				break;
			case 3:
			default:
				$this->setModule($bits[0]);
				$this->setController($bits[1]);
				$this->setAction($bits[2]);
				break;
		}

		return true;
	}
}
