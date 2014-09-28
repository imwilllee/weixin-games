<?php
/**
 * 排行榜
 */
    define('APP_ROOT', dirname(__DIR__));
    require APP_ROOT . '/core/bootstrap.php';
    $top = isset($_GET['top']) ? (int)$_GET['top'] : 10;
    $range = range(10, $config['common']['max_top'], 10);
    if (!in_array($top, $range)) {
        set_alert('参数错误！', 'warning');
        $top = 10;
    }
    $ranks = $db->select(
        'rank_list',
        array('id', 'nickname', 'tel', 'score', 'post_ip', 'post_date'),
        array(
            'ORDER' => array('score DESC', 'id DESC'),
            'LIMIT' => $top,
            'status' => 0
        )
    );
    $colors = array(
        1 => 'danger',
        2 => 'info',
        3 => 'warning'
    );
?>
<?php include __DIR__ .'/common/header.php'; ?>
<?php include __DIR__ .'/common/navbar.php'; ?>
    <div class="container">
        <?php echo show_alert(); ?>
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="active">排行榜</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <div class="box-tools">
                            <div class="pull-left">
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon">前</span>
                                        <select class="form-control" onchange="location.href='rank_list.php?top=' + this.value">
                                        <?php foreach ($range as $key =>$val): ?>
                                            <?php if($val == $top): ?>
                                            <option value="<?php echo $val; ?>" selected><?php echo $val; ?></option>
                                            <?php else: ?>
                                            <option value="<?php echo $val; ?>"><?php echo $val; ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        </select>
                                        <span class="input-group-addon">名</span>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="pull-right">
                                        <a href="export.php?type=rank&top=<?php echo $top; ?>" class="btn btn-primary">导出数据</a>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>排名</th>
                                    <th>昵称</th>
                                    <th>联系方式</th>
                                    <th>分数</th>
                                    <th>IP地址</th>
                                    <th>提交日期</th>
                                </tr>
                                </thead>
                                <tbody>
                            <?php $i = 1; ?>
                            <?php foreach($ranks as $rank): ?>
                                <?php if(isset($colors[$i])): ?>
                                <tr class="<?php echo $colors[$i]; ?>">
                                <?php else:?>
                                <tr>
                                <?php endif; ?>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo h($rank['nickname']); ?></td>
                                    <td><?php echo h($rank['tel']); ?></td>
                                    <td><?php echo $rank['score']; ?></td>
                                    <td><?php echo h($rank['post_ip']); ?></td>
                                    <td><?php echo $rank['post_date']; ?></td>
                                </tr>
                            <?php $i++; ?>
                            <?php endforeach; ?>
                                </tbody>
                        </table>
                    </div>

                    <div class="box-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include __DIR__ .'/common/footer.php'; ?>

