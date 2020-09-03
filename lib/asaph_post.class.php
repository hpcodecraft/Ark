<?php

/* The Asaph_Post class extends the Asaph_Admin class to allow creation
of new post. It is solely used from the bookmarklet */

require_once( ASAPH_PATH.'lib/asaph_admin.class.php' );
date_default_timezone_set("Europe/Berlin");

class Asaph_Post extends Asaph_Admin {

	public function __construct() {
		parent::__construct();
	}


	public function postUrl( $url, $title, $desc, $public, $collection, $image, $tag_string, $nsfw ) {
		if( !$this->userId ) {
			return 'not-logged-in';
		}

		$this->db->insertRow( ASAPH_TABLE_POSTS, array(
			'userId' => $this->userId,
			'hash' => md5( $url ),
			'created' => date( 'Y-m-d H:i:s' ),
			'type' => 'url',
			'public' => $public,
			'nsfw' => $nsfw,
			'collection' => $collection,
			'source' => $url,
			'title' => trim($title),
			'description' => $desc,
			'image_url' => $image,
		));

		$id = mysqli_insert_id();
		$this->saveTags($id, $tag_string);

		return true;
	}


	public function postImage( $url, $source, $title, $description, $public, $collection, $tag_string, $nsfw ) {
		if( !$this->userId ) {
			return 'not-logged-in';
		}

		// Determine the target path based on the current date (e.g. data/2008/04/)
		$time = time();
		$imageDir = ASAPH_PATH.Asaph_Config::$images['imagePath'] . date('Y/m', $time);
		$thumbDir = ASAPH_PATH.Asaph_Config::$images['thumbPath'] . date('Y/m', $time);

		// Extract the image name from the url, remove all special characters from it
		// and determine the local file name
		$imageName = strtolower( substr(strrchr( $url, '/'), 1) );
		$imageName = preg_replace( '/[^a-zA-Z\d\.]+/', '-', $imageName );
		$imageName = preg_replace( '/^\-+|\-+$/', '', $imageName );
		if( !preg_match('/\.(png|gif|jpg|jpeg)$/i', $imageName) ) {
			$imageName .= '.jpg';
		}
		$thumbName = substr( $imageName, 0, strrpos($imageName, '.') ) . '.jpg';

		$imageName = $this->getUniqueFileName( $imageDir, $imageName );
		$thumbName = $this->getUniqueFileName( $thumbDir, $thumbName );
		$imagePath = $imageDir .'/'. $imageName;
		$thumbPath = $thumbDir .'/'. $thumbName;


		// Create target directories and download the image
		if(
			!$this->mkdirr($imageDir) ||
			!$this->mkdirr($thumbDir) ||
			!$this->download($url, $referer, $imagePath)
		) {
			return 'download-failed';
		}


		// Was this image already posted
		$imageHash = md5_file( $imagePath );
		$c = $this->db->query('SELECT id FROM '.ASAPH_TABLE_POSTS.' WHERE hash = :1', $imageHash);
		if( !empty( $c ) ) {
			unlink( $imagePath );
			return 'duplicate-image';
		}


		// Create the thumbnail and insert post to the db
		if(
			!$this->createThumb(
				$imagePath, $thumbPath,
				Asaph_Config::$images['thumbWidth'], Asaph_Config::$images['thumbHeight'],
				Asaph_Config::$images['jpegQuality']
			)
		) {
			return 'thumbnail-failed';
		}
		list( $srcWidth, $srcHeight, $type ) = getimagesize( $imagePath );

		$this->db->insertRow( ASAPH_TABLE_IMAGES, array(
			'thumb' => $thumbName,
			'image' => $imageName,
			'width' => $srcWidth,
			'height' =>  $srcHeight
		));

		$this->db->insertRow( ASAPH_TABLE_POSTS, array(
			'userId' => $this->userId,
			'hash' => $imageHash,
			'created' => date('Y-m-d H:i:s'),
			'type' => 'image',
			'public' => $public,
			'nsfw' => $nsfw,
			'collection' => $collection,
			'source' => $source,
			'title' => $title,
			'description' => $description,
			'image' => $this->db->insertId()
		));

		$id = mysqli_insert_id();
		$this->saveTags($id, $tag_string);

		return true;
	}

	public function postUploadedImage($image, $source, $title, $description, $public, $collection, $tag_string, $nsfw) {
		if( !$this->userId ) {
			return 'not-logged-in';
		}

		// Determine the target path based on the current date (e.g. data/2008/04/)
		$time = time();
		$imageDir = ASAPH_PATH.Asaph_Config::$images['imagePath'] . date('Y/m', $time);
		$thumbDir = ASAPH_PATH.Asaph_Config::$images['thumbPath'] . date('Y/m', $time);

		// Extract the image name from the url, remove all special characters from it
		// and determine the local file name
		$imageName = strtolower( basename($image['name'] ));
		$imageName = preg_replace( '/[^a-zA-Z\d\.]+/', '-', $imageName );
		$imageName = preg_replace( '/^\-+|\-+$/', '', $imageName );
		if( !preg_match('/\.(png|gif|jpg|jpeg)$/i', $imageName) ) {
			$imageName .= '.jpg';
		}
		$thumbName = substr( $imageName, 0, strrpos($imageName, '.') ) . '.jpg';

		$imageName = $this->getUniqueFileName( $imageDir, $imageName );
		$thumbName = $this->getUniqueFileName( $thumbDir, $thumbName );
		$imagePath = $imageDir .'/'. $imageName;
		$thumbPath = $thumbDir .'/'. $thumbName;

		// Create target directories and download the image
		if(
			!$this->mkdirr($imageDir) ||
			!$this->mkdirr($thumbDir) ||
			!$this->copyUploadedImage($image, $imagePath)
		) {
			return 'upload-failed';
		}


		// Was this image already posted
		$imageHash = md5_file( $imagePath );
		$c = $this->db->query('SELECT id FROM '.ASAPH_TABLE_POSTS.' WHERE hash = :1', $imageHash);
		if( !empty( $c ) ) {
			unlink( $imagePath );
			return 'duplicate-image';
		}


		// Create the thumbnail and insert post to the db
		if(
			!$this->createThumb(
				$imagePath, $thumbPath,
				Asaph_Config::$images['thumbWidth'], Asaph_Config::$images['thumbHeight'],
				Asaph_Config::$images['jpegQuality']
			)
		) {
			return 'thumbnail-failed';
		}
		list( $srcWidth, $srcHeight, $type ) = getimagesize( $imagePath );

		$this->db->insertRow( ASAPH_TABLE_IMAGES, array(
			'thumb' => $thumbName,
			'image' => $imageName,
			'width' => $srcWidth,
			'height' =>  $srcHeight
		));

		$this->db->insertRow( ASAPH_TABLE_POSTS, array(
			'userId' => $this->userId,
			'hash' => $imageHash,
			'created' => date('Y-m-d H:i:s'),
			'type' => 'image',
			'public' => $public,
			'nsfw' => $nsfw,
			'collection' => $collection,
			'source' => $source,
			'title' => $title,
			'description' => $description,
			'image' => $this->db->insertId()
		));

		$id = mysqli_insert_id();
		$this->saveTags($id, $tag_string);

		return true;
	}

	/*
	public function postVideo( $url, $source, $type, $width, $height, $thumb, $title, $description ) {
		if( !$this->userId ) {
			return 'not-logged-in';
		}

		// Determine the target path based on the current date (e.g. data/2008/04/)
		$time = time();
		$this->db->insertRow( ASAPH_TABLE_VIDEOS, array(
			'src' => $url,
			'width' => $width,
			'height' =>  $height,
			'thumb' => $thumb,
			'type' => $type
		));

		$this->db->insertRow( ASAPH_TABLE_POSTS, array(
			'userId' => $this->userId,
			'hash' => md5($url),
			'created' => date( 'Y-m-d H:i:s', $time ),
			'type' => 'video',
			'source' => $source,
			'title' => $title,
			'description' => $description,
			'video' => $this->db->insertId()
		));

		return true;
	}
	*/

	public function postImageOnline( $url, $src, $type, $width, $height, $title, $description ) {
		if( !$this->userId ) {
			return 'not-logged-in';
		}

		// Determine the target path based on the current date (e.g. data/2008/04/)
		$time = time();
		$this->db->insertRow( ASAPH_TABLE_IMAGES, array(
			'thumb' => $url,
			'image' => $url,
			'width' => $width,
			'height' =>  $height,
		));

		$this->db->insertRow( ASAPH_TABLE_POSTS, array(
			'userId' => $this->userId,
			'hash' => md5($url),
			'created' => date( 'Y-m-d H:i:s', $time ),
			'type' => 'image',
			'source' => $src,
			'title' => $title,
			'description' => $description,
			'video' => $this->db->insertId()
		));

		return true;
	}

	public function postQuote( $quote, $src,$speaker, $title, $description, $public, $collection, $tag_string, $nsfw ) {
		if( !$this->userId ) {
			return 'not-logged-in';
		}

		// Determine the target path based on the current date (e.g. data/2008/04/)
		$time = time();
		$this->db->insertRow( ASAPH_TABLE_QUOTES, array(
			'quote' => $quote,
			'speaker' => $speaker,
		));

		$this->db->insertRow( ASAPH_TABLE_POSTS, array(
			'userId' => $this->userId,
			'hash' => md5($quote.$speaker),
			'created' => date( 'Y-m-d H:i:s', $time ),
			'type' => 'quote',
			'public' => $public,
			'nsfw' => $nsfw,
			'collection' => $collection,
			'source' => $src,
			'title' => $title,
			'description' => $description,
			'quote' => $this->db->insertId()
		));

		$id = mysqli_insert_id();
		$this->saveTags($id, $tag_string);

		return true;
	}

	private function saveTags( $post_id, $tag_string ) {

		// Trim tags
		$tags = explode(',', $tag_string);
		foreach($tags as &$tag) {
			$tag = strtolower(trim($tag));
		}

		// Create escaped text
		$tags_escaped = [];
		foreach($tags as $t) {
			array_push($tags_escaped, "'".$t."'");
		}

		// Get existing tags from database
		$found_tags = $this->db->query(
			'SELECT id, tag FROM '.ASAPH_TABLE_TAGS.' WHERE tag IN ('.implode(',', $tags_escaped).')'
		);

		// Create flat array from existing tags
		$found_tags_flat = [];
		foreach ($found_tags as $ft) {
			array_push($found_tags_flat, $ft['tag']);
		}

		// Diff it against all tags to find the new ones
		if(count($found_tags_flat) == 0) $new_tags = $tags;
		else $new_tags = array_diff($tags, $found_tags_flat);

		// Write new tags to database
		$all_tags = $found_tags;
		foreach ($new_tags as $nt) {
			$this->db->insertRow(
				ASAPH_TABLE_TAGS,
				array('tag' => $nt)
			);

			$new_id = mysqli_insert_id();
			array_push($all_tags, array('id' => $new_id, 'tag' => $nt));
		}

		// Delete old tag connections from cross table
		//$this->db->query('DELETE FROM '.ASAPH_TABLE_POSTS_TAGS.' WHERE post = :1', $id);

		// Write new tag connections to cross table
		foreach ($all_tags as $at) {
			$this->db->insertRow(
				ASAPH_TABLE_POSTS_TAGS,
				array('post' => $post_id, 'tag' => $at['id'])
			);
		}
	}


	private function copyUploadedImage($image, $targetFile) {
		// Check if image file is a actual image or fake image
    $check = getimagesize($image["tmp_name"]);
    if($check !== false) $uploadOk = 1;
    else $uploadOk = 0;

		if ($uploadOk == 0) return false;
		else return move_uploaded_file($image["tmp_name"], $targetFile);
	}

	private function download( $url, $referer, $target  ) {
		// Open the target file for writing
		$fpLocal = @fopen( $target, 'w' );
		if( !$fpLocal ) {
			return false;
		}


		// Use cURL to download if available
		if( is_callable('curl_init') ) {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_REFERER, $referer );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_FILE, $fpLocal );
			if( !curl_exec($ch) ) {
				fclose( $fpLocal );
				curl_close( $ch );
				return false;
			}
			curl_close( $ch );
		}
		// Otherwise use fopen
		else {
			$opts = array(
				'http' => array(
					'method' => "GET",
					'header' => "Referer: $referer\r\n"
				)
			);

			$context = stream_context_create( $opts );
			$fpRemote = @fopen( $url, 'r', false, $context );
			if( !$fpRemote ) {
				fclose( $fpLocal );
				return false;
			}

			while( !feof( $fpRemote ) ) {
				fwrite( $fpLocal, fread($fpRemote, 8192) );
			}
			fclose( $fpRemote );
		}

		fclose( $fpLocal );
		return true;
	}


	private function createThumb( $imgPath, $thumbPath, $thumbWidth, $thumbHeight, $quality ) {
		// Get image type and size and check if we can handle it
		list( $srcWidth, $srcHeight, $type ) = getimagesize( $imgPath );
		if($srcWidth < 1 || $srcHeight < 1) return false;

		switch( $type ) {
			case IMAGETYPE_JPEG: $imgCreate = 'ImageCreateFromJPEG'; break;
			case IMAGETYPE_GIF: $imgCreate = 'ImageCreateFromGIF'; break;
			case IMAGETYPE_PNG: $imgCreate = 'ImageCreateFromPNG'; break;
			default: return false;
		}

		// Crop the image horizontal or vertical
		$srcX = 0;
		$srcY = 0;
		if( ( $srcWidth/$srcHeight ) > ( $thumbWidth/$thumbHeight ) ) {
			$zoom = ($srcWidth/$srcHeight) / ($thumbWidth/$thumbHeight);
			$srcX = ($srcWidth - $srcWidth / $zoom) / 2;
			$srcWidth = $srcWidth / $zoom;
		}
		else {
			$zoom = ($thumbWidth/$thumbHeight) / ($srcWidth/$srcHeight);
			$srcY = ($srcHeight - $srcHeight / $zoom) / 2;
			$srcHeight = $srcHeight / $zoom;
		}

		// Resample and create the thumbnail
		$thumb = imageCreateTrueColor( $thumbWidth, $thumbHeight );
		$orig = $imgCreate( $imgPath );
		imageCopyResampled( $thumb, $orig, 0, 0, $srcX, $srcY, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight );
		imagejpeg( $thumb, $thumbPath, $quality );

		imageDestroy( $thumb );
		imageDestroy( $orig );
		return true;
	}
}

?>
