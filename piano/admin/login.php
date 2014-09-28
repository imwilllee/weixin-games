<?php
/**
 * 管理端登陆
 */
    define('APP_ROOT', dirname(__DIR__));
    require APP_ROOT . '/core/bootstrap.php';
    if (!empty($_SESSION['user'])) {
        redirect('index.php');
    }
    if ($app->is('post')) {
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $user = $db->get(
                'user',
                array('id', 'username', 'password', 'last_login_ip', 'last_login_date'),
                array(
                    'AND' => array(
                        'username' => $_POST['username'],
                        'password' => encode_password($_POST['password'])
                    )
                )
            );
            if (!empty($user)) {
                $user['current_login_id'] = $app->clientIp();
                $user['current_login_date'] = DATETIME_NOW;
                $_SESSION['user'] = $user;
                $db->update(
                    'user',
                    array(
                        'last_login_ip' => $user['current_login_id'],
                        'last_login_date' => $user['current_login_date']
                    ),
                    array('id' => $user['id'])
                );

                redirect('index.php');
            } else {
                set_alert('用户名不存在或者密码错误！');
            }
        } else {
            set_alert('请输入用户名和密码！');
        }
    }
?>
<?php include __DIR__ .'/common/header.php'; ?>
        <style type="text/css">
            body {
              padding-top: 40px;
              padding-bottom: 40px;
              background-color: #eee;
            }
        </style>

        <div class="container">
            <form class="form-signin" method="post">
                <h2 class="form-signin-heading">管理员登陆</h2>
                <?php echo show_alert(); ?>
                <input type="text" name="username" class="form-control" placeholder="用户名" value="<?php echo v('username'); ?>" autofocus>
                <input type="password" name="password" class="form-control" placeholder="密码">
                <button class="btn btn-lg btn-primary btn-block" type="submit">登陆系统</button>
            </form>
        </div>
<?php include __DIR__ .'/common/footer.php'; ?>