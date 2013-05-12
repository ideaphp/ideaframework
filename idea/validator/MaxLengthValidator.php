<?php
namespace idea\validator;

/**
 * MaxLengthValidator class file
 * 验证字符长度是否小于指定长度
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: MaxLengthValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class MaxLengthValidator extends Validator
{
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" is too long ("%option%" characters max).';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return (strlen($this->getValue()) < $this->getOption());
	}
}
