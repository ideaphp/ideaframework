<?php
namespace idea\db;

/**
 * TableSchema class file
 * 寄存数据库表的概要描述，包含表名、主键、字段名、字段默认值等
 * <ul>
 * <li>{@link $name}</li>
 * <li>{@link $primaryKey}</li>
 * <li>{@link $columnNames}</li>
 * <li>{@link $columns}</li>
 * <li>{@link $attributeDefaults}</li>
 * </ul>
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: TableSchema.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea.db
 * @since 1.0
 */
class TableSchema
{
	/**
	 * @var string 表名
	 */
	public $name;
	
	/**
	 * @var string 表的主键
	 */
	public $primaryKey;
	
	/**
	 * @var array 所有的列名
	 */
	public $columnNames = array();
	
	/**
	 * @var array 所有的列信息
	 */
	public $columns = array();
	
	/**
	 * @var array 所有默认的值
	 */
	public $attributeDefaults = array();
	
	/**
	 * 判断列名是否存在
	 * @param string $name
	 * @return boolean
	 */
	public function hasColumn($name)
	{
		return in_array($name, $this->columnNames);
	}
}
