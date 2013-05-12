<?php
namespace idea;

/**
 * Dir Name Define file
 * 定义项目常用目录
 * @author 宋欢 <iphper@yeah.net>
 * @version $Id: DirName.php 1 2013-03-29 16:48:06Z huan.song $
 * @package idea
 * @since 1.0
 */

/**
 * 调用此框架前需要先定义常量：项目名称
 */
defined('APP_NAME') || exit('Request Error, No defined APP_NAME');

/**
 * 不同操作系统的目录分割符
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/**
 * 不同操作系统的路径分割符
 */
defined('PS') || define('PS', PATH_SEPARATOR);

/**
 * IDEA框架目录
 */
defined('DIR_IDEA') || define('DIR_IDEA', dirname(__FILE__));

/**
 * 公共框架和代码库目录
 */
defined('DIR_LIBRARIES') || define('DIR_LIBRARIES', substr(DIR_IDEA, 0, -5));

/**
 * ROOT目录
 */
defined('DIR_ROOT') || define('DIR_ROOT', substr(DIR_LIBRARIES, 0, -10));

/**
 * 当前项目目录
 */
defined('DIR_APP') || define('DIR_APP', DIR_ROOT . DS . 'app' . DS . APP_NAME);

/**
 * 当前项目的公共代码库目录
 */
defined('DIR_APP_LIBRARY') || define('DIR_APP_LIBRARY', DIR_APP . DS . 'library');

/**
 * 当前项目的所有模块存放目录
 */
defined('DIR_APP_MODULES') || define('DIR_APP_MODULES', DIR_APP . DS . 'modules');

/**
 * 当前项目的脚本存放目录
 */
defined('DIR_APP_SCRIPTS') || define('DIR_APP_SCRIPTS', DIR_APP . DS . 'scripts');

/**
 * 当前项目的测试代码存放目录
 */
defined('DIR_APP_TESTS') || define('DIR_APP_TESTS', DIR_APP . DS . 'tests');

/**
 * 当前项目的模版存放目录
 */
defined('DIR_APP_VIEWS') || define('DIR_APP_VIEWS', DIR_APP . DS . 'views');

/**
 * 配置文件根目录
 */
defined('DIR_CONF') || define('DIR_CONF', DIR_ROOT . DS . 'conf');

/**
 * 当前项目的配置文件存放目录
 */
defined('DIR_CONF_APP') || define('DIR_CONF_APP', DIR_CONF . DS . 'app' . DS . APP_NAME);

/**
 * 数据库的配置文件存放目录
 */
defined('DIR_CONF_DB') || define('DIR_CONF_DB', DIR_CONF . DS . 'db');

/**
 * Ral的配置文件存放目录
 */
defined('DIR_CONF_RAL') || define('DIR_CONF_RAL', DIR_CONF . DS . 'ral');

/**
 * 缓存的配置文件存放目录
 */
defined('DIR_CONF_CACHE') || define('DIR_CONF_CACHE', DIR_CONF . DS . 'cache');

/**
 * 数据文件存放根目录
 */
defined('DIR_DATA') || define('DIR_DATA', DIR_ROOT . DS . 'data');

/**
 * 当前项目的数据文件存放目录
 */
defined('DIR_DATA_APP') || define('DIR_DATA_APP', DIR_DATA . DS . 'app' . DS . APP_NAME);

/**
 * 运行时生成的临时文件存放目录
 */
defined('DIR_DATA_RUNTIME') || define('DIR_DATA_RUNTIME', DIR_DATA . DS . 'runtime');

/**
 * 日志文件存放根目录
 */
defined('DIR_LOG') || define('DIR_LOG', DIR_ROOT . DS . 'log');

/**
 * 当前项目的日志文件存放目录
 */
defined('DIR_LOG_APP') || define('DIR_LOG_APP', DIR_LOG . DS . APP_NAME);

/**
 * 网站入口目录
 */
defined('DIR_WEBROOT') || define('DIR_WEBROOT', DIR_ROOT . DS . 'webroot');

/**
 * 当前项目的静态文件存放目录
 */
defined('DIR_WEBROOT_STATIC') || define('DIR_WEBROOT_STATIC', DIR_WEBROOT . DS . 'static' . DS . APP_NAME);

/**
 * 初始化日志文件存放根目录、当前项目的日志文件存放目录
 */
is_dir(DIR_LOG_APP) || mkdir(DIR_LOG_APP, 0664, true);
is_dir(DIR_LOG_APP) || exit('Request Error, Create Log Dir Failed');

/**
 * 初始化数据文件存放根目录、当前项目的数据文件存放目录
 */
is_dir(DIR_DATA_RUNTIME) || mkdir(DIR_DATA_RUNTIME, 0664, true);
is_dir(DIR_DATA_RUNTIME) || exit('Request Error, Create RunTime Dir Failed');
