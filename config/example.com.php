<?php

class Asaph_Config {

  // This title is used in templates and the rss feed
  public static $title = 'Ark Blog';

  // Domain name and path where Ark is installed in
  public static $domain = 'example.com';
  public static $absolutePath = '/';

  // If you want to be able to move/edit/delete generated files and folders
  // with your ftp-client, it's likely you'll have to set chmod to 0777
  public static $defaultChmod = 0777;

  public static $postsPerPage = 10;
  public static $adminPostsPerPage = 50;

  // Templates
  public static $templates = array(
    'posts' => 'templates/ark/posts.html.php',
    'about' => 'templates/ark/about.html.php',
    'feed' => 'templates/rss.xml.php'
  );

  // Settings for your mysql database
  public static $db = array(
    'host' => 'localhost',
    'database' => 'asaph',
    'user' => '',
    'password' => '',
    'prefix' => 'asaph_'
  );

  // Image and thumbnail settings
  public static $images = array(
    'imagePath' => 'data/images/',
    'thumbPath' => 'data/thumbs/',
    'thumbWidth' => 256,
    'thumbHeight' => 192,
    'jpegQuality' => 80,
  );
}


// Don't edit anything below here, unless you know what you're doing

define( 'ASAPH_TABLE_POSTS',  Asaph_Config::$db['prefix'].'posts' );
define( 'ASAPH_TABLE_USERS',  Asaph_Config::$db['prefix'].'users' );
define( 'ASAPH_TABLE_IMAGES', Asaph_Config::$db['prefix'].'images' );
define( 'ASAPH_TABLE_VIDEOS', Asaph_Config::$db['prefix'].'videos' );
define( 'ASAPH_TABLE_QUOTES', Asaph_Config::$db['prefix'].'quotes' );
define( 'ASAPH_TABLE_COLLECTIONS',  Asaph_Config::$db['prefix'].'collections' );
define( 'ASAPH_TABLE_TAGS', Asaph_Config::$db['prefix'].'tags' );
define( 'ASAPH_TABLE_POSTS_TAGS',  Asaph_Config::$db['prefix'].'posts_tags' );
define( 'ASAPH_TABLE_SETTINGS',  Asaph_Config::$db['prefix'].'settings' );

define( 'ASAPH_BASE_URL',   'http://'.Asaph_Config::$domain.Asaph_Config::$absolutePath );
define( 'ASAPH_POST_PHP',   ASAPH_BASE_URL.'admin/post.php' );
define( 'ASAPH_POST_JS',    ASAPH_BASE_URL.'admin/post.js.php' );
define( 'ASAPH_POST_CSS',   ASAPH_BASE_URL.'admin/templates/css/post.css' );

if( function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ) {
  $_GET = array_map( 'stripslashes', $_GET );
  $_POST = array_map( 'stripslashes', $_POST );
}

?>
