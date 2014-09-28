var body, blockWidth, blockHeight, GameLayer = [], GameLayerBG, touchArea = [], GameTimeLayer;
var transform, transitionDuration;
var soundIndex;

function init(argument) {
    body = document.getElementById('container');
    //创建游戏层
    body.innerHTML = createGameLayer();

    body.style.height = (window.innerHeight - document.getElementById('title').offsetHeight) + 'px';
    transform = typeof (body.style.webkitTransform) != 'undefined' ? 'webkitTransform' : (typeof (body.style.msTransform) != 'undefined' ? 'msTransform' : 'transform');
    transitionDuration = transform.replace(/ransform/g, 'ransitionDuration');

    GameTimeLayer = document.getElementById('GameTimeLayer');
    GameLayer.push(document.getElementById('GameLayer1'));
    GameLayer[0].children = GameLayer[0].querySelectorAll('div');
    GameLayer.push(document.getElementById('GameLayer2'));
    GameLayer[1].children = GameLayer[1].querySelectorAll('div');
    GameLayerBG = document.getElementById('GameLayerBG');

    GameLayerBG.ontouchstart = gameTapEvent;
    gameInit();
}

function createGameLayer() {
    var html = '<div id="GameLayerBG">';
    for (var i = 1; i <= 2; i++) {
        var id = 'GameLayer' + i;
        html += '<div id="' + id + '" class="GameLayer">';
        for (var j = 0; j < 10; j++) {
            for (var k = 0; k < 4; k++) {
                html += '<div id="' + id + '-' + (k + j * 4) + '" num="' + (k + j * 4) + '" class="block' + (k ? ' bl' : '') + '"></div>';
            }
        }
        html += '</div>';
    }
    html += '</div>';

    //html += '<div id="GameTimeLayer"></div>';

    return html;
}

var _gameBBList = [], _gameBBListIndex = 0, _gameOver = false, _gameStart = false, _gameTime, _gameTimeNum, _gameScore;
var _gameTimestamp;
function gameInit() {

    //createjs.Sound.alternateExtensions = ["m4a"];
    createjs.Sound.registerManifest(SoundSrc, "assets/sound/");
    //createjs.Sound.registerSound({ src: "assets/mp3/tap.mp3", id: "tap" });
    gameRestart();

    //加载完毕之后才显示开始按钮
    document.getElementById("modal-btn-start").style.display = "block";
}

function gameRestart() {
    console.log('gameRestart');
    soundIndex = 0;
    _gameBBList = [];
    _gameBBListIndex = 0;
    _gameScore = 0;
    _gameOver = false;
    _gameStart = false;
    _gameTimeNum = 3000;
    GameTimeLayer.innerHTML = creatTimeText(_gameTimeNum);
    countBlockSize();
    refreshGameLayer(GameLayer[0]);
    refreshGameLayer(GameLayer[1], 1);
}

function gameStart() {
    _gameStart = true;
    _gameTimestamp = new Date().getTime();
    _gameTime = setInterval(gameTime, 10);
}

function gameOver() {
    _gameOver = true;
    clearInterval(_gameTime);
    setTimeout(function () {
        GameLayerBG.className = '';
        showGameScoreLayer();
    }, 500);
}

function gameTime() {
    //_gameTimeNum--;
    //确保计时器准确
    var temp = _gameTimeNum - Math.floor((new Date().getTime() - _gameTimestamp)/10);
    if (temp <= 0) {
        GameTimeLayer.innerHTML = '&nbsp;&nbsp;时间到！';
        gameOver();
        //GameLayerBG.className += ' flash';
        createjs.Sound.play("end");
    } else {
        GameTimeLayer.innerHTML = creatTimeText(temp);
    }
}

function creatTimeText(n) {
    var text = (100000 + n + '').substr(-4, 4);
    text = '&nbsp;&nbsp;' + text.substr(0, 2) + "'" + text.substr(2) + "''"
    return text;
}

function countBlockSize() {
    //细调整宽高确保刚好全屏显示
    blockWidth = Math.floor(body.offsetWidth / 4);
    blockHeight = Math.floor(body.offsetHeight / 5);
    document.getElementById('title').style.height = (body.offsetHeight - 5 * blockHeight + document.getElementById('title').offsetHeight) + 'px';
    body.style.height = (window.innerHeight - document.getElementById('title').offsetHeight) + 'px';
    body.style.top = document.getElementById('title').offsetHeight + 'px';

    GameLayerBG.style.height = body.offsetHeight + 'px';
    touchArea[0] = window.innerHeight - blockHeight * 0;
    touchArea[1] = window.innerHeight - blockHeight * 3;
}

var _ttreg = / t{1,2}(\d+)/, _clearttClsReg = / t{1,2}\d+| bad/;
function refreshGameLayer(box, loop, offset) {
    //允许从第一行开始玩
    var i = Math.floor(Math.random() * 1000) % 4 + (loop ? 0 : 4);
    for (var j = 0; j < box.children.length; j++) {
        var r = box.children[j],
        rstyle = r.style;
        rstyle.left = (j % 4) * blockWidth + 'px';
        rstyle.bottom = Math.floor(j / 4) * blockHeight + 'px';
        rstyle.width = blockWidth + 'px';
        rstyle.height = blockHeight + 'px';
        r.className = r.className.replace(_clearttClsReg, '');
        if (i == j) {
            _gameBBList.push({ cell: i % 4, id: r.id });
            r.className += ' t' + (Math.floor(Math.random() * 1000) % 5 + 1);
            r.notEmpty = true;
            i = (Math.floor(j / 4) + 1) * 4 + Math.floor(Math.random() * 1000) % 4;
        } else {
            r.notEmpty = false;
        }
    }
    if (loop) {
        box.style.webkitTransitionDuration = '0ms';
        box.style.display = 'none';
        box.y = -blockHeight * (Math.floor(box.children.length / 4) + (offset || 0)) * loop;
        setTimeout(function () {
            box.style[transform] = 'translate3D(0,' + box.y + 'px,0)';
            setTimeout(function () {
            box.style.display = 'block';
        }, 100);
    }, 200);
    } else {
        box.y = 0;
        box.style[transform] = 'translate3D(0,' + box.y + 'px,0)';
    }
    box.style[transitionDuration] = '150ms';
}

function gameTapEvent(e) {
    if (_gameOver) {
        return false;
    }
    var tar = e.target;

    var y = e.clientY || e.targetTouches[0].clientY,
        x = (e.clientX || e.targetTouches[0].clientX) - body.offsetLeft,
        p = _gameBBList[_gameBBListIndex];

    if (y > touchArea[0] || y < touchArea[1]) {
        return false;
    }

    if ((p.id == tar.id && tar.notEmpty) 
        || (p.cell == 0 && x < blockWidth) 
        || (p.cell == 1 && x > blockWidth && x < 2 * blockWidth) 
        || (p.cell == 2 && x > 2 * blockWidth && x < 3 * blockWidth) 
        || (p.cell == 3 && x > 3 * blockWidth)) {
        if (!_gameStart) {
            gameStart();
        }

        if (soundIndex >= CITY_OF_SKY.length) { soundIndex = 0;};
        //console.log(CITY_OF_SKY[soundIndex]);
        createjs.Sound.play(CITY_OF_SKY[soundIndex]);
        //createjs.Sound.play("tap");
        soundIndex++;

        tar = document.getElementById(p.id);
        tar.className = tar.className.replace(_ttreg, ' tt$1');
        _gameBBListIndex++;
        _gameScore++;
        sessionStorage.setItem("currentScore",_gameScore);
        gameLayerMoveNextRow();
    } else if (_gameStart && !tar.notEmpty) {
        createjs.Sound.play("err");
        gameOver();
        tar.className += ' bad';
    }
    return false;
}

function gameLayerMoveNextRow() {
    for (var i = 0; i < GameLayer.length; i++) {
        var g = GameLayer[i];
        g.y += blockHeight;
        if (g.y > blockHeight * (Math.floor(g.children.length / 4))) {
            refreshGameLayer(g, 1, -1);
        } else {
            g.style[transform] = 'translate3D(0,' + g.y + 'px,0)';
        }
    }
}

function showGameScoreLayer() {
    var bast = cookie('bast-score');
    if (!bast || _gameScore > bast) {
        bast = _gameScore;
        cookie('bast-score', bast, 100);
    }
    game.gameOver(_gameScore, bast);
}

function toStr(obj) {
    if (typeof obj == 'object') {
        return JSON.stringify(obj);
    } else {
        return obj;
    }
    return '';
}

function cookie(name, value, time) {
    if (name) {
        if (value) {
            if (time) {
                var date = new Date();
                date.setTime(date.getTime() + 864e5 * time), time = date.toGMTString();
            }
            return document.cookie = name + "=" + escape(toStr(value)) + (time ? "; expires=" + time + (arguments[3] ? "; domain=" + arguments[3] + (arguments[4] ? "; path=" + arguments[4] + (arguments[5] ? "; secure" : "") : "") : "") : ""), !0;
        }
        return value = document.cookie.match("(?:^|;)\\s*" + name.replace(/([-.*+?^${}()|[\]\/\\])/g, "\\$1") + "=([^;]*)"), value = value && "string" == typeof value[1] ? unescape(value[1]) : !1, (/^(\{|\[).+\}|\]$/.test(value) || /^[0-9]+$/g.test(value)) && eval("value=" + value), value;
    }
    var data = {};
    value = document.cookie.replace(/\s/g, "").split(";");
    for (var i = 0; value.length > i; i++) name = value[i].split("="), name[1] && (data[name[0]] = unescape(name[1]));
    return data;
}
