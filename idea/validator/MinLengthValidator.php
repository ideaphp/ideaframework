<?php
namespace idea\validator;

/**
 * MinLengthValidator class file
 * 验证字符长度是否大于指定长度
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: MinLengthValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class MinLengthValidator extends Validator
{
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" is too short ("%option%" characters min).';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return (strlen($this->getValue()) > $this->getOption());
	}
}
