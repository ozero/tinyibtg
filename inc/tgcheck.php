<?php

//認証・認可済みならCookie上のユーザ情報をphp側に積んで続行。
function auth_tgcheck(){
  if (!isset($_COOKIE['tg_user'])) {
    //未認可ならログイン画面へ飛ばす。
    header('Location: login_tg.php');
    
  }else{
    $auth_data_json = urldecode($_COOKIE['tg_user']);
    $auth_data = json_decode($auth_data_json, true);
    define("AUTH_DATA", $auth_data);
  }
}

auth_tgcheck();
