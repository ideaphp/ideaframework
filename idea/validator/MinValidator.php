<?php
namespace idea\validator;

/**
 * MinValidator class file
 * 验证值是否大于指定值
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: MinValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class MinValidator extends Validator
{
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" must be at least "%option%".';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return ($this->getValue() > $this->getOption());
	}
}
