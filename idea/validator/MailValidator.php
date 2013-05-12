<?php
namespace idea\validator;

/**
 * MailValidator class file
 * 验证邮箱
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: MailValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class MailValidator extends Validator
{
	/**
	 * @var string 正则：验证邮箱
	 */
	const REGEX_MAIL = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';
	
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message =  '"%value%" does not appear to be a valid email address.';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return (preg_match(self::REGEX_MAIL, $this->getValue()) == $this->getOption());
	}
}
