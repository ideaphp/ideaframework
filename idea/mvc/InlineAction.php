<?php
namespace idea\mvc;

/**
 * InlineAction class file
 * 定义一个Action类，用来代替Controller类的Action方法
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: InlineAction.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.mvc
 * @since 1.0
 */
class InlineAction extends Action
{
	/**
	 * (non-PHPdoc)
	 * @see idea\mvc\interfaces.Action::run()
	 */
	public function run()
	{
		$method = $this->getId() . 'Action';
		$this->getController()->$method();
	}
}
