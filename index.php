<?php
    if (isset($_GET['status'])){
        $status = $_GET['status'];
//        var_dump($status);
    }
?>
<!DOCTYPE html>
<html>
 <head> 
  <meta charset="utf-8" /> 
  <meta name="viewport" content="width=device-width; initial-scale=1.0" /> 
  <title>在线选座订座（影院版）</title> 
  <meta name="keywords" content="jQuery,选座" /> 
  <link href="css/style.css" rel="stylesheet" type="text/css" /> 
 </head> 
 <body> 
  <div id="main"> 
   <div class="demo"> 
    <div id="seat-map"> 
     <div class="front">
      屏幕
     </div> 
    </div> 
    <div class="booking-details"> 
     <p>影片：<span>中国机长 9.3</span></p> 
     <p>时间：<span>10月01日 21:00</span></p> 
     <p>座位：</p> 
     <ul id="selected-seats"></ul> 
     <p>票数：<span id="counter">0</span></p> 
     <p>总计：<b>￥<span id="total">0</span></b></p> 
     <button id="buy" class="checkout-button">确定购买</button>
     <div id="legend"></div> 
    </div> 
    <div style="clear:both"></div> 
   </div> 
   <br /> 
  </div> 
  <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script> 
  <script type="text/javascript" src="js/jquery.seat-charts.min.js"></script> 
  <script type="text/javascript">
    var price = 80; //票价
    $(document).ready(function() {
        //页面加载完成后就开始执行
        var $cart = $('#selected-seats'), //座位区
        $counter = $('#counter'), //票数
        $total = $('#total'); //总计金额

        var sc = $('#seat-map').seatCharts({
            map: [  //座位图
                'aaaaaaaaaa',
                'aaaaaaaaaa',
                '__________',
                'aaaaaaaaaa',
                'aaaaaaaaaa',
                'aaaaaaaaaa',

            ],
            naming : {
                top : false,
                getLabel : function (character, row, column) {
                    return column;
                }
            },
            legend : { //定义图例
                node : $('#legend'),
                items : [
                    [ 'a', 'available',   '可选座' ],
                    [ 'a', 'unavailable', '已售出']
                ]
            },
            click: function () { //点击事件
                if (this.status() == 'available') { //可选座
                    $('<li>'+(this.settings.row+1)+'排'+this.settings.label+'座</li>')
                        .attr('id', 'cart-item-'+this.settings.id)
                        .data('seatId', this.settings.id)
                        .appendTo($cart);

                    $counter.text(sc.find('selected').length+1);
                    $total.text(recalculateTotal(sc)+price);

                    return 'selected';
                } else if (this.status() == 'selected') { //已选中
                        //更新数量
                        $counter.text(sc.find('selected').length-1);
                        //更新总计
                        $total.text(recalculateTotal(sc)-price);

                        //删除已预订座位
                        $('#cart-item-'+this.settings.id).remove();
                        //可选座
                        return 'available';
                } else if (this.status() == 'unavailable') { //已售出
                    return 'unavailable';
                } else {
                    return this.style();
                }
            }
        });
        //已售出的座位
        // sc.get(['5_6','5_5']).status('unavailable');
        //ws连接
        var wsServer = 'ws://47.102.101.13:9502'; //
        var websocket = new WebSocket(wsServer);  //创建ws对象
        websocket.onopen = function (evt) {       //创建连接对象
         websocket.send("<?php echo $status ;?>");
        };

        websocket.onclose = function (evt) {
         console.log("Disconnected");
        };

        websocket.onmessage = function (evt) {
         console.log('Retrieved data from server: ' + evt.data);
         console.log(evt.data);
         //在得到服务器的推送消息后,使用js改变座位状态:
            sc.get(JSON.parse(evt.data)).status('unavailable');
        };

        websocket.onerror = function (evt, e) {
         console.log('Error occured: ' + evt.data);
        };

    });
    //计算总金额
    function recalculateTotal(sc) {
        var total = 0;
        sc.find('selected').each(function () {
            total += price;
        });
        return total;
    }
    $('#buy').click(function () {
        ids = "";
        //遍历所有的li
        $('#selected-seats li').each(function () {
            // console.log(this.id)  //获取当前对象的id属性
            ids += this.id + "|"
        });
        location.href = 'success.php?ids='+ids;
    })
</script>  
 </body>
</html>