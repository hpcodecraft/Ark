<?php
  error_reporting(E_ALL);

  ini_set('display_errors', 'On');

  define( 'ASAPH_PATH', '../' );
  header( 'Content-type: text/html; charset=utf-8' );

  require_once( ASAPH_PATH.'lib/asaph_admin.class.php' );
  $asaphAdmin = new Asaph_Admin( Asaph_Config::$adminPostsPerPage );

  if( isset($_POST['login']) ) {
    if( $asaphAdmin->login($_POST['name'], $_POST['pass']) ) {
      header( 'Location: '.Asaph_Config::$absolutePath.'admin/' );
    }
    else {
      $loginError = true;
      include( ASAPH_PATH.'admin/templates/login.html.php' );
    }
  }
  else if(!$asaphAdmin->checkLogin()) {
    include( ASAPH_PATH.'admin/templates/login.html.php' );
  }
  else {

    // load collections
    $collections = $asaphAdmin->getCollections();

    if( isset($_GET['logout']) ) {
      $asaphAdmin->logout();
      header( 'Location: '.Asaph_Config::$absolutePath.'admin/' );
      exit;
    }


    if(isset($_GET['collection'])) {
      $posts = $asaphAdmin->getPostsFromCollection( $_GET['collection'], empty($_GET['page']) ? 0 : $_GET['page']-1 );
    }
    else $posts = $asaphAdmin->getPosts( empty($_GET['page']) ? 0 : $_GET['page']-1 );

    $pages = $asaphAdmin->getPages();
    include( ASAPH_PATH.'admin/templates/posts.html.php' );
  }
?>
