<?php
/**
 * 导出数据
 */
	define('APP_ROOT', dirname(__DIR__));
	require APP_ROOT . '/core/bootstrap.php';
	require APP_CLASS . '/export.php';
	// 类型定义
	$types = array('data', 'rank');
	if (empty($_GET['type']) || !in_array($_GET['type'], $types)) {
		set_alert('参数错误！', 'warning');
		redirect('index.php');
	}
	$filename = date('Ymd') . '-export.csv';
	$export = new ExportDataCSV('browser', $filename);
	$export->initialize();
	// 数据导出
	if ($_GET['type'] == 'data') {
		$export->addRow(array('ID', '昵称', '联系方式', '分数', 'IP地址', '提交日期'));
		$ranks = $db->select(
			'rank_list',
			array('id', 'nickname', 'tel', 'score', 'post_ip', 'post_date'),
			array(
				'ORDER' => array('id DESC')
			)
		);
		foreach ($ranks as $rank) {
			$export->addRow(
				array(
					$rank['id'],
					$rank['nickname'],
					$rank['tel'],
					$rank['score'],
					$rank['post_ip'],
					$rank['post_date']
				)
			);
		}
	} elseif($_GET['type'] == 'rank') {
		$export->addRow(array('排名', '昵称', '联系方式', '分数', 'IP地址', '提交日期'));
		$top = isset($_GET['top']) ? (int)$_GET['top'] : 10;
		$range = range(10, $config['common']['max_top'], 10);
		if (!in_array($top, $range)) {
			set_alert('参数错误！', 'warning');
			redirect('rank_list.php');
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
		$i = 1;
		foreach ($ranks as $rank) {
			$export->addRow(
				array(
					$i,
					$rank['nickname'],
					$rank['tel'],
					$rank['score'],
					$rank['post_ip'],
					$rank['post_date']
				)
			);
			$i++;
		}
	}
	$export->finalize();
