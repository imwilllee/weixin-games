<?php
/**
 * 微信钢琴游戏
 */
    define('ALLOW_FLG', true);
    define('APP_ROOT', dirname(dirname(__DIR__)));
    require APP_ROOT . '/core/bootstrap.php';
    if (!$app->is('weixin')) {
        header('HTTP/1.1 404 Not Found');
        header('status: 404 Not Found');
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
    <meta name="format-detection" content="telephone=no,address=no,email=no">
    <meta name="mobileOptimized" content="width">
    <meta name="handheldFriendly" content="true">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="<?php echo $config['basic']['title']; ?>">
    <title><?php echo $config['basic']['title']; ?></title>
    <link href="favicon.ico" rel="apple-touch-icon">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/common.css" rel="stylesheet">
</head>
<body onload="init()" onOrientationChange="game.screenOrientation()">
    <div id="title">
        <div id="GameTimeLayer"></div>
    </div>

    <div id="container"></div>

    <div id="mask-start">
        <img id="modal-start-pet" src="assets/image/game-start.png">
        <p style="margin-top:30px;"><h3 id="hot-text" style="color:#ffffff;font-size:18px;"></h3></p>
        <img id="modal-btn-start" src="assets/image/btn-start.png">
        <img class="modal-btm-copyright" src="assets/image/copyright.png">
    </div>

    <div id="mask-over">

        <div id="result-area">
            <img class="modal-game-share" src="assets/image/game-share.png">
            <div class="btn-group btn-score-area">
                <div class="btn btn-piano-gray btn-current-score">
                    <div class="title-score">得分</div>
                    <div class="font-current-score"></div>
                </div>
                <div class="btn btn-piano-pink btn-highest-score">
                    <div class="title-score">最高分</div>
                    <div class="font-highest-score"></div>
                </div>
            </div>
            <button class="btn btn-piano-pink btn-rank"> <i class="iconfont icon-piano-cup"></i> 查看排名</button>
        </div>

        <div id="user-submit-area">
            <div class="btn-group btn-score-area btn-top-score-area">
                <div class="btn btn-piano-gray btn-current-score">
                    <div class="title-score">得分 </div>
                    <div class="font-current-score"></div> 
                </div>
                <div class="btn btn-piano-pink btn-highest-score">
                    <div class="title-score">最高分 </div>
                    <div class="font-highest-score"></div> 
                </div>
            </div>

            <div class="input-group">
                <span class="input-group-addon"><i class="iconfont icon-piano-userinfo"></i></span>
                <input type="text" class="form-control submit-nickname" placeholder="昵称 NICK NAME">
            </div>

            <div class="input-group">
                <span class="input-group-addon"><i class="iconfont icon-piano-phone"></i></span>
                <input type="tel" class="form-control submit-phone" placeholder="电话 CELL PHONE">
            </div>
            <button class="btn btn-piano-pink btn-submit">快查看排名吧</button>

            <div class="rank-area">
                <div class="rank-title btn-piano-pink">
                    <i class="iconfont icon-piano-cup"></i>得分排名
                </div>
                <ul class="rank-list">

                </ul>
            </div>
        </div>

        <div class="modal-btm-btn-area">
            <img class="modal-btm-btn btn-again" src="assets/image/btn-again.png">
            <img class="modal-btm-btn btn-share" src="assets/image/btn-share.png">
            <a href="<?php echo $config['weixin']['follow_url']; ?>">
                <img class="modal-btm-btn btn-attention" src="assets/image/btn-attention.png">
            </a>
        </div>
        <button class="info-window"></button>
        <img class="modal-btm-copyright" src="assets/image/copyright.png">
    </div>
    <script>
        var objTime = null;
    </script>
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/createjs.js"></script>
    <script src="assets/js/PianoMusics.js"></script>
    <script src="assets/js/game.js"></script>
    <script src="assets/js/common.js?v=001"></script>
    <script src="assets/js/sprintf.min.js"></script>
    <script>
        var _host = "http://" + window.location.host;
        document.addEventListener(
            'WeixinJSBridgeReady',
            function onBridgeReady(){
                // 隐藏底部toolbar栏
                WeixinJSBridge.call('hideToolbar');
            }
        );

        var dataForWeixin = {
            callback: function() {
                setTimeout(
                    function(){
                        attention_wx();
                    },
                    2000
                );
            }
        };

        $(function(){
            // 热点展示内容
            var hotTextList = [
                '新机场、4号线地铁首站、16500起<br>3万地价、凯德mail，4.2米loft',
                '低总价、自由购、微空间<br>领影票、电视机、小家电',
                '中秋节、游戏控'
            ];
            // 默认显示第一个
            $('#hot-text').html(hotTextList[0]);
            // 展示数量
            var hotCount = hotTextList.length - 1;
            // 下一个展示索引
            var nextId = 1;
            // 热点切换时间
            var switchTime = 2000;

            objTime = setInterval(
                function() {
                    if (nextId <= hotCount ) {
                        $('#hot-text').hide().html(hotTextList[nextId]).slideDown('slow');
                        nextId++;
                    } else {
                        nextId = 0;
                    }
                },
                switchTime
            );

            var onBridgeReady = function() {
                // 分享好友
                WeixinJSBridge.on('menu:share:appmessage',
                function(argv) {
                    var score = Number(sessionStorage.getItem("currentScore"));
                    WeixinJSBridge.invoke('sendAppMessage', {
                        "appid": "<?php echo $config['weixin']['appmessage']['appid']; ?>",
                        "img_url": "<?php echo $config['weixin']['appmessage']['img_url']; ?>",
                        "img_width": "<?php echo $config['weixin']['appmessage']['img_width']; ?>",
                        "img_height": "<?php echo $config['weixin']['appmessage']['img_height']; ?>",
                        "link": "<?php echo $config['weixin']['appmessage']['link']; ?>",
                        "desc": sprintf("<?php echo $config['weixin']['appmessage']['desc']; ?>", score),
                        "title": sprintf("<?php echo $config['weixin']['appmessage']['title']; ?>", score)
                    },
                    function(res) { (dataForWeixin.callback)();
                    });
                });

                // 分享朋友圈
                WeixinJSBridge.on('menu:share:timeline',
                function(argv) { (dataForWeixin.callback)();
                    var score = Number(sessionStorage.getItem("currentScore"));

                    WeixinJSBridge.invoke('shareTimeline', {
                        "img_url": "<?php echo $config['weixin']['timeline']['img_url']; ?>",
                        "img_width": "<?php echo $config['weixin']['timeline']['img_width']; ?>",
                        "img_height": "<?php echo $config['weixin']['timeline']['img_height']; ?>",
                        "link": "<?php echo $config['weixin']['timeline']['link']; ?>",
                        "desc": sprintf("<?php echo $config['weixin']['timeline']['desc']; ?>", score),
                        "title": sprintf("<?php echo $config['weixin']['timeline']['title']; ?>", score)
                    },
                    function(res) {});
                });

                // 分享微博
                WeixinJSBridge.on('menu:share:weibo',
                function(argv) {
                    var score = Number(sessionStorage.getItem("currentScore"));
                    WeixinJSBridge.invoke('shareWeibo', {
                        "content": sprintf("<?php echo $config['weixin']['timeline']['title']; ?>", score),
                        "url": "<?php echo $config['weixin']['timeline']['link']; ?>",
                    },
                    function(res) { (dataForWeixin.callback)();
                    });
                });

                // 分享Facebook
                WeixinJSBridge.on('menu:share:facebook',
                function(argv) { (dataForWeixin.callback)();
                    var score = Number(sessionStorage.getItem("currentScore"));
                    WeixinJSBridge.invoke('shareFB', {
                        "img_url": "<?php echo $config['weixin']['timeline']['img_url']; ?>",
                        "img_width": "<?php echo $config['weixin']['timeline']['img_width']; ?>",
                        "img_height": "<?php echo $config['weixin']['timeline']['img_height']; ?>",
                        "link": "<?php echo $config['weixin']['timeline']['link']; ?>",
                        "desc": sprintf("<?php echo $config['weixin']['timeline']['desc']; ?>", score),
                        "title": sprintf("<?php echo $config['weixin']['timeline']['title']; ?>", score),
                    },
                    function(res) {});
                });
            };

            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
            }

        });
    </script>
</body>
</html>
