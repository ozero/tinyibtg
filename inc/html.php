<?php
if (!defined('TINYIB_BOARD')) {
	die('');
}

function pageHeader($is_subdir) {
	$return = [];

	$return[] = ($is_subdir)?
	  "<?php require_once('../inc/tgcheck.php');?>":
	  "<?php require_once('./inc/tgcheck.php');?>";
	$return[] = '<?php $_authdata_tpl = AUTH_DATA; ?>';
	$return[] = '<?php $_tpl_name = "{$_authdata_tpl[\'first_name\']} {$_authdata_tpl[\'last_name\']} ({$_authdata_tpl[\'id\']})"; ?>';
	$return[] = '<!DOCTYPE html><html><head>';
	$return[] = '<meta http-equiv="content-type" content="text/html;charset=UTF-8">';
	$return[] = '<meta http-equiv="cache-control" content="max-age=0">';
	$return[] = '<meta http-equiv="cache-control" content="no-cache">';
	$return[] = '<meta http-equiv="expires" content="0">';
	$return[] = '<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT">';
	$return[] = '<meta http-equiv="pragma" content="no-cache">';
	$return[] = '<meta name="viewport" content="width=device-width,initial-scale=1">';
	$return[] = '<title>'. TINYIB_BOARDDESC . '</title>';
	$return[] = '<link rel="shortcut icon" href="favicon.ico">';
	$return[] = '<link rel="stylesheet" type="text/css" href="css/global.css">';
	$return[] = '<link rel="stylesheet" type="text/css" href="css/futaba.css" title="Futaba">';
	$return[] = '<link rel="alternate stylesheet" type="text/css" href="css/burichan.css" title="Burichan">';
	$return[] = '<link href="//fonts.googleapis.com/css?family=Open+Sans+Condensed:300" rel="stylesheet">';
	$return[] = '<script src="js/jquery.js"></script>';
	$return[] = '<script src="js/script.js"></script>';
	$return[] = '<script src="js/tinyib.js"></script>';
	$return[] = ( TINYIB_CAPTCHA === 'recaptcha' )?
	  '<script src="https://www.google.com/recaptcha/api.js" async defer></script>' :
	  '';
	$return[] = '</head>';

	return join("\n", $return);
}

function pageFooter() {
	// If the footer link is removed from the page, please link to TinyIB somewhere on the site.
	// This is all I ask in return for the free software you are using.

	return <<<EOF
		<div class="footer">
			<a href="/login_tg.php">[ログアウト]</a> /
			<!-- - <a href="http://www.2chan.net" target="_top">futaba</a> + <a href="http://www.1chan.net" target="_top">futallaby</a> + <a href="https://gitlab.com/tslocum/tinyib" target="_top">tinyib</a> - -->
			Forked from <a href="https://gitlab.com/tslocum/tinyib" target="_top">tinyib</a>
		</div>
	</body>
</html>
EOF;
}

function supportedFileTypes() {
	global $tinyib_uploads;
	if (empty($tinyib_uploads)) {
		return "";
	}

	$types_allowed = array_map('strtoupper', array_unique(array_column($tinyib_uploads, 0)));
	$types_last = array_pop($types_allowed);
	$types_formatted = $types_allowed
		? implode(', ', $types_allowed) . ' and ' . $types_last
		: $types_last;

	return "Supported file type" . (count($tinyib_uploads) != 1 ? "s are " : " is ") . $types_formatted . ".";
}

function makeLinksClickable($text) {
	$text = preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%\!_+.,~#?&;//=]+)!i', '<a href="$1" target="_blank">$1</a>', $text);
	$text = preg_replace('/\(\<a href\=\"(.*)\)"\ target\=\"\_blank\">(.*)\)\<\/a>/i', '(<a href="$1" target="_blank">$2</a>)', $text);
	$text = preg_replace('/\<a href\=\"(.*)\."\ target\=\"\_blank\">(.*)\.\<\/a>/i', '<a href="$1" target="_blank">$2</a>.', $text);
	$text = preg_replace('/\<a href\=\"(.*)\,"\ target\=\"\_blank\">(.*)\,\<\/a>/i', '<a href="$1" target="_blank">$2</a>,', $text);

	return $text;
}

function buildPostForm($parent, $raw_post = false) {
	global $tinyib_hidefieldsop, $tinyib_hidefields, $tinyib_uploads, $tinyib_embeds;
	$hide_fields = $parent == TINYIB_NEWTHREAD ? $tinyib_hidefieldsop : $tinyib_hidefields;

	$postform_extra = array('name' => '', 'email' => '', 'subject' => '', 'footer' => '');
	$input_submit = '<input type="submit" value="投稿する" accesskey="z" class="submitpost">';
	if ($raw_post || !in_array('subject', $hide_fields)) {
		$postform_extra['subject'] = $input_submit;
	} else if (!in_array('email', $hide_fields)) {
		$postform_extra['email'] = $input_submit;
	} else if (!in_array('name', $hide_fields)) {
		$postform_extra['name'] = $input_submit;
	} else if (!in_array('email', $hide_fields)) {
		$postform_extra['email'] = $input_submit;
	} else {
		$postform_extra['footer'] = $input_submit;
	}

	$form_action = 'imgboard.php';
	$form_extra = '<input type="hidden" name="parent" value="' . $parent . '">';
	$input_extra = '';
	$rules_extra = '';
	$auth_data = AUTH_DATA;
	if ($raw_post) {
		$form_action = '?';
		$form_extra = '<input type="hidden" name="rawpost" value="1">';
		$input_extra = <<<EOF
			<tr>
			<td class="postblock">
			Reply to
			</td>
			<td>
			<input type="text" name="parent" size="28" maxlength="75" value="0" accesskey="t">&nbsp;0 to start a new thread
			</td>
			</tr>
EOF;
		$rules_extra = <<<EOF
			<ul>
			<li>Text entered in the Message field will be posted as is with no formatting applied.</li>
			<li>Line-breaks must be specified with "&lt;br&gt;".</li>
			</ul><br>
EOF;
	}

	$max_file_size_input_html = '';
	$max_file_size_rules_html = '';
	$reqmod_html = '';
	$filetypes_html = '';
	$file_input_html = '';
	$embed_input_html = '';
	$unique_posts_html = '';

	$captcha_html = '';
	if (TINYIB_CAPTCHA && !$raw_post) {
		if (TINYIB_CAPTCHA === 'recaptcha') {
			$captcha_inner_html = '
				<div style="min-height: 80px;">
				<div class="g-recaptcha" data-sitekey="' . TINYIB_RECAPTCHA_SITE . '"></div>
				<noscript>
				<div>
				<div style="width: 302px; height: 422px; position: relative;">
				<div style="width: 302px; height: 422px; position: absolute;">
				<iframe src="https://www.google.com/recaptcha/api/fallback?k=' . TINYIB_RECAPTCHA_SITE . '" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"></iframe>
				</div>
				</div>
				<div style="width: 300px; height: 60px; border-style: none;bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px;background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
				<textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid #c1c1c1; margin: 10px 25px; padding: 0px; resize: none;"></textarea>
				</div>
				</div>
				</noscript>
				</div>';
		} else { // Simple CAPTCHA
			$captcha_inner_html = '
				<input type="text" name="captcha" id="captcha" size="6" accesskey="c" autocomplete="off">&nbsp;&nbsp;(enter the text below)<br>
				<img id="captchaimage" src="inc/captcha.php" width="175" height="55" alt="CAPTCHA" onclick="javascript:reloadCAPTCHA()" style="margin-top: 5px;cursor: pointer;">';
		}

		$captcha_html = <<<EOF
			<tr>
			<td class="postblock">
			CAPTCHA
			</td>
			<td>
			$captcha_inner_html
			</td>
			</tr>
EOF;
	}

	if (!empty($tinyib_uploads) && ($raw_post || !in_array('file', $hide_fields))) {
		if (TINYIB_MAXKB > 0) {
			$max_file_size_input_html = '<input type="hidden" name="MAX_FILE_SIZE" value="' . strval(TINYIB_MAXKB * 1024) . '">';
			$max_file_size_rules_html = '<li>Maximum file size allowed is ' . TINYIB_MAXKBDESC . '.</li>';
		}

		$filetypes_html = '<li>' . supportedFileTypes() . '</li>';

		$file_input_html = <<<EOF
			<tr class="postform_hidden">
			<td>
			<div class="formlabel">画像ファイル(2MB)</div>
			<input type="file" name="file" size="35" accesskey="f">
			</td>
			</tr>
EOF;
	}

	if (!empty($tinyib_embeds) && ($raw_post || !in_array('embed', $hide_fields))) {
		$embed_input_html = <<<EOF
			<tr class="postform_hidden">
			<td>
			<div class="formlabel">添付URL</div>
			<input type="text" name="embed" size="28" accesskey="x" autocomplete="off">&nbsp;&nbsp;(paste a YouTube URL)
			</td>
			</tr>
EOF;
	}

	if (TINYIB_REQMOD == 'files' || TINYIB_REQMOD == 'all') {
		$reqmod_html = '<li>All posts' . (TINYIB_REQMOD == 'files' ? ' with a file attached' : '') . ' will be moderated before being shown.</li>';
	}

	$thumbnails_html = '';
	if (isset($tinyib_uploads['image/jpeg']) || isset($tinyib_uploads['image/pjpeg']) || isset($tinyib_uploads['image/png']) || isset($tinyib_uploads['image/gif'])) {
		$maxdimensions = TINYIB_MAXWOP . 'x' . TINYIB_MAXHOP;
		if (TINYIB_MAXW != TINYIB_MAXWOP || TINYIB_MAXH != TINYIB_MAXHOP) {
			$maxdimensions .= ' (new thread) or ' . TINYIB_MAXW . 'x' . TINYIB_MAXH . ' (reply)';
		}

		$thumbnails_html = "<li>Images greater than $maxdimensions will be thumbnailed.</li>";
	}

	$unique_posts = uniquePosts();
	if ($unique_posts > 0) {
		$unique_posts_html = "<li>Currently $unique_posts unique user posts.</li>\n";
	}

	$output = "";
	$output .= '<div class="postarea">';
	$output .= '<div align="center"><button id="toggle_postform" class="biggreenbtn">新しい投稿を書く</button></div>';
	$output .= '<form name="postform" id="postform" action="'.$form_action.'" method="post" enctype="multipart/form-data">';
	$output .= $max_file_size_input_html;
	$output .= $form_extra;
	$output .= '<table class="postform">';
	$output .= '<tbody>';
	$output .= $input_extra;

	if ($raw_post || !in_array('name', $hide_fields)) {
		$output .= '<tr class="postform_hidden">';
		$output .= '<td>';
		$output .= '<div class="formlabel">あなたのTG名</div>';
		$output .= '<?php echo $_tpl_name; ?>';
		$output .= '<input type="hidden" name="name" size="28" value="<?php echo $_tpl_name; ?>" accesskey="n">';
		$output .= $postform_extra['name'];
		$output .= '</td>';
		$output .= '</tr>';
	}
	if ($raw_post || !in_array('email', $hide_fields)) {
		$output .= <<<EOF
			<!-- tr>
			<td class="postblock">
			E-mail
			</td>
			<td>
			<input type="text" name="email" size="28" maxlength="75" accesskey="e">
			{$postform_extra['email']}
			</td>
			</tr -->
			<input type="hidden" name="email" value="{$auth_data['id']}">

EOF;
	}
	if ($raw_post || !in_array('subject', $hide_fields)) {
		$output .= <<<EOF
			<tr class="postform_hidden">
			<td>
			<div class="formlabel">件名</div>
			<input type="text" name="subject" id="form_name" maxlength="75" accesskey="s" autocomplete="off">
			</td>
			</tr>
EOF;
	}
	if ($raw_post || !in_array('message', $hide_fields)) {
		$output .= <<<EOF
			<tr class="postform_hidden">
			<td>
			<div class="formlabel">本文（必須）</div>
			<textarea id="message" name="message" accesskey="m"></textarea>
			</td>
			</tr>
EOF;
	}

	$output .= <<<EOF
					$captcha_html
					$file_input_html
					$embed_input_html
EOF;
	if ($raw_post || !in_array('password', $hide_fields)) {
		$output .= <<<EOF
			<tr class="postform_hidden">
			<td>
			<div class="formlabel">投稿のパスワード</div>
			<input type="password" name="password" id="newpostpassword" size="8" accesskey="p">
			&nbsp;&nbsp;(投稿・ファイルの削除用)
			<hr>
			<div align="center">
			{$postform_extra['subject']}
			</div>
			<br><br><br>
			</td>
			</tr>
EOF;
	}
	if ($postform_extra['footer'] != '') {
		$output .= <<<EOF
			<tr class="postform_hidden">
			<td>
			{$postform_extra['footer']}
			</td>
			</tr>
EOF;
	}
	$output .= <<<EOF
		<tr class="postform_hidden">
		<td class="rules">
		$rules_extra
		<ul>
		$reqmod_html
		$filetypes_html
		$max_file_size_rules_html
		$thumbnails_html
		$unique_posts_html
		</ul>
		</td>
		</tr>
		</tbody>
		</table>
		</form>
		</div>
EOF;

	return $output;
}

function buildPost_refLink($res, $post, $threadid){
	if ($res == TINYIB_RESPAGE) {
		$reflink = "<a href=\"{$threadid}.php#{$post['id']}\">No.</a><a href=\"{$threadid}.php#q{$post['id']}\" onclick=\"javascript:quotePost('{$post['id']}')\">{$post['id']}</a>";
	} else {
		$reflink = "<a href=\"res/{$threadid}.php#{$post['id']}\">No.</a><a href=\"res/{$threadid}.php#q{$post['id']}\">{$post['id']}</a>";
	}

	if ($post["stickied"] == 1) {
		$reflink .= ' <img src="sticky.png" alt="Stickied" title="Stickied" width="16" height="16">';
	}
	return $reflink;
}

function buildPost_filehtml($res, $post){

	$filehtml = '';
	$filesize = '';
	$expandhtml = '';
	$direct_link = isEmbed($post["file_hex"]) ?
		"#" :
		(($res == TINYIB_RESPAGE ? "../" : "") . "src/" . $post["file"]);

	if ($post['parent'] == TINYIB_NEWTHREAD && $post["file"] != '') {
		$filesize .= isEmbed($post['file_hex']) ? 'Embed: ' : 'File: ';
	}

	if (isEmbed($post["file_hex"])) {
		$expandhtml = $post['file'];
	} else if (substr($post['file'], -5) == '.webm') {
		$dimensions = 'width="500" height="50"';
		if ($post['image_width'] > 0 && $post['image_height'] > 0) {
			$dimensions = 'width="' . $post['image_width'] . '" height="' . $post['image_height'] . '"';
		}
		$expandhtml = <<<EOF
			<video $dimensions style="position: static; pointer-events: inherit;
			display: inline; max-width: 100%; max-height: 100%;" controls autoplay loop>
			<source src="$direct_link"></source>
			</video>
EOF;
	} else if (in_array(substr($post['file'], -4), array('.jpg', '.png', '.gif'))) {
		$expandhtml = "<a href=\"$direct_link\" onclick=\"return expandFile(event, '${post['id']}');\"><img src=\"" . ($res == TINYIB_RESPAGE ? "../" : "") . "src/${post["file"]}\" width=\"${post["image_width"]}\" style=\"max-width: 100%;height: auto;\"></a>";
	}

	$thumblink = "<a href=\"$direct_link\" target=\"_blank\"" . ((isEmbed($post["file_hex"]) || in_array(substr($post['file'], -4), array('.jpg', '.png', '.gif', 'webm'))) ? " onclick=\"return expandFile(event, '${post['id']}');\"" : "") . ">";
	$expandhtml = rawurlencode($expandhtml);

	if (isEmbed($post["file_hex"])) {
		$filesize .= "<a href=\"$direct_link\" onclick=\"return expandFile(event, '${post['id']}');\">${post['file_original']}</a>&ndash;(${post['file_hex']})";
	} else if ($post["file"] != '') {
		$filesize .= $thumblink . "${post["file"]}</a>&ndash;(${post["file_size_formatted"]}";
		if ($post["image_width"] > 0 && $post["image_height"] > 0) {
			$filesize .= ", " . $post["image_width"] . "x" . $post["image_height"];
		}
		if ($post["file_original"] != "") {
			$filesize .= ", " . $post["file_original"];
		}
		$filesize .= ")";
	}

	if ($filesize != '') {
		$filesize = '<span class="filesize condensed">' . $filesize . '</span>';
	}

	if ($filesize != '') {
		if ($post['parent'] != TINYIB_NEWTHREAD) {
			$filehtml .= '<br>';
		}
		$filehtml .= $filesize . '<br><div id="thumbfile' . $post['id'] . '" class="thumbfile">';
		if ($post["thumb_width"] > 0 && $post["thumb_height"] > 0) {
			$filehtml .= <<<EOF
$thumblink
	<img src="thumb/${post["thumb"]}" alt="${post["id"]}" class="thumb" id="thumbnail${post['id']}" width="${post["thumb_width"]}" height="${post["thumb_height"]}">
</a>
EOF;
		}
		$filehtml .= '</div>';

		if ($expandhtml != '') {
			$filehtml .= <<<EOF
<div id="expand${post['id']}" style="display: none;">$expandhtml</div>
<div id="file${post['id']}" class="thumb" style="display: none;"></div>
EOF;
		}
	}
	return $filehtml;
}

function buildPost($post, $res) {
	$return = "";
	$post["omitted"] = (!isset($post["omitted"]))?0:$post["omitted"];
	$threadid = ($post['parent'] == TINYIB_NEWTHREAD) ? $post['id'] : $post['parent'];

	$reflink = buildPost_refLink($res, $post, $threadid);
	$filehtml = buildPost_filehtml($res, $post);

	if ($post["parent"] == TINYIB_NEWTHREAD) {
		$return .= $filehtml;
	} else {
		$return .= '<table><tbody><tr>'
		.'<td class="doubledash">&#0168;</td>'
		.'<td class="reply" id="reply'.$post["id"].'">';
	}

	$return .= '<a id="'.$post['id'].'"></a>';
	$return .= '<label class="filetitle">';

	$return .= ($post['subject'] != '')?
		' <span>' . $post['subject'] . '</span> ':
		'';

	$return .= '</label><br>';
	$return .= '<input type="checkbox" name="delete" value="'.$post['id'].'">';
	$return .= $post["nameblock"];
	$return .= '<!-- span class="reflink">'.$reflink.'</span -->';

	$return .= ($post['parent'] != TINYIB_NEWTHREAD)?
		$filehtml:
		"";


	if (TINYIB_TRUNCATE > 0 && !$res && substr_count($post['message'], '<br>') > TINYIB_TRUNCATE) {
		// Truncate messages on board index pages for readability
		$br_offsets = strallpos($post['message'], '<br>');
		$post['message'] = substr($post['message'], 0, $br_offsets[TINYIB_TRUNCATE - 1]);
		$post['message'] .= '<br><span class="omittedposts">
			（ → <a href="res/'.$post["id"].'.php">全文を表示します</a>　）</span><br>';
	}
	$return .= <<<EOF
		<div class="message">
		${post["message"]}
		</div>
EOF;

	if ($post['parent'] == TINYIB_NEWTHREAD && $res == TINYIB_INDEXPAGE) {
		$return .= "<div class='res_permalink'>[<a href=\"res/${post["id"]}.php\">返信 / Permalink</a>]</div>";
	}

	if ($post['parent'] == TINYIB_NEWTHREAD) {
		if ($res == TINYIB_INDEXPAGE && $post['omitted'] > 0) {
			$return .= '<span class="omittedposts">' . $post['omitted'] . ' ' . plural('post', $post['omitted']) . ' omitted. Click Reply to view.</span>';
		}
	} else {
		$return .= <<<EOF
			</td>
			</tr>
			</tbody>
			</table>
EOF;
	}

	return $return;
}


function buildPage_pagenavigator($pages, $thispage ){
	$pages = max($pages, 0);
	$previous = ($thispage == 1) ? "index" : $thispage - 1;
	$next = $thispage + 1;

	$pagelinks = ($thispage == 0) ?
		"<td>新しい投稿</td>" :
		'<td><form method="get" action="' . $previous . '.php"><input class="biggreenbtn" value="←新しい" type="submit"></form></td>';
	$pagelinks .= "<td>";
	for ($i = 0; $i <= $pages; $i++) {
		if ($thispage == $i) {
			$pagelinks .= '&#91;' . $i . '&#93; ';
		} else {
			$href = ($i == 0) ? "index" : $i;
			$pagelinks .= '&#91;<a href="' . $href . '.php">' . $i . '</a>&#93; ';
		}
	}
	$pagelinks .= "</td>";
	$pagelinks .= ($pages <= $thispage) ?
		"<td>過去の投稿</td>" :
		'<td><form method="get" action="' . $next . '.php"><input class="biggreenbtn" value="過去へ→" type="submit"></form></td>';

	$pagenavigator = '<table border="1" id="pagelinks"><tbody><tr>'
		.$pagelinks.'</tr></tbody></table>';
	return $pagenavigator;
}

function buildPage($htmlposts, $parent, $pages = 0, $thispage = 0) {
	$managelink = basename($_SERVER['PHP_SELF']) . "?manage";

	$postingmode = "";
	$pagenavigator = "";
	if ($parent == TINYIB_NEWTHREAD) {
		$pagenavigator = buildPage_pagenavigator($pages, $thispage );

	} else {
		$postingmode = '<div class="res_backtotop">&#91;<a href="../">'
			.'トップページに戻る</a>&#93;</div>';
		$pagenavigator = $postingmode;
	}

	$postform = buildPostForm($parent);
	if ($parent != TINYIB_NEWTHREAD) {
		$postform = str_replace("新しい投稿を書く", "以下の投稿にコメントする", $postform);
	}

	$body = <<<EOF
	<body>
		<div class="adminbar">
			[<a href="$managelink" style="text-decoration: underline;">Manage</a>]
		</div>
		<div class="logo">
EOF;
	$body .= TINYIB_LOGO . TINYIB_BOARDDESC . <<<EOF
		</div>
		<hr width="90%">
		$postingmode
		$postform
		<hr>
		<form id="delform" action="imgboard.php?delete" method="post">
		<input type="hidden" name="board"
EOF;
	$body .= 'value="' . TINYIB_BOARD . '">' . <<<EOF
		$htmlposts
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
		$pagenavigator
		<br>
EOF;
	$is_subdir = ($parent == TINYIB_NEWTHREAD)?false:true;
	return pageHeader($is_subdir) . $body . pageFooter();
}

function rebuildIndexes() {
	$page = 0;
	$i = 0;
	$htmlposts = '';
	$threads = allThreads();
	$pages = ceil(count($threads) / TINYIB_THREADSPERPAGE) - 1;

	foreach ($threads as $thread) {
		$replies = postsInThreadByID($thread['id']);
		$thread['omitted'] = max(0, count($replies) - TINYIB_PREVIEWREPLIES - 1);

		// Build replies for preview
		$htmlreplies = array();
		for ($j = count($replies) - 1; $j > $thread['omitted']; $j--) {
			$htmlreplies[] = buildPost($replies[$j], TINYIB_INDEXPAGE);
		}

		$htmlposts .= buildPost($thread, TINYIB_INDEXPAGE) . implode('', array_reverse($htmlreplies)) . "\n<hr>";

		if (++$i >= TINYIB_THREADSPERPAGE) {
			$file = ($page == 0) ? TINYIB_INDEX : ($page . '.php');
			writePage($file, buildPage($htmlposts, 0, $pages, $page));

			$page++;
			$i = 0;
			$htmlposts = '';
		}
	}

	if ($page == 0 || $htmlposts != '') {
		$file = ($page == 0) ? TINYIB_INDEX : ($page . '.php');
		writePage($file, buildPage($htmlposts, 0, $pages, $page));
	}
}

function rebuildThread($id) {
	$htmlposts = "";
	$posts = postsInThreadByID($id);
	foreach ($posts as $post) {
		$htmlposts .= buildPost($post, TINYIB_RESPAGE);
	}

	$htmlposts .= "\n<hr>";

	writePage('res/' . $id . '.php', fixLinksInRes(buildPage($htmlposts, $id)));
}

function adminBar() {
	global $loggedin, $isadmin, $returnlink;
	$return = '[<a href="' . $returnlink . '" style="text-decoration: underline;">Return</a>]';
	if (!$loggedin) {
		return $return;
	}

	return '[<a href="?manage">Status</a>] [' . (($isadmin) ? '<a href="?manage&bans">Bans</a>] [' : '') . '<a href="?manage&moderate">Moderate Post</a>] [<a href="?manage&rawpost">Raw Post</a>] [' . (($isadmin) ? '<a href="?manage&rebuildall">Rebuild All</a>] [' : '') . (($isadmin && installedViaGit()) ? '<a href="?manage&update">Update</a>] [' : '') . (($isadmin && TINYIB_DBMIGRATE) ? '<a href="?manage&dbmigrate"><b>Migrate Database</b></a>] [' : '') . '<a href="?manage&logout">Log Out</a>] &middot; ' . $return;
}

function managePage($text, $onload = '') {
	$adminbar = adminBar();
	$body = <<<EOF
	<body$onload>
		<div class="adminbar">
			$adminbar
		</div>
		<div class="logo">
EOF;
	$body .= TINYIB_LOGO . TINYIB_BOARDDESC . <<<EOF
		</div>
		<hr width="90%">
		<div class="replymode">Manage mode</div>
		$text
		<hr>
EOF;
	return pageHeader(false) . $body . pageFooter();
}

function manageOnLoad($page) {
	switch ($page) {
		case 'login':
			return ' onload="document.tinyib.managepassword.focus();"';
		case 'moderate':
			return ' onload="document.tinyib.moderate.focus();"';
		case 'rawpost':
			return ' onload="document.tinyib.message.focus();"';
		case 'bans':
			return ' onload="document.tinyib.ip.focus();"';
	}
}

function manageLogInForm() {
	return <<<EOF
	<form id="tinyib" name="tinyib" method="post" action="?manage">
	<fieldset>
	<legend align="center">Enter an administrator or moderator password</legend>
	<div class="login">
	<input type="password" id="managepassword" name="managepassword"><br>
	<input type="submit" value="Log In" class="managebutton">
	</div>
	</fieldset>
	</form>
	<br>
EOF;
}

function manageBanForm() {
	return <<<EOF
	<form id="tinyib" name="tinyib" method="post" action="?manage&bans">
	<fieldset>
	<legend>Ban an IP address</legend>
	<label for="ip">IP Address:</label> <input type="text" name="ip" id="ip" value="${_GET['bans']}"> <input type="submit" value="Submit" class="managebutton"><br>
	<label for="expire">Expire(sec):</label> <input type="text" name="expire" id="expire" value="0">&nbsp;&nbsp;<small><a href="#" onclick="document.tinyib.expire.value='3600';return false;">1hr</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='86400';return false;">1d</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='172800';return false;">2d</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='604800';return false;">1w</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='1209600';return false;">2w</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='2592000';return false;">30d</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='0';return false;">never</a></small><br>
	<label for="reason">Reason:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label> <input type="text" name="reason" id="reason">&nbsp;&nbsp;<small>optional</small>
	<legend>
	</fieldset>
	</form><br>
EOF;
}

function manageBansTable() {
	$text = '';
	$allbans = allBans();
	if (count($allbans) > 0) {
		$text .= '<table border="1"><tr><th>IP Address</th><th>Set At</th><th>Expires</th><th>Reason Provided</th><th>&nbsp;</th></tr>';
		foreach ($allbans as $ban) {
			$expire = ($ban['expire'] > 0) ? date('y/m/d(D)H:i:s', $ban['expire']) : 'Does not expire';
			$reason = ($ban['reason'] == '') ? '&nbsp;' : htmlentities($ban['reason']);
			$text .= '<tr><td>' . $ban['ip'] . '</td><td>' . date('y/m/d(D)H:i:s', $ban['timestamp']) . '</td><td>' . $expire . '</td><td>' . $reason . '</td><td><a href="?manage&bans&lift=' . $ban['id'] . '">lift</a></td></tr>';
		}
		$text .= '</table>';
	}
	return $text;
}

function manageModeratePostForm() {
	return <<<EOF
	<form id="tinyib" name="tinyib" method="get" action="?">
	<input type="hidden" name="manage" value="">
	<fieldset>
	<legend>Moderate a post</legend>
	<div valign="top"><label for="moderate">Post ID:</label> <input type="text" name="moderate" id="moderate"> <input type="submit" value="Submit" class="managebutton"></div><br>
	<small><b>Tip:</b> While browsing the image board, you can easily moderate a post if you are logged in:<br>
	Tick the box next to a post and click "Delete" at the bottom of the page with a blank password.</small><br>
	</fieldset>
	</form><br>
EOF;
}

function manageModeratePost($post) {
	global $isadmin;
	$ban = banByIP($post['ip']);
	$ban_disabled = (!$ban && $isadmin) ? '' : ' disabled';
	$ban_info = (!$ban) ? ((!$isadmin) ? 'Only an administrator may ban an IP address.' : ('IP address: ' . $post["ip"])) : (' A ban record already exists for ' . $post['ip']);
	$delete_info = ($post['parent'] == TINYIB_NEWTHREAD) ? 'This will delete the entire thread below.' : 'This will delete the post below.';
	$post_or_thread = ($post['parent'] == TINYIB_NEWTHREAD) ? 'Thread' : 'Post';

	$sticky_html = "";
	if ($post["parent"] == TINYIB_NEWTHREAD) {
		$sticky_set = $post['stickied'] == 1 ? '0' : '1';
		$sticky_unsticky = $post['stickied'] == 1 ? 'Un-sticky' : 'Sticky';
		$sticky_unsticky_help = $post['stickied'] == 1 ? 'Return this thread to a normal state.' : 'Keep this thread at the top of the board.';
		$sticky_html = <<<EOF
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td align="right" width="50%;">
		<form method="get" action="?">
		<input type="hidden" name="manage" value="">
		<input type="hidden" name="sticky" value="${post['id']}">
		<input type="hidden" name="setsticky" value="$sticky_set">
		<input type="submit" value="$sticky_unsticky Thread" class="managebutton" style="width: 50%;">
		</form>
	</td><td><small>$sticky_unsticky_help</small></td></tr>
EOF;

		$post_html = "";
		$posts = postsInThreadByID($post["id"]);
		foreach ($posts as $post_temp) {
			$post_html .= buildPost($post_temp, TINYIB_INDEXPAGE);
		}
	} else {
		$post_html = buildPost($post, TINYIB_INDEXPAGE);
	}

	return <<<EOF
	<fieldset>
	<legend>Moderating No.${post['id']}</legend>

	<fieldset>
	<legend>Action</legend>

	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr><td align="right" width="50%;">

	<form method="get" action="?">
	<input type="hidden" name="manage" value="">
	<input type="hidden" name="delete" value="${post['id']}">
	<input type="submit" value="Delete $post_or_thread" class="managebutton" style="width: 50%;">
	</form>

	</td><td><small>$delete_info</small></td></tr>
	<tr><td align="right" width="50%;">

	<form method="get" action="?">
	<input type="hidden" name="manage" value="">
	<input type="hidden" name="bans" value="${post['ip']}">
	<input type="submit" value="Ban Poster" class="managebutton" style="width: 50%;"$ban_disabled>
	</form>

	</td><td><small>$ban_info</small></td></tr>

	$sticky_html

	</table>

	</fieldset>

	<fieldset>
	<legend>$post_or_thread</legend>
	$post_html
	</fieldset>

	</fieldset>
	<br>
EOF;
}

function manageStatus() {
	global $isadmin;
	$threads = countThreads();
	$bans = count(allBans());
	$info = $threads . ' ' . plural('thread', $threads) . ', ' . $bans . ' ' . plural('ban', $bans);
	$output = '';

	if ($isadmin && TINYIB_DBMODE == 'mysql' && function_exists('mysqli_connect')) { // Recommend MySQLi
		$output .= <<<EOF
	<fieldset>
	<legend>Notice</legend>
	<p><b>TINYIB_DBMODE</b> is currently <b>mysql</b> in <b>settings.php</b>, but <a href="http://www.php.net/manual/en/book.mysqli.php">MySQLi</a> is installed.  Please change it to <b>mysqli</b>.  This will not affect your data.</p>
	</fieldset>
EOF;
	}

	$reqmod_html = '';

	if (TINYIB_REQMOD == 'files' || TINYIB_REQMOD == 'all') {
		$reqmod_post_html = '';

		$reqmod_posts = latestPosts(false);
		foreach ($reqmod_posts as $post) {
			if ($reqmod_post_html != '') {
				$reqmod_post_html .= '<tr><td colspan="2"><hr></td></tr>';
			}
			$reqmod_post_html .= '<tr><td>' . buildPost($post, TINYIB_INDEXPAGE) . '</td><td valign="top" align="right">
			<table border="0"><tr><td>
			<form method="get" action="?"><input type="hidden" name="manage" value=""><input type="hidden" name="approve" value="' . $post['id'] . '"><input type="submit" value="Approve" class="managebutton"></form>
			</td><td>
			<form method="get" action="?"><input type="hidden" name="manage" value=""><input type="hidden" name="moderate" value="' . $post['id'] . '"><input type="submit" value="More Info" class="managebutton"></form>
			</td></tr><tr><td align="right" colspan="2">
			<form method="get" action="?"><input type="hidden" name="manage" value=""><input type="hidden" name="delete" value="' . $post['id'] . '"><input type="submit" value="Delete" class="managebutton"></form>
			</td></tr></table>
			</td></tr>';
		}

		if ($reqmod_post_html != '') {
			$reqmod_html = <<<EOF
	<fieldset>
	<legend>Pending posts</legend>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	$reqmod_post_html
	</table>
	</fieldset>
EOF;
		}
	}

	$post_html = '';
	$posts = latestPosts(true);
	foreach ($posts as $post) {
		if ($post_html != '') {
			$post_html .= '<tr><td colspan="2"><hr></td></tr>';
		}
		$post_html .= '<tr><td>' . buildPost($post, TINYIB_INDEXPAGE) . '</td><td valign="top" align="right"><form method="get" action="?"><input type="hidden" name="manage" value=""><input type="hidden" name="moderate" value="' . $post['id'] . '"><input type="submit" value="Moderate" class="managebutton"></form></td></tr>';
	}

	$output .= <<<EOF
	<fieldset>
	<legend>Status</legend>

	<fieldset>
	<legend>Info</legend>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tbody>
	<tr><td>
		$info
	</td>
	</tr>
	</tbody>
	</table>
	</fieldset>

	$reqmod_html

	<fieldset>
	<legend>Recent posts</legend>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	$post_html
	</table>
	</fieldset>

	</fieldset>
	<br>
EOF;

	return $output;
}

function manageInfo($text) {
	return '<div class="manageinfo">' . $text . '</div>';
}
