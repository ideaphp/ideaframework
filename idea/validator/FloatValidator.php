<?php
namespace idea\validator;

/**
 * FloatValidator class file
 * 验证一个值是否浮点型
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: FloatValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class FloatValidator extends Validator
{
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" is not a float.';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return (is_float($this->getValue()) == $this->getOption());
	}
}
