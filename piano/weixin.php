<?php
/**
 * 微信开发者认证
 */
define('WEIXIN_TOKEN', '60cf2a34ab7d21083cca8642482c092a');

if (checkSignature() === true) {
	echo $_GET['echostr'];
}

/**
 * 验证URL有效性
 * 
 * @return bool
 */
function checkSignature() {
	$signature = isset($_GET['signature']) ? $_GET['signature'] : '';
	$timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
	$nonce = isset($_GET['nonce']) ? $_GET['nonce'] : '';
	$token = WEIXIN_TOKEN;
	$tmpArr = array($token, $timestamp, $nonce);
	sort($tmpArr, SORT_STRING);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );

	if ($tmpStr == $signature ) {
		return true;
	} else {
		return false;
	}
}
