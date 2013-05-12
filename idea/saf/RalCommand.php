<?php
namespace idea\saf;

use idea\Core;
use idea\ap\ErrorException;
use idea\util\Ral;

/**
 * RalCommand class file
 * Ral操作类
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: RalCommand.php 1 2013-04-05 01:38:06Z huan.song $
 * @package idea.saf
 * @since 1.0
 */
class RalCommand
{
	/**
	 * @var integer 执行失败后，尝试重试的最大次数
	 */
	const MAX_RETRY_TIMES = 3;
	
	/**
	 * @var string 寄存Ral配置名
	 */
	protected $_clusterName = null;
	
	/**
	 * @var array 寄存Ral配置
	 */
	protected $_config = null;
	
	/**
	 * @var instance of idea\util\Ral
	 */
	protected $_ral = null;
	
	/**
	 * 构造方法：初始化Ral配置名
	 * @param string $clusterName
	 */
	public function __construct($clusterName)
	{
		$this->_clusterName = $clusterName;
	}
	
	/**
	 * CURL方式提交数据
	 * @param string $pathinfo
	 * @param array $params
	 * @param string $method
	 * @return mixed
	 */
	public function talk($pathinfo, array $params = array(), $method = 'GET')
	{
		$start = gettimeofday();
		$config = $this->getConf();
		$ral = $this->getRal();
		$ral->setLogId(Core::getLogger()->getId());
		for ($retry = 0; $retry < $config['retry']; $retry++) {
			try {
				$result = $ral->talk($pathinfo);
				$message = 'ral talk successfully';
				$code = 0;
			}
			catch (ErrorException $e) {
				$message = 'ral talk failed, ' . $e->getMessage();
				$code = $e->getCode();
			}
			$event = array(
				'msg' => $message,
				'retry' => $retry,
				'conf' => serialize($config)
			);
			if ($code === 0) {
				Core::log('notice', $event, __METHOD__, $code);
				return $result;
			}
			else {
				Core::log('warning', $event, __METHOD__, $code);
			}
		}
		
		return false;
	}
	
	/**
	 * 获取Ral对象
	 * @return idea\util\Ral
	 */
	public function getRal()
	{
		if ($this->_ral === null) {
			$config = $this->getConf();
			$this->_ral = new Ral($config['server'], $config['port'], $config['connect_time_out_ms'], $config['time_out_ms'], $config['converter']);
		}
		return $this->_ral;
	}
	
	/**
	 * 获取数据库配置
	 * @return array
	 * @throws ErrorException 如果没有指定服务器名称或IP地址、服务器端口号、连接超时、执行超时或获取数据后转码方式，抛出异常
	 */
	public function getConf()
	{
		if ($this->_config === null) {
			$config = Conf::getRalConf($this->_clusterName);
			if (!isset($config['server']) || !isset($config['port']) || !isset($config['connect_time_out_ms']) || !isset($config['time_out_ms'])|| !isset($config['converter'])) {
				throw new ErrorException(sprintf(
					'RalCommand no entry is registered for key: server|port|connect_time_out_ms|time_out_ms|converter in ral config "%s"', serialize($config)
				));
			}
			$config['retry'] = isset($config['retry']) ? (int) $config['retry'] : MAX_RETRY_TIMES;
			$this->_config = $config;
		}
		
		return $this->_config;
	}
}
