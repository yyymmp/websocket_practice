<?php
$ids = $_GET['ids'];
$ids= explode('|',$ids);
$arr = array();
foreach ($ids as $id){
    if ($id != ''){
        $des = substr($id,10);
        $arr[] = $des;
    }
}
//起一个协程redis 但是协程应用于cli模式下
http_post($arr,'http://127.0.0.1:9999');
function http_post($data,$url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<a href="index.php?status=success">支付成功,返回选座</a>
</body>
</html>
