<?php
/**
 * 数据管理
 */
    define('APP_ROOT', dirname(__DIR__));
    require APP_ROOT . '/core/bootstrap.php';
    require APP_CLASS . '/pagination.php';

    // 分页设置
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    // 每页显示数量
    $size = 10;
    $offset = $size * ($page - 1);
    // 数据总数
    $count = $db->count(
        'rank_list'
    );
    $pagination = new Pagination($page, $count);
    $pagination->setPrevious('上一页');
    $pagination->setNext('下一页');
    $pagination->setRPP($size);
    $pages = ceil($count / $size);

    // 查询数据
    if ($count > 0 && ($page > 0 && $page <= $pages)) {
        $ranks = $db->select(
            'rank_list',
            array('id', 'nickname', 'tel', 'score', 'post_ip', 'post_date'),
            array(
                'ORDER' => array('id DESC'),
                'LIMIT' => array($offset, $size)
            )
        );
    } else {
        $ranks = array();
        set_alert('未查询到数据！', 'info');
    }
?>
<?php include __DIR__ .'/common/header.php'; ?>
<?php include __DIR__ .'/common/navbar.php'; ?>
    <div class="container">
        <?php echo show_alert(); ?>
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="active">数据管理</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <div class="box-tools">
                            <div class="pull-right">
                                <a href="export.php?type=data" class="btn btn-primary">导出数据</a>
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>昵称</th>
                                    <th>联系方式</th>
                                    <th>分数</th>
                                    <th>IP地址</th>
                                    <th>提交日期</th>
                                </tr>
                                </thead>
                                <tbody>
                            <?php foreach($ranks as $rank): ?>
                                <tr>
                                    <td><?php echo $rank['id']; ?></td>
                                    <td><?php echo h($rank['nickname']); ?></td>
                                    <td><?php echo h($rank['tel']); ?></td>
                                    <td><?php echo $rank['score']; ?></td>
                                    <td><?php echo h($rank['post_ip']); ?></td>
                                    <td><?php echo $rank['post_date']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                                </tbody>
                        </table>
                    </div>

                    <div class="box-footer">
                            <?php echo $pagination->parse(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include __DIR__ .'/common/footer.php'; ?>