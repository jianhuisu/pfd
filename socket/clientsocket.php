
<html>
<head>
    <title>chatdemo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
</head>

<body>
    <div id="xo">asdf</div>
<script src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>
<script>

    $(function(){

        // serverHost IP + port 即可建立通讯
        var wsurl = 'ws://192.168.32.10:19910';
        var websocket;
        var i = 0;
        var username = '<?= isset($_GET['username']) ? $_GET['username'] : 'user'.date('His',time());?>';
        var msg = {username:username,message:''};

        if(window.WebSocket){
            websocket = new WebSocket(wsurl);

            //连接建立
            websocket.onopen = function(event){
                console.log("Connected to WebSocket server.");
            };

            //收到消息
            websocket.onmessage = function(event) {

                //解析收到的json消息数据
                var msg = JSON.parse(event.data);
                console.log(msg);
            };

            //发生错误
            websocket.onerror = function(event){
                console.log("Connected to WebSocket server error");
            };

            //连接关闭
            websocket.onclose = function(event){
                console.log('websocket Connection Closed. ');
            };

            function send(){

                try{
                    msg.message = ++i;
                    websocket.send(JSON.stringify(msg));
                } catch(ex) {
                    console.warn(ex);
                }

            }

            $(window).keydown(function(event){
                if(event.keyCode == 13){
                    console.log('user enter');
                    send();
                }
            })

        } else {
            alert('该浏览器不支持web socket');
        }

    });
</script>
</body>
</html>