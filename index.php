<?php
require_once('init.php');
$colors = ['black', 'white'];
$color = $_GET['color'];
if ( in_array($color, $colors) ) {
    $versa['color'] = array_shift(array_diff($colors, [$color]));
    $versa['link'] = $host['http'].'/index.php?color='.$versa['color'];
} else {
    $color = 'black';
    header('location:'.$host['http'].'/index.php?color='.$color);
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
    <title>五子棋</title>
    <link href="./layout.css" rel="stylesheet">
    </head>
<body>
    <div id=body>
        <div id=info>
<p>
邀请朋友对战：
</p>
<p>
<input type=text value='<?php echo $versa['link']; ?>' />
</p>
        </div>
        <div id=battle></div>
        <div id=notice></div>
    </div>
</body>
<script>

var PK =  {};

const urlParams = new URLSearchParams(window.location.search);

document.addEventListener("DOMContentLoaded", () => {
      run();
});

var run = () => {
    initBattle();
    initPlayer();
    dotAction();
}


var initBattle = () => {
    rows = [];
    for ( let i=1;i<=15;i++ ) {
        row = [];
        row.push("<div class=row>");
        for ( let j=1;j<=15;j++ ) {
            id = (i-1) * 15 + j;
            row.push("<div boss=nil row="+i+" col="+j+" id=dot"+id+"></div>");
        }
        row.push("</div>");
        rows.push(row.join(''));
    }
    document.getElementById('battle').innerHTML = rows.join('');
}

var dotAction = () => {
    const battle = document.getElementById('battle');
    battle.querySelectorAll('.row div').forEach(function(dot){                                                                
        dot.addEventListener('click', function(){seize(dot)}, false);
    });
}

if (window["WebSocket"]) {
    let href = location.hostname+(location.port ? ':'+location.port: '');
    conn = new WebSocket("<?php echo $host['websocket'];?>");
    conn.onclose = function (evt) {
        showNotice("<b>连接关闭</b>");
    };
    conn.onmessage = function (evt) {
        const res = JSON.parse(evt.data);
        document.getElementById(res.pos).classList.add(res.color);
        document.getElementById(res.pos).setAttribute("boss", res.color);
        /*
        var messages = evt.data.split('\n');
        for (var i = 0; i < messages.length; i++) {
            showNotice(messages[i]);
        }
        */
    };
}

var seize = (dot) => {
    if ( dot.getAttribute('boss') == "nil" ) {
        let post = {};
        post['row'] = dot.getAttribute('row');
        post['col'] = dot.getAttribute('col');
        post['pos'] = dot.getAttribute('id');
        post['color'] = PK.color;
        conn.send(JSON.stringify(post));
    }

    if ( PK.order == PK.color ) {
    } else {

    }
}

function showNotice(message) {
    var el = document.createElement("div");
    el.innerHTML = message;
    document.getElementById("notice").appendChild(el);
}

async function initPlayer() {
    //let PKID = urlParams.get('PKID');
    PK.color = urlParams.get('color');
}
</script>
</html>
