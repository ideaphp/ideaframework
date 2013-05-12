<?php
namespace idea\saf;

use idea\Core;
use idea\ap\ErrorException;
use idea\db\Driver;
use idea\db\Statement;
use idea\db\Transaction;
use idea\db\TableSchema;

/**
 * DbCommand class file
 * 数据库操作类
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: DbCommand.php 1 2013-04-05 01:38:06Z huan.song $
 * @package idea.saf
 * @since 1.0
 */
class DbCommand extends Statement
{
	/**
	 * @var integer 连接数据库失败后，尝试重连的最大次数
	 */
	const MAX_RETRY_TIMES = 3;
	
	/**
	 * @var string 寄存数据库配置名
	 */
	protected $_clusterName = null;
	
	/**
	 * @var array 寄存数据库配置
	 */
	protected $_config = null;
	
	/**
	 * @var array instances of idea\db\TableSchema
	 */
	protected $_tableSchemas = array();
	
	/**
	 * @var instance of cms\helper\CommandBuilder
	 */
	protected $_commandBuilder = null;
	
	/**
	 * @var instance of idea\db\Transaction
	 */
	protected $_transaction = null;
	
	/**
	 * 构造方法：初始化数据库配置名
	 * @param string $clusterName
	 */
	public function __construct($clusterName)
	{
		$this->_clusterName = $clusterName;
		parent::__construct($this->getDriver(false));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see idea\db.Statement::query()
	 */
	public function query($sql = null, $params = null)
	{
		$start = gettimeofday();
		try {
			$output = parent::query($sql, $params);
			$message = 'sql query successfully';
			$code = 0;
		}
		catch (ErrorException $e) {
			$output = false;
			$message = 'sql query failed, ' . $e->getMessage();
			$code = $e->getCode();
		}
		$end = gettimeofday();
		
		$event = array(
			'msg' => $message,
			'sql' => $sql,
			'params' => serialize($params),
			'cost' => ($end['sec'] - $start['sec']) * 1000000 + ($end['usec'] - $start['usec']),
			'conf' => serialize($this->getConf()),
		);

		if ($output) {
			Core::log('notice', $event, __METHOD__, $code);
		}
		else {
			Core::log('warning', $event, __METHOD__, $code);
		}
		return $output;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see idea\db.Statement::getDriver()
	 */
	public function getDriver($autoOpen = true)
	{
		if ($this->_driver === null) {
			$config = $this->getConf();
			$this->_driver = new Driver($config['dsn'], $config['username'], $config['password'], $config['charset']);
			if (isset($config['case_folding'])) {
				$this->_driver->setCaseFolding($config['case_folding']);
			}
		}
		
		if (!$autoOpen) {
			return $this->_driver;
		}
		
		if ($this->_driver->getIsConnected()) {
			return $this->_driver;
		}
		
		$config = $this->getConf();
		for ($retry = 0; $retry < $config['retry']; $retry++) {
			try {
				$this->_driver->open();
				$message = 'PDO connect db successfully';
				$code = 0;
			}
			catch (ErrorException $e) {
				$message = 'PDO connect db failed, ' . $e->getMessage();
				$code = $e->getCode();
			}
			
			$event = array(
				'msg' => $message,
				'retry' => $retry,
				'conf' => serialize($config)
			);
			if ($code === 0) {
				Core::log('notice', $event, __METHOD__, $code);
				return $this->_driver;
			}
			else {
				Core::log('warning', $event, __METHOD__, $code);
			}
		}
		
		return false;
	}
	
	/**
	 * 获取最后一次插入记录的ID
	 * @return integer
	 */
	public function getLastInsertId()
	{
		try {
			return $this->getDriver(false)->getLastInsertId();
		}
		catch (ErrorException $e) {
			Core::log('warning', $e->getMessage(), __METHOD__, $e->getCode());
		}

		return 0;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see idea\db.Statement::getRowCount()
	 */
	public function getRowCount()
	{
		try {
			return parent::getRowCount();
		}
		catch (ErrorException $e) {
			Core::log('warning', $e->getMessage(), __METHOD__, $e->getCode());
		}

		return 0;
	}
	
	/**
	 * 获取数据库配置
	 * @return array
	 * @throws ErrorException 如果没有指定DSN、用户名、密码或编码格式，抛出异常
	 */
	public function getConf()
	{
		if ($this->_config === null) {
			$config = Conf::getDbConf($this->_clusterName);
			if (!isset($config['dsn']) || !isset($config['username']) || !isset($config['password']) || !isset($config['charset'])) {
				throw new ErrorException(sprintf(
					'DbCommand no entry is registered for key: dsn|username|password|charset in db config "%s"', serialize($config)
				));
			}
			$config['retry'] = isset($config['retry']) ? (int) $config['retry'] : MAX_RETRY_TIMES;
			$this->_config = $config;
		}
		
		return $this->_config;
	}
	
	/**
	 * 获取创建简单的执行命令的类
	 * 应该根据不同的数据库类型创建对应的CommandBuilder类
	 * 这里只用到MySQL数据库，暂时不做对应多数据库类型
	 * $dbType = $this->getDriver(false)->getDbType();
	 * @return cms\helper\CommandBuilder
	 */
	public function getCommandBuilder()
	{
		if ($this->_commandBuilder === null) {
			$this->_commandBuilder = new CommandBuilder();
		}
		
		return $this->_commandBuilder;
	}
	
	/**
	 * 获取PDO事务处理类
	 * @return idea\db\Transaction
	 */
	public function getTransaction()
	{
		if ($this->_transaction === null) {
			$this->_transaction = new Transaction($this->getDriver());
		}
		
		return $this->_transaction;
	}
	
	/**
	 * 通过表的实体类，获取表的概要描述，包括：表名、主键、字段、默认值
	 * 应该根据不同的数据库类型创建对应的TableSchema类
	 * 这里只用到MySQL数据库，暂时不做对应多数据库类型
	 * $dbType = $this->getDriver(false)->getDbType();
	 * @param string $entityName
	 * @return idea\db\TableSchema
	 */
	public function getTableSchema($entityName)
	{
		if (isset($this->_tableSchemas[$entityName])) {
			return $this->_tableSchemas[$entityName];
		}
		
		$ref = new \ReflectionClass($entityName);
		$attributes = $ref->getDefaultProperties();
		$columnNames = array_keys($attributes);
		foreach ($attributes as $key => $value) {
			if ($value === null) {
				unset($attributes[$key]);
			}
		}

		$tableSchema = new TableSchema();
		$tableSchema->name = $ref->hasConstant('TABLE_NAME') ? $ref->getConstant('TABLE_NAME') : $ref->getShortName();
		$tableSchema->columnNames = $columnNames;
		$tableSchema->primaryKey = $ref->hasConstant('PRIMARY_KEY') ? $ref->getConstant('PRIMARY_KEY') : array_shift($columnNames);
		$tableSchema->attributeDefaults = $attributes;
		
		return $this->_tableSchemas[$entityName] = $tableSchema;
	}
}
