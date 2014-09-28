<?php
/**
 * 共通函数文件
 */

/**
 * 获取环境变量
 * 
 * @param string $key 索引
 * @return string
 */
function env($key) {
	if ($key === 'HTTPS') {
		if (isset($_SERVER['HTTPS'])) {
			return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		}
		return (strpos(env('SCRIPT_URI'), 'https://') === 0);
	}

	if ($key === 'SCRIPT_NAME') {
		if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
			$key = 'SCRIPT_URL';
		}
	}

	$val = null;
	if (isset($_SERVER[$key])) {
		$val = $_SERVER[$key];
	} elseif (isset($_ENV[$key])) {
		$val = $_ENV[$key];
	} elseif (getenv($key) !== false) {
		$val = getenv($key);
	}

	if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
		$addr = env('HTTP_PC_REMOTE_ADDR');
		if ($addr !== null) {
			$val = $addr;
		}
	}

	if ($val !== null) {
		return $val;
	}

	switch ($key) {
		case 'DOCUMENT_ROOT':
			$name = env('SCRIPT_NAME');
			$filename = env('SCRIPT_FILENAME');
			$offset = 0;
			if (!strpos($name, '.php')) {
				$offset = 4;
			}
			return substr($filename, 0, -(strlen($name) + $offset));
		case 'PHP_SELF':
			return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
		case 'CGI_MODE':
			return (PHP_SAPI === 'cgi');
		case 'HTTP_BASE':
			$host = env('HTTP_HOST');
			$parts = explode('.', $host);
			$count = count($parts);

			if ($count === 1) {
				return '.' . $host;
			} elseif ($count === 2) {
				return '.' . $host;
			} elseif ($count === 3) {
				$gTLD = array(
					'aero',
					'asia',
					'biz',
					'cat',
					'com',
					'coop',
					'edu',
					'gov',
					'info',
					'int',
					'jobs',
					'mil',
					'mobi',
					'museum',
					'name',
					'net',
					'org',
					'pro',
					'tel',
					'travel',
					'xxx'
				);
				if (in_array($parts[1], $gTLD)) {
					return '.' . $host;
				}
			}
			array_shift($parts);
			return '.' . implode('.', $parts);
	}
	return null;
}

/**
 * 安全输出内容
 * 
 * @param string  $text    内容
 * @param boolean $double  编码现有内容
 * @param string  $charset 编码
 * @return string
 */
function h($text, $double = true, $charset = 'UTF-8') {
	if (is_array($text)) {
		$texts = array();
		foreach ($text as $k => $t) {
			$texts[$k] = h($t, $double, $charset);
		}
		return $texts;
	} elseif (is_object($text)) {
		if (method_exists($text, '__toString')) {
			$text = (string)$text;
		} else {
			$text = '(object)' . get_class($text);
		}
	} elseif (is_bool($text)) {
		return $text;
	}

	if (is_string($double)) {
		$charset = $double;
	}
	return htmlspecialchars($text, ENT_QUOTES, $charset, $double);
}

/**
 * 调试输出
 * 
 * @param mixd $var 对象变量
 * @return string
 */
function pr($var) {
	$template = '<pre>%s</pre>';
	printf($template, print_r($var, true));
}

/**
 * 密码加密
 * 
 * @param string $password 密码
 * @return string
 */
function encode_password($password) {
	return md5(ENCODE_KEY . $password);
}

/**
 * 跳转页面
 * 
 * @param string $url 链接
 * @return void
 */
function redirect($url) {
	header('Location:' . $url);
	exit();
}

/**
 * 设置提示信息
 * 
 * @param string $message 内容
 * @param string $type    类型 danger,warning,info,success
 * @return void
 */
function set_alert($message, $type = 'danger') {
	$_SESSION['_alert'] = array(
		'message' => $message,
		'class' => $type
	);
}

/**
 * 显示提示信息
 * 
 * @return string
 */
function show_alert() {
	if (!empty($_SESSION['_alert'])) {
		$message = $_SESSION['_alert']['message'];
		$class = $_SESSION['_alert']['class'];
		$template = '<div class="alert alert-%s">%s</div>';
		unset($_SESSION['_alert']);
		return sprintf($template, $class, $message);
	}
	return null;
}

/**
 * 设置默认value
 * 
 * @param string $key 名称
 * @return string
 */
function v($key) {
	return isset($_POST[$key]) ? h($_POST[$key]) : false;
}
