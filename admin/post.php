<?php
define( 'ASAPH_PATH', '../' );
require_once( ASAPH_PATH.'lib/asaph_post.class.php' );

header( 'Content-type: text/html; charset=utf-8' );

$asaphPost = new Asaph_Post();

// logged in?
// no, not logged in
if(!$asaphPost->checkLogin()) {
	// $_POST content available?
	// yes
	// do login
	if(isset($_POST['login'])) {
		// try login
		// continue to post form
		if($asaphPost->login($_POST['name'], $_POST['pass'])) {
			$collections = $asaphPost->getCollections();
			include( ASAPH_PATH.'admin/templates/remote-post.html.php' );
		}
		// login failed
		// show login form
		else {
			$loginError = true;
			include( ASAPH_PATH.'admin/templates/remote-login.html.php' );
		}
	}
	// no
	// show login form
	else {
		include( ASAPH_PATH.'admin/templates/remote-login.html.php' );
	}
}
// yes, logged in
else {
	// $_POST content available?
	// yes
	// add new post
	// continue to show success
	if(isset($_POST['type'])) {
		$_public = 0;
		if(isset($_POST['public'])) {
			if($_POST['public'] == 'on') $_public = 1;
		}

		$_nsfw = 0;
		if(isset($_POST['nsfw'])) {
			if($_POST['nsfw'] == 'on') $_nsfw = 1;
		}

		switch($_POST['type']) {
			case 'link':
				$status = $asaphPost->postUrl( $_POST['url'], $_POST['title'], $_POST['description'], $_public, $_POST['collection'], $_POST['image_url'], $_POST['tags'], $_nsfw );
				break;

      case 'custom-link':
				$status = $asaphPost->postUrl( $_POST['url'], $_POST['title'], $_POST['description'], $_public, $_POST['collection'], "null", $_POST['tags'], $_nsfw ); // TODO "null" as string sucks, fix that later
				break;

			case 'image':

				if($_POST['source'] == 'upload') {
					$_POST['source'] = ASAPH_BASE_URL;
					$status = $asaphPost->postUploadedImage($_FILES['image'], $_POST['source'], $_POST['title'], $_POST['description'], $_public, $_POST['collection'], $_POST['tags'], $_nsfw);
				}
				else {
					$status = $asaphPost->postImage( $_POST['image'], $_POST['source'], $_POST['title'], $_POST['description'], $_public, $_POST['collection'], $_POST['tags'], $_nsfw);
				}
				break;

			case 'quote':
				$status = $asaphPost->postQuote( $_POST['quote'], $_POST['source'], $_POST['speaker'], $_POST['title'], $_POST['description'], $_public, $_POST['collection'], $_POST['tags'], $_nsfw);
				break;

			// case 'video':
			// 	$status = $asaphPost->postVideo( $_POST['video'], $_POST['source'], $_POST['video_type'], $_POST['width'], $_POST['height'], $_POST['thumb'], $_POST['title'], $_POST['description'], $_public );
			// 	break;
		}

		if( $status === true ) {
			include( ASAPH_PATH.'admin/templates/remote-success.html.php' );
		}
		// post failed
		// show post form again
		else {
			include( ASAPH_PATH.'admin/templates/remote-post.html.php' );
		}

	}
	// no
	// show post form
	else {
		$collections = $asaphPost->getCollections();
		include( ASAPH_PATH.'admin/templates/remote-post.html.php' );
	}
}

// shortcut function to echo request data in templates
function printReqVar( $s ) {
	if(!empty($_POST[$s])) echo $_POST[$s];
	else if(!empty($_GET[$s])) echo $_GET[$s];
}
?>
