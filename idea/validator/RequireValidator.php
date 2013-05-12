<?php
namespace idea\validator;

/**
 * RequireValidator class file
 * 验证是否是安全的可被require的文件
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: RequireValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class RequireValidator extends Validator
{
	/**
	 * @var string 正则：是否是安全的require文件
	 */
	const REGEX_SECURITY_REQUIRE = '/[^a-z0-9\\/\\\\_.:-]/i';
	
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" is not a security require.';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return (preg_match(self::REGEX_SECURITY_REQUIRE, $this->getValue()) == $this->getOption());
	}
}
