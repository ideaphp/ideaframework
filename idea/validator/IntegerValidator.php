<?php
namespace idea\validator;

/**
 * IntegerValidator class file
 * 验证一个值是否是整型
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: IntegerValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class IntegerValidator extends Validator
{
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" is not a integer.';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return (is_int($this->getValue()) == $this->getOption());
	}
}
