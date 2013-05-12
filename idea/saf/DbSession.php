<?php
namespace idea\saf;

use idea\ap\interfaces\SessionSaveHandler;

/**
 * DbSession class file
 * 用数据库存储会话，默认自动创建SESSION表
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: DbSession.php 1 2013-04-05 20:00:06Z huan.song $
 * @package idea.saf
 * @since 1.0
 */
class DbSession implements SessionSaveHandler
{
	/**
	 * @var instance of cms\helper\DbCommand
	 */
	protected $_dbCommand = null;
	
	/**
	 * @var string 会话表名
	 */
	protected $_table = 'session';
	
	/**
	 * @var boolean 是否自动创建Session表
	 */
	protected $_autoCreateSessTable = true;
	
	/**
	 * 构造方法：初始化数据库操作类、Session表名、是否自动创建Session表
	 * @param cms\helper\DbCommand $dbCommand
	 * @param string $table
	 * @param boolean $autoCreateSessTable
	 */
	public function __construct(DbCommand $dbCommand, $table = null, $autoCreateSessTable = false)
	{
		$this->_dbCommand = $dbCommand;
		if ($table !== null) {
			$this->_table = (string) $table;
		}
		$this->_autoCreateSessTable = (boolean) $autoCreateSessTable;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see idea\ap\interfaces.SessionSaveHandler::open()
	 */
	public function open($path, $name)
	{
		if ($this->_autoCreateSessTable) {
			$this->createSessTable($this->_table);
		}
		$this->gc((int) ini_get('session.gc_maxlifetime'));
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see idea\ap\interfaces.SessionSaveHandler::close()
	 */
	public function close()
	{
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see idea\ap\interfaces.SessionSaveHandler::read()
	 */
	public function read($sessId)
	{
		$sql = 'SELECT `data` FROM `' . $this->_table . '` WHERE `session_id` = ? LIMIT 1';
		return $this->_dbCommand->fetchColumn($sql, $sessId);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see idea\ap\interfaces.SessionSaveHandler::write()
	 */
	public function write($sessId, $data)
	{
		$sql = 'REPLACE INTO `' . $this->_table . '` (`dt_last_access`, `data`, `session_id`) VALUES (?, ?, ?)';
		$params = array(mktime(), $data, $sessId);
		return $this->_dbCommand->query($sql, $params);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see idea\ap\interfaces.SessionSaveHandler::destroy()
	 */
	public function destroy($sessId)
	{
		$sql = 'DELETE FROM `' . $this->_table . '` WHERE `session_id` = ?';
		return $this->_dbCommand->query($sql, $sessId);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see idea\ap\interfaces.SessionSaveHandler::gc()
	 */
	public function gc($maxLifeTime)
	{
		$sql = 'DELETE FROM `' . $this->_table . '` WHERE `dt_last_access` < ?';
		return $this->_dbCommand->query($sql, mktime() - $maxLifeTime);
	}
	
	/**
	 * 通过表名，创建会话表
	 * @param string $table
	 * @return boolean
	 */
	public function createSessTable($table)
	{
		$sql = "
CREATE TABLE IF NOT EXISTS $table (
	session_id     CHAR(32) NOT NULL COMMENT 'SESSION_ID',
	dt_last_access INT      NOT NULL COMMENT '最后一次访问SESSION的时间',
	data           TEXT     NOT NULL COMMENT 'SESSION的内容',
	dt_start       INT      NOT NULL COMMENT '第一次访问SESSION的时间'
);
";
		return $this->_dbCommand->query($sql);
	}
	
	public function __destruct()
	{
		session_write_close();
	}
}
