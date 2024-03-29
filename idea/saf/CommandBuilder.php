<?php
namespace idea\saf;

/**
 * CommandBuilder class file
 * 创建简单的MySQL执行命令
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: CommandBuilder.php 1 2013-04-05 01:38:06Z huan.song $
 * @package idea.saf
 * @since 1.0
 */
class CommandBuilder
{
	/**
	 * @var string 填充SQL字符
	 */
	const PLACE_HOLDERS = '?';
	
	/**
	 * 创建查数据的命令
	 * @param string $table
	 * @param array $columnNames
	 * @param string $condition
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @return string
	 */
	public function createFind($table, array $columnNames = array(), $condition = '', $order = '', $limit = 0, $offset = 0)
	{
		$command = 'SELECT ' . implode(', ', $this->quoteColumnNames($columnNames)) . ' FROM ' . $this->quoteTableName($table);
		$command = $this->applyCondition($command, $condition);
		$command = $this->applyOrder($command, $order);
		$command = $this->applyLimit($command, $limit, $offset);
		return $command;
	}
	
	/**
	 * 创建查记录数的命令
	 * @param string $table
	 * @param string $condition
	 * @return string
	 */
	public function createCount($table, $condition = '')
	{
		$command = 'SELECT COUNT(*) AS total FROM ' . $this->quoteTableName($table);
		return $this->applyCondition($command, $condition);
	}
	
	/**
	 * 创建添加数据的命令
	 * @param string $table
	 * @param array $columnNames
	 * @param boolean $ignore
	 * @return string
	 */
	public function createInsert($table, array $columnNames = array(), $ignore = false)
	{
		$command = 'INSERT ' . ($ignore ? 'IGNORE INTO ' : 'INTO ') .  $this->quoteTableName($table);
		$command .= ' (' . implode(', ', $this->quoteColumnNames($columnNames)) . ') VALUES';
		$command .= ' (' . rtrim(str_repeat(self::PLACE_HOLDERS . ', ', count($columnNames)), ', ') . ')';
		return $command;
	}
	
	/**
	 * 创建添加数据的命令，如果主键或唯一键存在则执行更新命令
	 * @param string $table
	 * @param array $columnNames
	 * @param string $onDup
	 * @param boolean $ignore
	 * @return string
	 */
	public function createInsertOnDup($table, array $columnNames = array(), $onDup = '', $ignore = false)
	{
		return $this->createInsert($table, $columnNames, $ignore) . ' ON DUPLICATE KEY UPDATE ' . $onDup;
	}
	
	/**
	 * 创建更新数据的命令
	 * @param string $table
	 * @param array $columnNames
	 * @param string $condition
	 * @return string
	 */
	public function createUpdate($table, array $columnNames = array(), $condition = '')
	{
		$command = 'UPDATE ' . $this->quoteTableName($table) . ' SET ';
		$command .= implode(' = ' . self::PLACE_HOLDERS . ', ', $this->quoteColumnNames($columnNames)) . ' = ' . self::PLACE_HOLDERS;
		return $this->applyCondition($command, $condition);
	}
	
	/**
	 * 创建更新数据的命令，如果数据存在则更新，如果数据不存在则添加
	 * @param string $table
	 * @param array $columnNames
	 * @param string $condition
	 * @return string
	 */
	public function createReplace($table, array $columnNames = array())
	{
		$command = 'REPLACE INTO ' .  $this->quoteTableName($table);
		$command .= ' (' . implode(', ', $this->quoteColumnNames($columnNames)) . ') VALUES';
		$command .= ' (' . rtrim(str_repeat(self::PLACE_HOLDERS . ', ', count($columnNames)), ', ') . ')';
		return $command;
	}
	
	/**
	 * 创建删除数据的命令
	 * @param string $table
	 * @param string $condition
	 * @return string
	 */
	public function createDelete($table, $condition)
	{
		$command = 'DELETE FROM ' . $this->quoteTableName($table);
		return $this->applyCondition($command, $condition);
	}
	
	/**
	 * 根据键名创建条件命令
	 * @param array $columnNames
	 * @return string
	 */
	public function createAndCondition(array $columnNames = array())
	{
		return implode(' = ' . self::PLACE_HOLDERS . ' AND ', $this->quoteColumnNames($columnNames)) . ' = ' . self::PLACE_HOLDERS;
	}
	
	/**
	 * 向命令中追加条件语句
	 * @param string $command
	 * @param string $condition
	 * @return string
	 */
	public function applyCondition($command, $condition)
	{
		if ($condition != '') {
			return $command . ' WHERE ' . $condition;
		}
	
		return $command;
	}
	
	/**
	 * 向命令中追加条件“与”语句
	 * @param string $command
	 * @param string $condition
	 * @return string
	 */
	public function applyAndCondition($command, $condition)
	{
		if ($condition != '') {
			return $command . ' AND ' . $condition;
		}

		return $command;
	}
	
	/**
	 * 向命令中追加条件“或”语句
	 * @param string $command
	 * @param string $condition
	 * @return string
	 */
	public function applyOrCondition($command, $condition)
	{
		if ($condition != '') {
			return $command . ' OR ' . $condition;
		}
	
		return $command;
	}
	
	/**
	 * 向命令中追加排序语句
	 * @param string $command
	 * @param string $order
	 * @return string
	 */
	public function applyOrder($command, $order)
	{
		if ($order != '') {
			$command .= ' ORDER BY ' . $order;
		}
	
		return $command;
	}
	
	/**
	 * 向命令中追加限制查询条数语句
	 * @param string $command
	 * @param integer $limit
	 * @param integer $offset
	 * @return string
	 */
	public function applyLimit($command, $limit, $offset)
	{
		if (($limit = (int) $limit) >= 0) {
			$command .= ' LIMIT ' . $limit;
		}
	
		if (($offset = (int) $offset) > 0) {
			$command .=' OFFSET '. $offset;
		}
	
		return $command;
	}
	
	/**
	 * 引用一个表名，被引用的表名可以放在SQL语句中执行
	 * @param string $name
	 * @return string
	 */
	public function quoteTableName($name)
	{
		return '`' . $name . '`';
	}
	
	/**
	 * 引用一个列名，被引用的列名可以放在SQL语句中执行
	 * @param string $name
	 * @return string
	 */
	public function quoteColumnName($name)
	{
		return '`' . $name . '`';
	}
	
	/**
	 * 引用多个列名，被引用的列名可以放在SQL语句中执行
	 * @param array $names
	 * @return array
	 */
	public function quoteColumnNames(array $names)
	{
		return array_map(array($this, 'quoteColumnName'), $names);
	}
}
