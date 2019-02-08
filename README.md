
# TinyIBっつーふたばクローンをTG認証に対応させてみた話。

ええとねー。「＊＊＊のTGグループに入ってるひとだけが見られる掲示板」を作ったよ。

- ＠botfatherと `/newbot` ってお話して、Telegram Botを準備してね
  - botの名前を指定
  - botのユーザ名を指定　→　ここでHTTP APIを喋る用のトークンもらえるよ
  - `/setdomain` からbotのアクセス元webサイトのドメイン名を指定

- `settings.default.php` を `../data/setting.php`にコピーしてね

- `inc/html.php` の `____ABSOLUTE_PATH_FOR_TGCHECK_PHP____` を適切な内容に直してね

- どうにかして許可元ID台帳となるTGグループのchat_idを取得してね。
  - こうやる：["Telegram event handler | InfluxData Documentation"](https://docs.influxdata.com/kapacitor/v1.5/event_handlers/telegram/#get-your-telegram-chat-id)

- 以下をうめてね
  - `BOT_USERNAME`
  - `BOT_TOKEN`
  - `BOT_CHATROOM_ID`

- `./imgboard.php` にアクセスしてね
 
これで事足りるんじゃないかなあ。
