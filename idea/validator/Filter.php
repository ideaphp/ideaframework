<?php
namespace idea\validator;

use idea\ap\ErrorException;

/**
 * Filter class file
 * 数据验证类
 * <pre>
 * 验证规则：
 * $rules = array(
 *     'user_loginname' => array(
 *         'MinLength' => array(6, '用户名长度%value%不能小于%option%个字符.'), 
 *         'MaxLength' => array(12, '用户名长度%value%不能大于%option%个字符.')
 *     ),
 *     'user_password' => array(
 *         'idea\\validator\\MinLengthValidator' => array(6, '密码长度%value%不能小于%option%个字符.'),
 *         'MaxLength' => array(12, '密码长度%value%不能大于%option%个字符.')
 *     ),
 *     'user_email' => array(
 *     	   'MaxLength' => array(50, '邮箱长度%value%不能大于%option%个字符.'),
 *         'Mail' => array(true, '邮箱%value%不符合规范.')
 *     ),
 * );
 * 
 * $params = array(
 *     'user_loginname' => 'abcdefghi',
 *     'user_password' => '1234',
 *     'user_email' => 'iphperyeahnet'
 * );
 *
 * $filter = new Filter();
 * $hasError = $filter->run($rules, $params);
 * $errors = $filter->getErrors();
 * 结果：
 * $errors = array(
 *     'user_password' => '密码长度1234不能小于6个字符.',
 *     'user_email' => array('邮箱长度iphperyeahnet...不能大于50个字符.', '邮箱iphperyeahnet...不符合规范.')
 * );
 * </pre>
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: Filter.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.validator
 * @since 1.0
 */
class Filter
{
	/**
	 * @var array 记录所有错误信息
	 */
	protected $_errors = array();
	
	/**
	 * 运行验证处理类
	 * @param array $rules
	 * @param array $params
	 * @return boolean
	 * @throws ErrorException 如果验证规则或者验证数据不是数组，抛出异常
	 * @throws ErrorException 如果字段验证规则不是数组，抛出异常
	 * @throws ErrorException 如果键名在验证数据中不存在，抛出异常
	 */
	public function run(array $rules, array $params)
	{
		if ($rules === null || $params === null) {
			throw new ErrorException('Filter the rules and params must be array.');
		}

		$this->clearErrors();
		foreach ($rules as $key => $rule) {
			if (!is_array($rule)) {
				throw new ErrorException(sprintf(
					'Filter the rule of rules key "%s" must be array.', $key
				));
			}
			
			if (!isset($params[$key])) {
				throw new ErrorException(sprintf(
					'Filter the key "%s" of param is undefined', $key
				));
			}
	
			foreach ($rule as $validator => $_rule) {
				$instance = $this->getValidator($validator, $params[$key], $_rule);
				if ($instance->isValid()) {
					continue;
				}
	
				$this->addError($key, $instance->getMessage());
			}
		}
		
		return !$this->hasError();
	}
	
	/**
	 * 创建验证类
	 * @param string $validator
	 * @param mixed $value
	 * @param array $rules
	 * @return idea\validator\Validator
	 * @throws ErrorException 如果Validator类不存在，抛出异常
	 * @throws ErrorException 如果获取的实例不是idea\validator\Validator类的子类，抛出异常
	 */
	public function createValidator($validator, $value, array $rules)
	{
		$option = isset($rules[0]) ? $rules[0] : null;
		if ($option === null) {
			throw new ErrorException(
				'Filter option of rules is undefined'
			);
		}
		
		$message = isset($rules[1]) ? $rules[1] : '';
		if (strpos($validator, '\\') === false) {
			$validator = 'idea\\validator\\' . $validator . 'Validator';
		}
		
		if (!class_exists($validator)) {
			throw new ErrorException(sprintf(
				'Filter is unable to find the requested validator "%s".', $validator
			));
		}
		
		$instance = new $validator($value, $option, $message);
		if (!$instance instanceof Validator) {
			throw new ErrorException(sprintf(
				'Filter Validator class "%s" is not instanceof idea\validator\Validator.', $validator
			));
		}
		
		return $instance;
	}
	
	/**
	 * 获取所有的错误信息
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	/**
	 * 清除所有的错误信息
	 * @return idea\validator\Validator
	 */
	public function clearErrors()
	{
		$this->_errors = array();
		return $this;
	}
	
	/**
	 * 通过键名获取错误信息
	 * @param string|null $key
	 * @param boolean $justOne
	 * @return mixed
	 */
	public function getError($key = null, $justOne = true)
	{
		if (empty($this->_errors)) {
			return null;
		}
	
		$errors = array();
		if ($key === null) {
			return array_shift(array_slice($this->_errors, 0, 1));
		}
		elseif (isset($this->_errors[$key])) {
			return ($justOne && is_array($this->_errors[$key])) ? array_shift($this->_errors[$key]) : $this->_errors[$key];
		}
		else {
			return null;
		}
	}

	/**
	 * 添加一条错误信息
	 * @param string $key
	 * @param string $value
	 * @return idea\validator\Validator
	 */
	public function addError($key, $value)
	{
		if (isset($this->_errors[$key])) {
			if (!is_array($this->_errors[$key])) {
				$this->_errors[$key] = array($this->_errors[$key]);
			}

			$this->_errors[$key][] = $value;
		}
		else {
			$this->_errors[$key] = $value;
		}

		return $this;
	}
	
	/**
	 * 通过键名判定错误信息是否存在
	 * @param string|null $key
	 * @return boolean
	 */
	public function hasError($key = null)
	{
		if ($key === null) {
			return (count($this->_errors) > 0);
		}

		return isset($this->_errors[$key]);
	}
}
