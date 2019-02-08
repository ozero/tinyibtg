<?php
/*
https://gist.github.com/anonymous/6516521b1fb3b464534fbc30ea3573c2
https://core.telegram.org/widgets/login
*/


if (isset($_GET['logout']) && ($_GET['logout'])) {
  setcookie('tg_user', '');
  header('Location: login_tg.php');
}

if (!defined('BOT_TOKEN')) {
  require_once("../data/settings.php");
}


$tg_user = false;
if (isset($_COOKIE['tg_user'])) {
  //ログイン時に、ユーザ情報がjsにより$_COOKIE['tg_user']にセットされている。
  $auth_data_json = urldecode($_COOKIE['tg_user']);
  $auth_data = json_decode($auth_data_json, true);
  print "<!-- pre>"."_COOKIE: ".print_r($_COOKIE,true);
  print "auth_data: ".print_r($auth_data, true)."</pre -->";
  $tg_user =  $auth_data;
}

if ($tg_user !== false) {
  $first_name = htmlspecialchars($tg_user['first_name']);
  $last_name = htmlspecialchars($tg_user['last_name']);
  //
  if (isset($tg_user['username'])) {
    //
    $username = htmlspecialchars($tg_user['username']);
    $html = "<h3>あなたは現在 Telegramアカウント '<a href=\"https://t.me/{$username}\">{$first_name} {$last_name}</a>'としてログインしています。</h3>";
  } else {
    //
    $html = "<h3>あなたは現在 Telegramアカウント '{$first_name} {$last_name}' としてログインしています。</h3>";
  }

  //
  if (isset($tg_user['photo_url'])) {
    $photo_url = htmlspecialchars($tg_user['photo_url']);
    $html .= "<img width='100' src=\"{$photo_url}\">";
  }

  //
  $html .= "<p><a href=\"?logout=1\">[ログアウトする]</a></p>";

} else {
  //
  $bot_username = BOT_USERNAME;
  $html = <<<HTML
<h3>Telegramアカウントでログインしてください。</h3>
<script async src="https://telegram.org/js/telegram-widget.js?2" data-telegram-login="{$bot_username}" data-size="large" data-auth-url="check_authorization.php"></script>
<br>
<br>
<div>
  ログイン時に、Telegramアプリでのログイン確認が必要な場合があります。
</div>
HTML;
}

echo <<<HTML
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Telegramでログインしています</title>
  </head>
  <body><center>{$html}</center></body>
</html>
HTML;
