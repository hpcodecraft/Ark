<?php
define( 'ASAPH_PATH', '../' );
require_once( ASAPH_PATH.'lib/asaph_admin.class.php' );

header( 'Content-type: text/html; charset=utf-8' );

function getGETString() {
  $getArr = array();
  foreach($_GET as $k => $v) {
    array_push($getArr, $k.'='.urlencode($v));
  }
  return implode('&', $getArr);
}

$asaphAdmin = new Asaph_Admin( Asaph_Config::$adminPostsPerPage );

if(!$asaphAdmin->checkLogin()) exit;

// handle actions
$status = '';

if(isset($_POST['action'])) {
  switch($_POST['action']) {

    case 'add-collection':
      $status = $asaphAdmin->addCollection( $_POST['name'] );
      break;

    case 'edit-collection':
      if(isset($_POST['delete']) && $_POST['delete'] == 'on') {
        $status = $asaphAdmin->deleteCollection( $_POST['id'] );
      }
      else {
        $nsfw = 0;
        $featured = 0;
        if(isset($_POST['nsfw']) && $_POST['nsfw'] == 'on') $nsfw = 1;
        if(isset($_POST['featured']) && $_POST['featured'] == 'on') $featured = 1;
        $status = $asaphAdmin->updateCollection( $_POST['id'], $_POST['name'], $nsfw, $featured );
      }
      break;

    case 'edit-account':
      $status = $asaphAdmin->updateUser( $_POST['id'], $_POST['name'], $_POST['password'], $_POST['password2'] );
      break;

    case 'edit-post':
      if(isset($_POST['delete']) && $_POST['delete'] == 'on') {
        $status = $asaphAdmin->deletePost( $_POST['id'] );
      }
      else {
        if($_POST['type'] == 'quote') {
          $status = $asaphAdmin->updateQuote($_POST['quote-id'], $_POST['quote'], $_POST['speaker']);
        }

        $public = 0;
        $nsfw = 0;
        if(isset($_POST['public']) && $_POST['public'] == 'on') $public = 1;
        if(isset($_POST['nsfw']) && $_POST['nsfw'] == 'on') $nsfw = 1;
        $status = $asaphAdmin->updatePost( $_POST['id'], $_POST['title'], $_POST['description'], $public, $_POST['collection'], $_POST['tags'], $nsfw);
      }
      break;

    case 'settings':

      $nsfw_content_admin = 0;
      if(isset($_POST['admin_show_nsfw_content']) && $_POST['admin_show_nsfw_content'] == 'on') $nsfw_content_admin = 1;

      $nsfw_content_website = 0;
      if(isset($_POST['public_page_show_nsfw_content']) && $_POST['public_page_show_nsfw_content'] == 'on') $nsfw_content_website = 1;

      $status = $asaphAdmin->updateSettings($nsfw_content_admin, $nsfw_content_website);
      break;
  }
}


if(isset($_GET['dialog'])) {
  switch($_GET['dialog']) {
    case 'edit-collection':
      $collection = $asaphAdmin->getCollection($_GET['id']);
      break;
    case 'edit-account':
      $user = $asaphAdmin->getUser( $asaphAdmin->userId );
      break;
    case 'edit-post':
      $collections = $asaphAdmin->getCollections();
      $post = $asaphAdmin->getPost( $_GET['id'] );
      break;
    case 'settings':
      $settings = $asaphAdmin->getSettings();
      break;
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ark - Admin Dialog</title>
  <link href='https://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="<?php echo Asaph_Config::$absolutePath; ?>admin/templates/css/jquery.tagsinput.min.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo Asaph_Config::$absolutePath; ?>admin/templates/css/admin.css" />
  <link rel="Shortcut Icon" href="<?php echo Asaph_Config::$absolutePath; ?>admin/templates/asaph.ico" />
</head>
<body class="ark-dialog-body">

  <?php if(isset($_GET['dialog'])) include(ASAPH_PATH.'admin/templates/dialog/'.$_GET['dialog'].'.html.php'); ?>

  <?php if($status === true): ?>
  <script>parent.location.reload()</script>
  <?php endif; ?>

  <script src="templates/js/jquery-1.10.2.min.js"></script>
  <script src="templates/js/jquery.tagsinput.min.js"></script>

  <script>
    $(function() {
      $('#post-tags').tagsInput({
         'height':'40px',
         'width':'305px',
         'interactive':true,
         'defaultText':'add a tag',
         'removeWithBackspace' : true,
         'minChars' : 0,
         'maxChars' : 0,
         'placeholderColor' : '#666666'
      });
    });
  </script>
</body>
</html>
