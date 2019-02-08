<?php
/*
login_tg.php上のJS(TGログインウィジェット)が、
ログイン成功時にこっちにPOSTでリクエストを投げる。
*/

if (!defined('BOT_TOKEN')) {
  require_once("../data/settings.php");
}


try {
  $auth_data = checkTelegramAuthorization($_GET);
  saveTelegramUserData($auth_data);
  checkTelegramGroupJoined($auth_data['id']);
} catch (Exception $e) {
  die ($e->getMessage());
}

header('Location: index.php');

// ---------------------------------------------------------

//TGのログインJSウィジェットがこっちに投げてきた識別結果を検証して認証する
function checkTelegramAuthorization($auth_data) {
  if(!isset($auth_data['hash'])){
    throw new Exception('Data is NOT from Telegram (1)');
  }
  $check_hash = $auth_data['hash'];
  unset($auth_data['hash']);
  $data_check_arr = [];
  foreach ($auth_data as $key => $value) {
    $data_check_arr[] = $key . '=' . $value;
  }
  sort($data_check_arr);
  $data_check_string = implode("\n", $data_check_arr);
  $secret_key = hash('sha256', BOT_TOKEN, true);
  $hash = hash_hmac('sha256', $data_check_string, $secret_key);
  if (strcmp($hash, $check_hash) !== 0) {
    throw new Exception('Data is NOT from Telegram (2)');
  }
  if ((time() - $auth_data['auth_date']) > 86400) {
    throw new Exception('Data is outdated');
  }
  return $auth_data;
}

//ついでに、ログインユーザが指定グループに所属してるかを確認してアクセス権を認可する
function checkTelegramGroupJoined($user_id){
  $api_endpoint = "https://api.telegram.org/bot";
  $_is_joined = false;
  $chat_ids = json_decode(BOT_CHATROOM_ID, true);
  foreach($chat_ids as $cid){
    $api_request_url = "{$api_endpoint}".BOT_TOKEN."/getChatMember?chat_id={$cid}&user_id={$user_id}";
    $result = getApiDataCurl($api_request_url);
    if(isset($result['ok'])){
      $_is_joined = ($result['ok'] === true)?true:$_is_joined;
    }
  }
  if($_is_joined !== true){
    throw new Exception('あなたは許可されたTGグループに所属していません。');
  }
  return;
}

//認証済みの識別結果をCookieに保存する
function saveTelegramUserData($auth_data) {
  $auth_data_json = json_encode($auth_data);
  setcookie('tg_user', $auth_data_json);
}

//APIアクセス(Curl)
function getApiDataCurl($url){
  // https://qiita.com/shinkuFencer/items/d7546c8cbf3bbe86dab8
  
  $option = [
    CURLOPT_RETURNTRANSFER => true, //文字列として返す
    CURLOPT_TIMEOUT        => 3, // タイムアウト時間
  ];
  
  $ch = curl_init($url);
  curl_setopt_array($ch, $option);
  
  $json    = curl_exec($ch);
  $info    = curl_getinfo($ch);
  $errorNo = curl_errno($ch);
  
  // OK以外はエラーなので空白配列を返す
  if ($errorNo !== CURLE_OK) {
    // 詳しくエラーハンドリングしたい場合はerrorNoで確認
    // タイムアウトの場合はCURLE_OPERATION_TIMEDOUT
    return [];
  }
  
  // 200以外のステータスコードは失敗とみなし空配列を返す
  if ($info['http_code'] !== 200) {
    return [];
  }
  
  // 文字列から変換
  $jsonArray = json_decode($json, true);
  
  return $jsonArray;
}

