<?php
namespace idea\validator;

/**
 * NotEmptyValidator class file
 * 验证一个值是否不为空
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: NotEmptyValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class NotEmptyValidator extends Validator
{
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" is empty.';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return ((!empty($this->getValue())) == $this->getOption());
	}
}
