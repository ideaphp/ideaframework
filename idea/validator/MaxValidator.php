<?php
namespace idea\validator;

/**
 * MaxValidator class file
 * 验证值是否小于指定值
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: MaxValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class MaxValidator extends Validator
{
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" must be at most "%option%".';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return ($this->getValue() < $this->getOption());
	}
}
