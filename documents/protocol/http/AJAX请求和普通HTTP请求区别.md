# ajax http request & http request

不论 XMLhttpRequest 还是 httpequest 都是浏览器提供的,用来与服务器通讯的对象.
两者的底层都是基于tcp/ip协议,利用socket api实现的.本质上并没有明显的区别.
两者的不同更多的聚集在浏览器对两个通讯对象 通讯完毕之后,`web page`如何响应上面.

 - AJAX通`XMLHttpRequest`对象请求服务器服务器接受请求返数据实现刷新交互
 - 普通http请求通`httpRequest`对象请求服务器接受请求返数据需要页面刷新

AJAX请求头会多一个`x-requested-with`参数，值为`XMLHttpRequest`

XMLHttpRequest是Ajax技术的一个核心，没有它Ajax无从运作。
XMLHttpRequest：XMLHttpRequest是XMLHttp组件的一个对象，使用XMLHttpRequest可以实现浏览器端与服务器端进行异步通信。
通过HttpRequest对象，Web应用程序无需刷新页面就可以向服务器提交信息，然后得到服务器端的返回信息

再来谈谈Ajax与websocket、http.其实这三者各有优缺点，websocket、ajax的出现解决的http协议的一些问题，但http依然在很多地方是好的有优势的，

 - ajax是单向 客户端到服务端
 - http也是单向由客户端发起的
 - websocket实现了双向

但他们各自有自己适合的使用场景.

## demo

    ajax.php
    <?php
    
        if(isset($_GET['name']) && $_GET['name'] === 'ajax'){
            echo "xmlHttpRequest request";
            var_dump($_SERVER);
            exit;
        } else {
            var_dump($_SERVER);
        }
    ?>
    
    <script>
    
        var oAjax = null;
        if(window.XMLHttpRequest){
            oAjax = new XMLHttpRequest();
        }else{
            oAjax = new ActiveXObject('Microsoft.XMLHTTP');
        }
    
        var url = location.protocol+ "//" + document.domain+"/ajax.php?name=ajax";
        oAjax.open('GET', url, true);
        oAjax.send();
    
        oAjax.onreadystatechange=function(){
            if(oAjax.readyState == 4 ){
                if(oAjax.status==200){
                    alert("success");
                    console.log(oAjax.responseText);
                }else{
                    alert("not found");
                    console.log(404);
                }
            }
        };
    
    </script>
    
    </html>
