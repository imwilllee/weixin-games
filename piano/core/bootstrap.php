<?php
/**
 * 系统引导文件
 */
// 域名地址必须以/结尾。
define('APP_DOMAIN', 'http://115.28.169.20/');
define('APP_CORE', APP_ROOT . '/core');
define('APP_CLASS', APP_ROOT . '/core/class');
// 配置文件
$config = include APP_CORE . '/config.php';
// 时区设置
date_default_timezone_set('PRC');
// session名称
session_name('WX_SESSID');
// 使用cookie
ini_set('session.use_cookies', 1);
// 过期时间
ini_set('session.cookie_lifetime', $config['common']['session_time']);
ini_set('session.gc_maxlifetime', $config['common']['session_time']);
// 自动启动
if (ini_get('session.auto_start') != 1) {
	session_start();
}

if (function_exists('mb_internal_encoding')) {
	$encoding = 'UTF-8';
	if (!empty($encoding)) {
		mb_internal_encoding($encoding);
	}
	if (!empty($encoding) && function_exists('mb_regex_encoding')) {
		mb_regex_encoding($encoding);
	}
}
// 共通函数
require APP_CORE . '/func.php';
// 数据库操作类
require APP_CLASS . '/medoo.php';
// 共通方法类库
require APP_CLASS . '/app.php';
$db = new Medoo($config['database']);
$app = new App();

define('DATETIME_NOW', date('Y-m-d H:i:s', env('REQUEST_TIME')));
define('ENCODE_KEY', '328d97f75ba5e7774dfc');
define('BASENAME', basename(env('PHP_SELF')));
// 允许不登陆访问页面
$allows = array(
	'login.php',
	'logout.php'
);
// 权限验证
if (!defined('ALLOW_FLG')) {
	if (!in_array(BASENAME, $allows)) {
		if (empty($_SESSION['user'])) {
			set_alert('请先登陆系统！', 'warning');
			redirect('login.php');
		}
	}
}