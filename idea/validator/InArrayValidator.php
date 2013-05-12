<?php
namespace idea\validator;

/**
 * InArrayValidator class file
 * 验证值在数组中是否存在
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: InArrayValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class InArrayValidator extends Validator
{
	/**
	 * @var string 默认出错后的提醒消息
	 */
	protected $_message = '"%value%" was not found in the haystack.';
	
	/**
	 * (non-PHPdoc)
	 * @see idea\validator.Validator::isValid()
	 */
	public function isValid()
	{
		return in_array($this->getValue(), $this->getOption());
	}
}
