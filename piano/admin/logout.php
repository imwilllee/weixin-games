<?php
/**
 * 退出登陆
 */
    define('APP_ROOT', dirname(__DIR__));
    require APP_ROOT . '/core/bootstrap.php';
    if (!empty($_SESSION['user'])) {
        unset($_SESSION['user']);
        set_alert('退出成功！', 'info');
    }
    redirect('login.php');
