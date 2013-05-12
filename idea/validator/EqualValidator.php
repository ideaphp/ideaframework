<?php
namespace idea\validator;

/**
 * EqualValidator class file
 * 验证两个值是否相等
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: EqualValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class EqualValidator extends Validator
{
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" is not equal "%option%".';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return ($this->getValue() === $this->getOption());
	}
}
