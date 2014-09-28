<?php
/**
 * 获取排行榜
 */
	define('ALLOW_FLG', true);
	define('APP_ROOT', dirname(dirname(dirname(__DIR__))));
	require APP_ROOT . '/core/bootstrap.php';
	if (!$app->is('weixin')) {
		header('HTTP/1.1 404 Not Found');
		header('status: 404 Not Found');
		exit();
	}
	if ($app->is('ajax') && $app->is('post')) {
		// 查询现有排行信息
		$exist = $db->get(
			'rank_list',
			array('id', 'nickname', 'tel', 'score'),
			array(
				'AND' => array(
					'guid' => $_POST['guid'],
					'status' => 0
				),
				'ORDER' => array('score DESC')
			)
		);
		$status = 0;
		// 如果已经存在
		if (!empty($exist)) {
			// 比较现有分数
			if ($_POST['score'] >= $exist['score']) {
				$db->update(
					'rank_list',
					array('status' => 1),
					array('id' => $exist['id'])
				);
			} else {
				if ($_POST['nickname'] != $exist['nickname'] || $_POST['tel'] != $exist['tel']) {
					// 更新最新的名字和号码
					$db->update(
						'rank_list',
						array(
							'nickname' => $_POST['nickname'],
							'tel' => $_POST['tel']
						),
						array('id' => $exist['id'])
					);
				}
				$status = 1;
			}
		}
		// 新数据提交
		$data = array(
			'nickname' => $_POST['nickname'],
			'tel' => $_POST['tel'],
			'score' => $_POST['score'],
			'guid' => $_POST['guid'],
			'post_ip' => $app->clientIp(),
			'post_date' => DATETIME_NOW,
			'post_ua' => env('HTTP_USER_AGENT'),
			'status' => $status
		);
		if ($id = $db->insert('rank_list', $data)) {
			// 排名前十
			$ranks = $db->select(
				'rank_list',
				array('nickname', 'score', 'guid'),
				array(
					'ORDER' => array('score DESC', 'id DESC'),
					'LIMIT' => 10,
					'status' => 0
				)
			);
			$i = 1;
			foreach ($ranks as &$rank) {
				$rank['order'] = $i;
				$i++;
			}
			// 当前分数之前排名
			$beforeCount = $db->count(
				'rank_list',
				array(
					'ORDER' => array('score DESC', 'id DESC'),
					'AND' => array(
						'status' => 0,
						'score[>]' => $data['score']
					)
				)
			);
			// 超出十位
			if ($beforeCount >= 10) {
				$currentCount = $db->count(
					'rank_list',
					array(
						'AND' => array(
							'status' => 0,
							'score' => $data['score'],
							'id[>]' => $id
						)
					)
				);
				$ranks[] = array(
					'order' => $beforeCount + $currentCount + 1,
					'nickname' => $data['nickname'],
					'score' => $data['score'],
					'guid' => $data['guid']
				);
			}

			echo json_encode($ranks);
		}
	}