<?php
/**
 * 更改密码
 */
    define('APP_ROOT', dirname(__DIR__));
    require APP_ROOT . '/core/bootstrap.php';
    $error = false;
    if ($app->is('post')) {
        if (!isset($_POST['password']) || $_POST['password'] == '') {
            $error['password'] = '请填写原始密码！';
        }
        if (!isset($_POST['new_password']) || $_POST['new_password'] == '') {
            $error['new_password'] = '请填写新密码！';
        }
        if (!isset($_POST['confirm_password']) || $_POST['confirm_password'] == '') {
            $error['confirm_password'] = '请填写确认新密码！';
        }
        if (!isset($error['new_password'])) {
            if (!preg_match('/^[0-9a-zA-Z]{6,32}$/i', $_POST['new_password'])) {
                $error['new_password'] = '新密码格式不正确！';
            }
        }
        if (!isset($error['confirm_password'])) {
            if ($_POST['new_password'] != $_POST['confirm_password']) {
                $error['confirm_password'] = '两次密码输入不一致！';
            }
        }
        if (!isset($error['password'])) {
            if (encode_password($_POST['password']) != $_SESSION['user']['password']) {
                $error['password'] = '原始密码错误！';
            }
        }
        if (!empty($error)) {
            set_alert('验证出错，请根据错误提示修正！');
        } else {
            $update = array(
                'password' => encode_password($_POST['new_password']),
                'update_date' => DATETIME_NOW
            );
            $where = array('id' => $_SESSION['user']['id']);
            if ($db->update('user', $update, $where )) {
                set_alert('密码修改成功，下次登录生效！', 'success');
                $_SESSION['user']['password'] = $update['password'];
                redirect('change_password.php');
            } else {
                set_alert('密码修改失败！');
            }
        }
    }
?>
<?php include __DIR__ .'/common/header.php'; ?>
<?php include __DIR__ .'/common/navbar.php'; ?>
    <div class="container">
        <?php echo show_alert(); ?>
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="active">修改密码</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <form role="form" action="change_password.php" method="post">
                            <div class="form-group<?php echo isset($error['password']) ? ' has-error' : ''; ?>">
                                <label>原始密码</label>
                                <input type="password" name="password" class="form-control" placeholder="原始密码" value="<?php echo v('password'); ?>" autofocus>
                                <?php if (isset($error['password'])): ?>
                                <p class="text-danger"><?php echo $error['password']; ?></p>
                                <?php endif;?>
                            </div>
                            <div class="form-group<?php echo isset($error['new_password']) ? ' has-error' : ''; ?>">
                                <label>新密码</label>
                                <input type="password" name="new_password" class="form-control" placeholder="新密码" value="<?php echo v('new_password'); ?>">
                                <p class="text-primary">半角英文数字长度6-32位。</p>
                                <?php if (isset($error['new_password'])): ?>
                                <p class="text-danger"><?php echo $error['new_password']; ?></p>
                                <?php endif;?>
                            </div>
                            <div class="form-group<?php echo isset($error['confirm_password']) ? ' has-error' : ''; ?>">
                                <label>确认新密码</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="确认新密码" value="<?php echo v('confirm_password'); ?>">
                                <?php if (isset($error['confirm_password'])): ?>
                                <p class="text-danger"><?php echo $error['confirm_password']; ?></p>
                                <?php endif;?>
                            </div>
                            <button type="submit" class="btn btn-primary">确认修改</button>
                        </form>
                    </div>
                    <div class="box-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include __DIR__ .'/common/footer.php'; ?>

