<?php
//创建websocket服务器对象，监听0.0.0.0:9502端口
use Swoole\Coroutine\Redis;

$ws = new swoole_websocket_server("0.0.0.0", 9502);
//同时创建一个http服务
$http = $ws->listen('0.0.0.0','9999',SWOOLE_SOCK_TCP );
//http 回调
$http->on('request', function ($request, $response) {
    $data = $request -> post;  //获取发送过来的数据
    //使用协程
    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    foreach ($data as $item){
        $redis -> lPush('zgjz',$item);
    }
});
//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    $data = $redis->lRange('zgjz',0,-1);
    $serilize_data = json_encode($data);
    $ws->push($request->fd, $serilize_data);
//    var_dump($request->fd, $request->get, $request->server);
//    $ws->push($request->fd, "hello, welcome\n");  //发送数据到客户端$request->fd
});

//监听WebSocket消息事件
/**
 * $ws  存着所有的webSocket连接
 */
$ws->on('message', function ($ws, $frame) {
    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    $data = $redis->lRange('zgjz',0,-1);
    $serilize_data = json_encode($data);
    if ($frame->data == 'success') {
        //如果是从支付成功过来的
        foreach ($ws->connections as $fd) {  //给所有webSocket连接发送消息
            $ws->push($fd, $serilize_data);
        }
    }
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();