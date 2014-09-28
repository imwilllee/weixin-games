var _host = "http://" + window.location.host;
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady(){
    WeixinJSBridge.call('hideToolbar');
});

var dataForWeixin = {
    appId: "",
    MsgImg: "http://115.28.169.20/games/piano/assets/image/favicon.jpg", //这里直接调用线上的地址
    TLImg: "http://115.28.169.20/games/piano/assets/image/favicon.jpg",  //这里直接调用线上的地址
    url:  "http://115.28.169.20/games/piano/",
    fakeid: "",
    callback: function() {
        setTimeout(function(){
            attention_wx();
        },
        2000);
    }
}; 
(function() {
    var onBridgeReady = function() {
        WeixinJSBridge.on('menu:share:appmessage',
        function(argv) {

            var share_score = Number(sessionStorage.getItem("currentScore"));

            WeixinJSBridge.invoke('sendAppMessage', {
                "appid": dataForWeixin.appId,
                "img_url": dataForWeixin.MsgImg,
                "img_width": "120",
                "img_height": "120",
                "link": dataForWeixin.url,
                "desc": share_score + "分！我简直超神了！你敢来挑战我吗？===测试信息===！",
                "title": share_score + "分！我简直超神了！你敢来挑战我吗？===测试信息===！"
            },
            function(res) { (dataForWeixin.callback)();
            });
        });
        WeixinJSBridge.on('menu:share:timeline',
        function(argv) { (dataForWeixin.callback)();
            if(typeof share_score==undefined) var share_score;
            share_score = Number(sessionStorage.getItem("currentScore"));

            WeixinJSBridge.invoke('shareTimeline', {
                "img_url": dataForWeixin.TLImg,
                "img_width": "120",
                "img_height": "120",
                "link": dataForWeixin.url,
                "desc": share_score + "分！我简直超神了！你敢来挑战我吗？===测试信息===！",
                "title": share_score + "分！我简直超神了！你敢来挑战我吗？===测试信息===！"
            },
            function(res) {});
        });
        WeixinJSBridge.on('menu:share:weibo',
        function(argv) {
            WeixinJSBridge.invoke('shareWeibo', {
                "content": dataForWeixin.title,
                "url": dataForWeixin.url
            },
            function(res) { (dataForWeixin.callback)();
            });
        });
        WeixinJSBridge.on('menu:share:facebook',
        function(argv) { (dataForWeixin.callback)();
            WeixinJSBridge.invoke('shareFB', {
                "img_url": dataForWeixin.TLImg,
                "img_width": "120",
                "img_height": "120",
                "link": dataForWeixin.url,
                "desc": dataForWeixin.desc,
                "title": dataForWeixin.title
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
})();