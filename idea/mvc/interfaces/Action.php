<?php
namespace idea\mvc\interfaces;

/**
 * Action interface file
 * Action接口，辅助分解Controller类业务，将Controller业务化整为零，方便管理和重用
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: Action.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.mvc.interfaces
 * @since 1.0
 */
interface Action
{
	/**
	 * 调用Action类的入口
	 * @return void
	 */
	public function run();

	/**
	 * 获取控制器类
	 * @return idea\mvc\Controller
	 */
	public function getController();

	/**
	 * 获取Action名
	 * @return string
	 */
	public function getId();
}
