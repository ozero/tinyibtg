
# TinyIBっつーふたばクローンをTG認証に対応させてみた話。

「＊＊＊のTGグループに入ってるひとだけが見られる掲示板」を作った

- スクリプトは専用のドメイン下においたがいいのかな。適当にサブドメ切っとくれ。TGbotにドメインが要るっぽい。

- ＠botfatherと `/newbot` ってお話して、Telegram Botを準備して
  - botの名前を指定
  - botのユーザ名を指定　→　ここでHTTP APIを喋る用のトークンもらえる
  - `/setdomain` からbotのアクセス元webサイトのドメイン名を指定

- `settings.default.php` を `../data/setting.php`にコピーして

- 許可元ID台帳として使いたいTGグループのchat_idを取得
  - こうやる：["Telegram event handler | InfluxData Documentation"](https://docs.influxdata.com/kapacitor/v1.5/event_handlers/telegram/#get-your-telegram-chat-id)

- 以下をうめて
  - `BOT_USERNAME`
  - `BOT_TOKEN`
  - `BOT_CHATROOM_ID`

- `./imgboard.php` にアクセス
 
これで事足りるんじゃないかな
