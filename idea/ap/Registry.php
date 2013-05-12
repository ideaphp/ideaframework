<?php
namespace idea\ap;

/**
 * Registry class file
 * 全局数据寄存类
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: Registry.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.ap
 * @since 1.0
 */
class Registry
{
	/**
	 * @var array 用于寄存全局的数据
	 */
	protected static $_globals = array();

	/**
	 * 通过名称获取数据
	 * @param string $name
	 * @return mixed
	 */
	public static function get($name)
	{
		if (self::has($name)) {
			return self::$_globals[$name];
		}
	
		return null;
	}

	/**
	 * 设置名称和数据
	 * @param string $name
	 * @param mixed
	 * @return void
	 */
	public static function set($name, $value)
	{
		self::$_globals[$name] = $value;
	}

	/**
	 * 通过名称删除数据
	 * @param string $name
	 * @return void
	 */
	public static function del($name)
	{
		if (self::has($name)) {
			unset(self::$_globals[$name]);
		}
	}

	/**
	 * 通过名称判定数据是否已经存在
	 * @param string $name
	 * @return boolean
	 */
	public static function has($name)
	{
		return isset(self::$_globals[$name]);
	}
}
