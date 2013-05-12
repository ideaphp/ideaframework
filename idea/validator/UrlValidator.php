<?php
namespace idea\validator;

/**
 * UrlValidator class file
 * 验证Url
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: UrlValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class UrlValidator extends Validator
{
	/**
	 * @var string 正则：验证URL
	 */
	const REGEX_URL = '~^
		(http|https|ftp|ftps)://                # protocol
		(
		([a-z0-9-]+\.)+[a-z]{2,6}               # a domain name
		|                                       #  or
		\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}      # a IP address
		)
		(:[0-9]+)?                              # a port (optional)
		(/?|/\S+)                               # a /, nothing or a / with something
	$~ix';
	
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" does not appear to be a valid url address.';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return (preg_match(self::REGEX_URL, $this->getValue()) == $this->getOption());
	}
}
