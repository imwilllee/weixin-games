var game = {

    rePhone:/^1[3-8]\d{9}$/,

   /**
    * 获取指定范围内的随机数工具方法
    */
    getRandom:function(min,max){
        var range = max - min;
        var rand = Math.random();
        return (min + Math.round(rand * range));
    },

    /**
     * 获取警告信息提示框工具方法
     * @param type 调用提示框的类型(警告类信息提示，成功提示框)
     * @param str 提示的信息内容
     */
    showInfoWindow: function(str,type)
    {

          $(".info-window").html(str);
          if(type == "alert")
          {
               $(".info-window").css({
                    'backgroundColor' : 'rgba(255, 0 , 0 , .6)'
               });

          }else if(type == "info")
          {
               $(".info-window").css({
                    'backgroundColor' : 'rgba(56, 115 , 255 , .6)'
               });
          }

          $(".info-window").hide().show();

          setTimeout(function(){
               $(".info-window").fadeOut(1500);
          },1000); 

    },

    //获取音频文件
    setSounds:function(){
        for (var i=0; i<PIANO_SIMPLE.length; i++)
        {
            createjs.Sound.registerSound({ src: "assets/mp3/tap.mp3", id: i });
            //createjs.Sound.registerSound({ src: PIANO_SIMPLE[i], id: i });
        }
    },

    /**
     * 点击查看排名按钮显示用户提交昵称UI组件
     */
    showRankWidget:function()
    {
        $("#result-area").fadeOut(300,function(){
            $("#user-submit-area").fadeIn(300);
        });

        if(typeof localStorage.getItem("l_menma_cnt_piano_nickname") == "string")
        {
            var localNickname = localStorage.getItem("l_menma_cnt_piano_nickname");
            $(".submit-nickname").val(localNickname);
        }

        if(typeof localStorage.getItem("l_menma_cnt_piano_cellphone") == "string")
        {
            var localCellphone = localStorage.getItem("l_menma_cnt_piano_cellphone");
            $(".submit-phone").val(localCellphone);
        }
    },

    /**
     * 用户提交数据校验
     */
     validateUserSubmit:function(){
        var nickname = $(".submit-nickname").val();
        var cellPhone = $(".submit-phone").val();

        if(nickname == "" || cellPhone == ""){
            game.showInfoWindow("查看排名前请留下您的大名和联系方式哦!","alert");
            return false;
        }

        if(!game.rePhone.test(cellPhone)){
            game.showInfoWindow("您的联系方式格式不对哦!","alert");
            return false;
        }
        return true;
     },

    /**
     * 用户提交昵称查看成绩排名
     */
     showScoreList:function()
     {
        var currentScore     = $(".font-current-score").html();
        var currentNickName  = $(".submit-nickname").val();
        var cellPhone        = $(".submit-phone").val();
        var guid             = '';
        if(typeof localStorage.getItem("local_cache_guid") != "string") {
            guid = game.getGuid();
            localStorage.setItem("local_cache_guid",guid);
        } else {
            guid = localStorage.getItem("local_cache_guid");
        }

        //这里需要向后台发送请求保存当前用户的昵称和得分数据，并且获取一定量的用户数据进行排名显示
        if(game.validateUserSubmit()){
            $('.btn-submit').attr('disabled', true);
            //local存储第一次填的数据
            if(typeof localStorage.getItem("l_menma_cnt_piano_nickname") != "string")
            {
                localStorage.setItem("l_menma_cnt_piano_nickname",currentNickName);
            }

            if(typeof localStorage.getItem("l_menma_cnt_piano_cellphone") != "string")
            {
                localStorage.setItem("l_menma_cnt_piano_cellphone",cellPhone);
            }

            $.ajax({
                type:'POST',
                async:false,
                url:'/games/piano/api/rank_list.php',
                data:
                {
                    score    : currentScore,
                    nickname : currentNickName,
                    guid     : guid,
                    tel      : cellPhone
                },
                dataType:'json',
                success:function(data){
                    //获取到的是已经排序好的数据
                    //console.log(data);return;
                    var sListStr = "";

                    for(var i in data)
                    {
                        //添加rank-list-item-current类到当前用户项
                        if(data[i]['guid'] == guid)
                        {
                            sListStr += "<li class='rank-list-item rank-list-item-current'>";
                        }else{
                            sListStr += "<li class='rank-list-item'>";
                        }

                        sListStr += "<ul class='rank-list-item-wrap'>";
                        sListStr += "<li class='rank-list-wrap-item rank-list-item-num'>"+data[i]['order']+"</li>";
                        sListStr += "<li class='rank-list-wrap-item rank-list-item-user'>"+data[i]['nickname']+"</li>";
                        sListStr += "<li class='rank-list-wrap-item rank-list-item-score'>"+data[i]['score']+"</li>";
                        sListStr += "</ul>";
                        sListStr += "</li>";
                    }

                    $(".rank-list").html(sListStr);
                    $(".input-group, .btn-submit, .msg-rank").fadeOut(300,function(){
                        $(".rank-area").fadeIn(300);
                        $('.btn-submit').attr('disabled', false);
                    });
                }
            });
        }
        

     },

     getGuid:function() {
        function S4() {
           return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
        }
        return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
    },
    
    /**
     * 自定义的重新开始游戏方法
     */
    restart : function(){
            //隐藏整体模态层
            $("#mask-over").fadeOut(300);
            //隐藏用户提交和查看排名区块组件
            $("#user-submit-area").fadeOut(300);
            //子元素隐藏查看排名组件。显示用户提交组件

            $(".rank-area").hide();
            $(".input-group, .btn-submit, .msg-rank").show();

            $("#result-area").fadeIn(300);
            
            //子元素隐藏提示分享的图片，显示logo图
            $(".modal-game-share").hide();
            $(".modal-pet-top").show();

            //剔除得分排名里已获得的信息
            $(".rank-list-item").remove();

            gameRestart();

    },
    /**
     * 屏幕旋转事件函数
     */
    screenOrientation:function(){
        if(window.orientation != 0)
        {
            alert("建议在竖屏状态下玩游戏!");
            $("body").hide();
        }else{
            $("body").show();
            window.scrollTo(0,0);
        }


    },
    css : function(){
        //调整rank-area最高高度
        $(".rank-list").css({
            'maxHeight' : $(window).height() - 
                          ($(".btn-top-score-area").height() + 20 + $(".rank-title").height() + 
                            $(".modal-btm-btn-area").height() + $(window).height()*0.2)
        });
    },

    init : function () {

        game.css();
        sessionStorage.setItem("score",0);

        //开始游戏
        $("#modal-btn-start").on("touchend",function(){
            clearInterval(objTime);
            $("#mask-start").fadeOut(300);
        });

        //再来一次  click用于方便PC调试  手机上建议用touchend
        //再来一次 对自定义的蒙版元素进行初始化
        $(".btn-again").on("touchend",function(){game.restart();});

        //分享成绩
        $(".btn-share").on("touchend",function(){
            //当前页面为刚结束的页面
            if($("#result-area").css("display")=="block"){
                $("#result-area").fadeOut(300,function(){
                    $(".modal-game-share").show(1,function(){
                        $("#result-area").fadeIn(300);
                    });
                });
            //用户提交和查看得分排名页面
            }else if($("#user-submit-area").css("display")=="block"){
                $("#user-submit-area").fadeOut(300,function(){
                    $(".modal-game-share").show(1,function(){
                        $("#result-area").fadeIn(300);
                    });
                });

            }
        });

        //添加点击查看排名按钮函数
        $(".btn-rank").on("touchend",function(){game.showRankWidget();});

        //添加用户提交昵称按钮点击事件
        $(".btn-submit").on("touchend",function(){game.showScoreList();});
        
        //解决手机上弹出和收回虚拟键盘时的UI问题
        $("input").on("blur",function(){
            window.scrollTo(0,0);
        });
        
        //安卓手机浏览器的窗口调整事件
        var ua = window.navigator.userAgent;

        //安卓手机以及iOS7.0.4系统的手机
        if(/iPhone/.test(ua) && /7_0_4/.test(ua)){
            $("input").on("focus",function(){
                $(".modal-btm-btn-area,.modal-btm-copyright").hide();
            });
            $("input").on("blur",function(){
                $(".modal-btm-btn-area,.modal-btm-copyright").show();
            });
        }
        if(/Android/.test(ua)){
            $(window).on("resize",function(){
                if($(".modal-btm-btn-area").css("display")=="block"){
                    $(".modal-btm-btn-area").hide();
                }else{
                    $(".modal-btm-btn-area").show();
                }
                if($(".modal-btm-copyright").css("display")=="block"){
                    $(".modal-btm-copyright").hide();
                }else{
                    $(".modal-btm-copyright").show();
                }
            });
        }
    },

    gameOver : function (s, h) {
        $(".font-current-score").html(s);
        $(".font-highest-score").html(h);
        $("#mask-over").fadeIn(300);
    } 
}
window.addEventListener("load",game.init,false);
//不允许出现蒙版时浏览器滚动条滚动
document.getElementById('mask-start').addEventListener('touchmove', function(event){
    event.preventDefault();
},false); 

document.getElementById('mask-over').addEventListener('touchmove', function(event){
    event.preventDefault();
},false); 