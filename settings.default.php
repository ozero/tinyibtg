<?php
# TinyIB
#
# https://gitlab.com/tslocum/tinyib
#
# Contact the author via trevor@rocketnine.space if you need support.
# See README for instructions on configuring, moderating and upgrading your board.
#
# Set TINYIB_DBMODE to a MySQL-related mode if it's available.  By default it's set to flatfile, which can be very slow.

// Administrator/moderator credentials
define('TINYIB_ADMINPASS', '');       // Administrators have full access to the board
define('TINYIB_MODPASS', '');         // Moderators only have access to delete (and moderate if TINYIB_REQMOD is set) posts  ['' to disable]

// Board description and behavior
define('TINYIB_BOARD', 'b');          // Unique identifier for this board using only letters and numbers
define('TINYIB_BOARDDESC', 'TinyIB'); // Displayed at the top of every page
define('TINYIB_ALWAYSNOKO', false);   // Redirect to thread after posting
define('TINYIB_CAPTCHA', '');         // Reduce spam by requiring users to pass a CAPTCHA when posting: simple / recaptcha  (click Rebuild All in the management panel after enabling)  ['' to disable]
define('TINYIB_REQMOD', '');          // Require moderation before displaying posts: files / all  (see README for instructions, only MySQL is supported)  ['' to disable]

// Board appearance
define('TINYIB_INDEX', 'index.html'); // Index file
define('TINYIB_LOGO', '');            // Logo HTML
define('TINYIB_THREADSPERPAGE', 10);  // Amount of threads shown per index page
define('TINYIB_PREVIEWREPLIES', 3);   // Amount of replies previewed on index pages
define('TINYIB_TRUNCATE', 15);        // Messages are truncated to this many lines on board index pages  [0 to disable]
define('TINYIB_WORDBREAK', 0);       // Words longer than this many characters will be broken apart  [0 to disable]
define('TINYIB_TIMEZONE', 'UTC');     // See https://secure.php.net/manual/en/timezones.php - e.g. America/Los_Angeles
$tinyib_hidefieldsop = array();       // Fields to hide when creating a new thread - e.g. array('name', 'email', 'subject', 'message', 'file', 'embed', 'password')
$tinyib_hidefields = array();         // Fields to hide when replying

// Post control
define('TINYIB_DELAY', 30);           // Delay (in seconds) between posts from the same IP address to help control flooding  [0 to disable]
define('TINYIB_MAXTHREADS', 100);     // Oldest threads are discarded when the thread count passes this limit  [0 to disable]
define('TINYIB_MAXREPLIES', 0);       // Maximum replies before a thread stops bumping  [0 to disable]

// Upload types
//   Empty array to disable
//   Format: MIME type => (extension, optional thumbnail)
$tinyib_uploads = array('image/jpeg'                    => array('jpg'),
                        'image/pjpeg'                   => array('jpg'),
                        'image/png'                     => array('png'),
                        'image/gif'                     => array('gif'));
#                       'application/x-shockwave-flash' => array('swf', 'swf_thumbnail.png'));
#                       'video/webm'                    => array('webm')); // WebM upload requires mediainfo and ffmpegthumbnailer  (see README for instructions)
#                       'audio/webm'                    => array('webm'));

// oEmbed APIs
//   Empty array to disable
$tinyib_embeds = array('SoundCloud' => 'http://soundcloud.com/oembed?format=json&url=TINYIBEMBED',
                       'Vimeo'      => 'http://vimeo.com/api/oembed.json?url=TINYIBEMBED',
                       'YouTube'    => 'http://www.youtube.com/oembed?url=TINYIBEMBED&format=json');

// File control
define('TINYIB_MAXKB', 2048);         // Maximum file size in kilobytes  [0 to disable]
define('TINYIB_MAXKBDESC', '2 MB');   // Human-readable representation of the maximum file size
define('TINYIB_THUMBNAIL', 'gd');     // Thumbnail method to use: gd / imagemagick  (see README for instructions)
define('TINYIB_NOFILEOK', false);     // Allow the creation of new threads without uploading a file

// Thumbnail size - new thread
define('TINYIB_MAXWOP', 250);         // Width
define('TINYIB_MAXHOP', 250);         // Height

// Thumbnail size - reply
define('TINYIB_MAXW', 250);           // Width
define('TINYIB_MAXH', 250);           // Height

// Tripcode seed - Must not change once set!
define('TINYIB_TRIPSEED', '');        // Enter some random text  (used when generating secure tripcodes)

// CAPTCHA
//   The following only apply when TINYIB_CAPTCHA is set to recaptcha
//   For API keys visit https://www.google.com/recaptcha
define('TINYIB_RECAPTCHA_SITE', '');  // Site key
define('TINYIB_RECAPTCHA_SECRET', '');// Secret key

// Database
//   Recommended database modes from best to worst:
//     pdo, mysqli, mysql, sqlite3, sqlite (deprecated), flatfile (only useful if you need portability or lack any kind of database)
define('TINYIB_DBMODE', 'flatfile');  // Mode
define('TINYIB_DBMIGRATE', false);    // Enable database migration tool  (see README for instructions)
define('TINYIB_DBBANS', 'bans');      // Bans table name (use the same bans table across boards for global bans)
define('TINYIB_DBPOSTS', TINYIB_BOARD . '_posts'); // Posts table name

// Database configuration - MySQL / pgSQL
//   The following only apply when TINYIB_DBMODE is set to mysql, mysqli or pdo with default (blank) TINYIB_DBDSN
define('TINYIB_DBHOST', 'localhost'); // Hostname
define('TINYIB_DBPORT', 3306);        // Port  (set to 0 if you are using a UNIX socket as the host)
define('TINYIB_DBUSERNAME', '');      // Username
define('TINYIB_DBPASSWORD', '');      // Password
define('TINYIB_DBNAME', '');          // Database
define('TINYIB_DB_PATH', '../data/tinyib.db');           // When using sqlite3, put relative path of your sqlite db file

// Database configuration - PDO
//   The following only apply when TINYIB_DBMODE is set to pdo  (see README for instructions)
define('TINYIB_DBDRIVER', 'mysql');   // PDO driver to use (mysql / pgsql / sqlite / etc.)
define('TINYIB_DBDSN', '');           // Enter a custom DSN to override all of the connection/driver settings above  (see README for instructions)
//                                         When changing this, you should still set TINYIB_DBDRIVER appropriately.
//                                         If you're using PDO with a MySQL or pgSQL database, you should leave this blank.

define('TINYIB_PATH_INSTALL', '/home/gyouza/www/ib.currentdir.com/docs');//Installed path

// Telegram Login
define('BOT_USERNAME', ''); // place username of your bot here
define('BOT_TOKEN', ''); // place bot token of your bot here
define('BOT_CHATROOM_ID', '["-271345","-123490786","-9872369"]'); // place groupchat ids which allow to access

