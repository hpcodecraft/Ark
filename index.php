<?php
  error_reporting(E_ALL);

  ini_set('display_errors', 'On');

  date_default_timezone_set("Europe/Berlin");
  define( 'ASAPH_PATH', '' );
  require_once( ASAPH_PATH.'lib/asaph.class.php' );

  header( 'Content-type: text/html; charset=utf-8' );

  // Is mod_rewrite enabled? (see .htaccess)
  if( isset($_GET['rw']) ) {
    define( 'ASAPH_LINK_PREFIX', Asaph_Config::$absolutePath );
    $params = explode( '/', $_GET['rw'] );
  } else {
    define( 'ASAPH_LINK_PREFIX', Asaph_Config::$absolutePath.'?' );
    $params = empty($_GET) ? array() : explode( '/', key($_GET) );
  }

  $asaph = new Asaph( Asaph_Config::$postsPerPage );
  $collections = array();
  $settings = $asaph->getSettings();

  include('lib/functions.php');

  if(!empty($params[0])) {
    switch($params[0]) {
      case 'about':
        include(ASAPH_PATH.Asaph_Config::$templates['about']);
        break;

      case 'feed':
        $posts = $asaph->getPosts(0);
        include(ASAPH_PATH.Asaph_Config::$templates['feed']);
        break;

      case 'collection':
        $display_mode = 'collection';
        $collection = $params[1];
        $collection_name = $asaph->getCollectionName($collection);
        $page = !empty($params[3]) ? $params[3]-1 : 0;

        $posts = $asaph->getPostsOfCollection($collection, $page);
        $pages = $asaph->getPages();
        include(ASAPH_PATH.Asaph_Config::$templates['posts']);
        break;

      case 'post':
        $display_mode = 'post';
        $postid = !empty($params[1]) ? $params[1] : 0;
        $asaph = new Asaph(1);
        $post = $asaph->getPost($postid);

        if(empty($post)) {
          $post = array("description" => "<h3>404 Post not found</h3>
          <p>Oops, something went wrong here. <a href='".Asaph_Config::$absolutePath."'>Beam me home, Scotty!</a></p>","created" => time(), "user" => "Ark");
        }
        $posts = array($post);
        include(ASAPH_PATH.Asaph_Config::$templates['posts']);
        break;

      default:
        $display_mode = 'blog';
        $page = !empty($params[1]) ? $params[1]-1 : 0;
        $collections = $asaph->getFeaturedCollections();
        $posts = $asaph->getPosts( $page );
        $pages = $asaph->getPages();
        include(ASAPH_PATH.Asaph_Config::$templates['posts']);
        break;
    }
  }
  else {
    // front page
    $display_mode = 'blog';
    $collections = $asaph->getFeaturedCollections();
    $posts = $asaph->getPosts(0);
    $pages = $asaph->getPages();
    include(ASAPH_PATH.Asaph_Config::$templates['posts']);
  }
?>
