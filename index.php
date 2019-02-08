<?php require_once("/home/gyouza/www/ib.currentdir.com/docs/inc/tgcheck.php"); ?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8">
		<meta http-equiv="cache-control" content="max-age=0">
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="expires" content="0">
		<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
		<meta http-equiv="pragma" content="no-cache">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<title>Note for Telegram		</title>
		<link rel="shortcut icon" href="favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/global.css">
		<link rel="stylesheet" type="text/css" href="css/futaba.css" title="Futaba">
		<link rel="alternate stylesheet" type="text/css" href="css/burichan.css" title="Burichan">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed" rel="stylesheet">
		<script src="js/jquery.js"></script>
		<script src="js/script.js"></script>
		<script src="js/tinyib.js"></script>
		
	</head>	<body>
		<div class="adminbar">
			[<a href="imgboard.php?manage" style="text-decoration: underline;">Manage</a>]
		</div>
		<div class="logo">Note for Telegram		</div>
		<hr width="90%">
		
				<div class="postarea">
			<div align="center"><button id="toggle_postform" class="biggreenbtn">新しい投稿を書く</button></div>
			<form name="postform" id="postform" action="imgboard.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="MAX_FILE_SIZE" value="2097152">
			<input type="hidden" name="parent" value="0">
			<table class="postform">
				<tbody>
										<tr class="postform_hidden">
						<td>
							<div class="formlabel">あなたのTG名</div>
							ozero @Osaka JP (138586654)
							<input type="hidden" name="name" size="28"
							value="ozero @Osaka JP (138586654)"
							accesskey="n">
							
						</td>
					</tr>					<tr class="postform_hidden">
						<td>
							<div class="formlabel">件名</div>
							<input type="text" name="subject" id="form_name" maxlength="75" accesskey="s" autocomplete="off">
						</td>
					</tr>					<tr class="postform_hidden">
						<td>
							<div class="formlabel">本文（必須）</div>
							<textarea id="message" name="message" accesskey="m"></textarea>
						</td>
					</tr>					
										<tr class="postform_hidden">
						<td>
							<div class="formlabel">画像ファイル(2MB)</div>
							<input type="file" name="file" size="35" accesskey="f">
						</td>
					</tr>
										<tr class="postform_hidden">
						<td>
							<div class="formlabel">投稿のパスワード</div>
							<input type="password" name="password" id="newpostpassword" size="8" accesskey="p">
							&nbsp;&nbsp;(投稿・ファイルの削除用)
							<hr>
							<div align="center">
							<input type="submit" value="投稿する" accesskey="z" class="submitpost">
							</div>
							<br><br><br>
						</td>
					</tr>					<tr class="postform_hidden">
						<td class="rules">
							
							<ul>
								
								<li>Supported file types are JPG, PNG and GIF.</li>
								<li>Maximum file size allowed is 2 MB.</li>
								<li>Images greater than 250x250 will be thumbnailed.</li>
								
							</ul>
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		</div>
		<hr>
		<form id="delform" action="imgboard.php?delete" method="post">
		<input type="hidden" name="board"value="b">		
		<table class="userdelete">
			<tbody>
				<tr>
					<td>
						投稿の削除：削除用パスワード <input type="password" name="password" id="deletepostpassword" size="8" placeholder="Password">&nbsp;<input name="deletepost" value="投稿の削除" type="submit">
					</td>
				</tr>
			</tbody>
		</table>
		<br clear="both">
		</form>
		<table border="1" id="pagelinks">
	<tbody>
		<tr>
			<td>過去の投稿</td><td>&#91;0&#93; </td><td>新しい投稿</td>
		</tr>
	</tbody>
</table>
		<br>		<div class="footer">
			<a href="/login_tg.php">[ログアウト]</a> /  
			<!-- - <a href="http://www.2chan.net" target="_top">futaba</a> + <a href="http://www.1chan.net" target="_top">futallaby</a> + <a href="https://gitlab.com/tslocum/tinyib" target="_top">tinyib</a> - -->
			Forked from <a href="https://gitlab.com/tslocum/tinyib" target="_top">tinyib</a>
		</div>
	</body>
</html>