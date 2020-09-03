<?php

/* The Asaph_Admin class extends the Asaph class to allow modification
of posts and users */

require_once( ASAPH_PATH.'lib/asaph.class.php' );

class Asaph_Admin extends Asaph {
	public $userId = null;
	protected $cookieName = 'asaphAdmin';

	public function __construct( $postsPerPage = 25 ) {
		parent::__construct( $postsPerPage );

		$this->userId = $this->getUserId();
	}

	public function updateSettings($nsfw_content_admin, $nsfw_content_website, $site_title, $site_slogan) {
		$query = "UPDATE ".ASAPH_TABLE_SETTINGS." SET setting_value='".$nsfw_content_admin."' WHERE setting_key='admin_show_nsfw_content'";
		$this->db->query($query);

		$query = "UPDATE ".ASAPH_TABLE_SETTINGS." SET setting_value='".$nsfw_content_website."' WHERE setting_key='public_page_show_nsfw_content'";
		$this->db->query($query);

    $query = "UPDATE ".ASAPH_TABLE_SETTINGS." SET setting_value='".$site_title."' WHERE setting_key='site_title'";
		$this->db->query($query);

    $query = "UPDATE ".ASAPH_TABLE_SETTINGS." SET setting_value='".$site_slogan."' WHERE setting_key='site_slogan'";
    $this->db->query($query);

		return true;
	}

	public function getPosts( $page ) {
		$this->currentPage = abs( intval($page) );

		$nsfw_query = "";
		if($this->settings['admin_show_nsfw_content'] == 0) {
			$nsfw_query = " WHERE p.nsfw=0 ";
		}

		$posts = $this->db->query(
			'SELECT SQL_CALC_FOUND_ROWS
				UNIX_TIMESTAMP(p.created) as created,
				p.id, p.type, p.public, p.collection, p.title, p.description, p.source, p.image, p.image_url, p.video, p.quote, p.nsfw, u.name AS user
			FROM
				'.ASAPH_TABLE_POSTS.' p
			LEFT JOIN '.ASAPH_TABLE_USERS.' u
				ON u.id = p.userId
			'.$nsfw_query.'
			ORDER BY
				id DESC
			LIMIT
				:1, :2',
			$this->currentPage * $this->postsPerPage,
			$this->postsPerPage
		);
		$this->totalPosts = $this->db->foundRows();

		foreach( array_keys($posts) as $i ) {
			$this->processPost( $posts[$i] );
		}

		return $posts;
	}

	public function searchPosts( $query ) {

		$nsfw_query = "";
		if($this->settings['admin_show_nsfw_content'] == 0) {
			$nsfw_query = "p.nsfw=0 AND ";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS
			UNIX_TIMESTAMP(p.created) AS created,
			p.id,
			p.type,
			p.public,
			p.collection,
			p.title,
			p.description,
			p.source,
			p.image,
			p.image_url,
			p.video,
			p.quote,
			p.nsfw,
			u.name AS user
		FROM asaph_posts p
		JOIN asaph_users u
			ON u.id = p.userId
		JOIN asaph_collections c
			ON c.id = p.collection
		JOIN asaph_posts_tags pt
			ON pt.post = p.id
		JOIN asaph_tags t
			ON t.id = pt.tag
		LEFT JOIN asaph_quotes q
			ON q.id = p.quote
		WHERE ".$nsfw_query."(p.title LIKE '%".$query."%'
			OR p.description LIKE '%".$query."%'
			OR c.name LIKE '%".$query."%'
			OR t.tag LIKE '%".$query."%'
			OR q.quote LIKE '%".$query."%'
			OR q.speaker LIKE '%".$query."%')
		GROUP BY p.id
		ORDER BY p.created DESC";

		$posts = $this->db->query($sql);

		foreach( array_keys($posts) as $i ) {
			$this->processPost( $posts[$i] );
		}

		return $posts;
	}

	public function getPostsFromCollection( $collection, $page ) {
		$this->currentPage = abs( intval($page) );

		$nsfw_query = "";
		if($this->settings['admin_show_nsfw_content'] == 0) {
			$nsfw_query = " AND p.nsfw=0 ";
		}

		$posts = $this->db->query(
			'SELECT SQL_CALC_FOUND_ROWS
				UNIX_TIMESTAMP(p.created) as created,
				p.id, p.type, p.public, p.collection, p.title, p.description, p.source, p.image, p.image_url, p.video, p.quote, p.nsfw, u.name AS user
			FROM
				'.ASAPH_TABLE_POSTS.' p
			LEFT JOIN '.ASAPH_TABLE_USERS.' u
				ON u.id = p.userId
			WHERE p.collection = :1'.$nsfw_query.'
			ORDER BY
				id DESC
			LIMIT
				:2, :3',
			$collection,
			$this->currentPage * $this->postsPerPage,
			$this->postsPerPage
		);
		$this->totalPosts = $this->db->foundRows();

		foreach( array_keys($posts) as $i ) {
			$this->processPost( $posts[$i] );
		}

		return $posts;
	}

	public function getPost( $id ) {
		$nsfw_query = "";
		if($this->settings['admin_show_nsfw_content'] == 0) {
			$nsfw_query = " AND p.nsfw=0 ";
		}

		$post = $this->db->getRow(
			'SELECT
				UNIX_TIMESTAMP(p.created) as created,
				p.id, p.type, p.public, p.collection, p.title, p.description, p.source, p.image, p.image_url, p.video, p.quote, p.nsfw, u.name AS user
			FROM
				'.ASAPH_TABLE_POSTS.' p
			LEFT JOIN '.ASAPH_TABLE_USERS.' u
				ON u.id = p.userId
			WHERE
				p.id = :1'.$nsfw_query.'
			ORDER BY
				created DESC',
			$id
		);
		if( empty($post) ) {
			return array();
		}
		$this->processPost( $post );
		return $post;
	}

	public function getCollections() {

		$nsfw_query = "";
		if($this->settings['admin_show_nsfw_content'] == 0) {
			$nsfw_query = " WHERE nsfw=0 ";
		}

		$collections = $this->db->query( 'SELECT id, name, nsfw, featured FROM '.ASAPH_TABLE_COLLECTIONS.$nsfw_query.' ORDER BY name ASC' );
		foreach( array_keys($collections) as $i ) {
			$collections[$i]['name'] = htmlspecialchars( $collections[$i]['name'] );
		}
		return $collections;
	}

	public function getCollection( $id ) {
		$collection = $this->db->getRow( 'SELECT id, name, nsfw, featured FROM '.ASAPH_TABLE_COLLECTIONS.' WHERE id=:1', $id );
		return $collection;
	}

	public function addCollection( $name ) {
		if( empty($name) ) {
			return 'collectionname-empty';
		}

		$this->db->insertRow(
			ASAPH_TABLE_COLLECTIONS,
			array(
				'name' => $name
			)
		);
		return true;
	}

	public function updateCollection( $id, $name, $nsfw, $featured ) {
		if( empty($name) ) {
			return 'collectionname-empty';
		}
		$data = array(
			'name' => $name,
			'nsfw' => $nsfw,
      'featured' => $featured,
		);

		$this->db->updateRow(
			ASAPH_TABLE_COLLECTIONS,
			array( 'id' => $id ),
			$data
		);

		$this->db->query( 'UPDATE '.ASAPH_TABLE_POSTS.' SET nsfw='.$nsfw.' WHERE collection = :1', $id );

		return true;
	}

	public function deleteCollection( $id ) {
		$this->db->query( 'UPDATE '.ASAPH_TABLE_POSTS.' SET collection = 0 WHERE collection = :1', $id );
		$this->db->query( 'DELETE FROM '.ASAPH_TABLE_COLLECTIONS.' WHERE id = :1', $id );
		return true;
	}


	public function checkLogin() {
		return !empty( $this->userId );
	}


	public function login( $name, $pass ) {
		$user = $this->db->getRow(
			'SELECT id FROM '.ASAPH_TABLE_USERS.' WHERE name = :1 AND pass = :2',
			$name, md5( $pass )
		);

		if( empty($user) ) {
			return false;
		}

		$this->userId = $user['id'];
		$loginId = md5(uniqid(rand()));
		setcookie( $this->cookieName, $loginId, time() + 3600 * 24 * 365 );

		$this->db->updateRow(
			ASAPH_TABLE_USERS,
			array( 'id' => $this->userId ),
			array( 'loginId' => $loginId )
		);

		return true;
	}


	public function logout() {
		if( empty($_COOKIE[$this->cookieName]) ) {
			return false;
		}

		$this->db->updateRow(
			ASAPH_TABLE_USERS,
			array('loginId' => $_COOKIE[$this->cookieName] ),
			array('loginId' => '')
		);
		$this->userId = null;

		return true;
	}


	protected function getUserId() {
		if( empty($_COOKIE[$this->cookieName]) ) {
			return null;
		}

		$user = $this->db->getRow(
			'SELECT id FROM '.ASAPH_TABLE_USERS.' WHERE loginId = :1',
			$_COOKIE[$this->cookieName]
		);

		return empty( $user ) ? null : $user['id'];
	}

	public function updateQuote( $id, $quote, $speaker ) {
		$data = array('quote' => $quote, 'speaker' => $speaker);

		$this->db->updateRow(
			ASAPH_TABLE_QUOTES,
			array( 'id' => $id ),
			$data
		);

		return true;
	}

	public function updatePost( $id, $title, $description, $public, $collection, $tags, $nsfw ) {
		$data = array(
			'title' => $title,
			'description' => $description,
			'public' => $public,
			'nsfw' => $nsfw,
			'collection' => $collection
		);

		$this->db->updateRow(
			ASAPH_TABLE_POSTS,
			array( 'id' => $id ),
			$data
		);


		// Trim tags
		$tags = explode(',', $tags);
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

		// Delete old tag connections from cross table^
		$this->db->query('DELETE FROM '.ASAPH_TABLE_POSTS_TAGS.' WHERE post = :1', $id);

		// Write new tag connections to cross table
		foreach ($all_tags as $at) {
			$this->db->insertRow(
				ASAPH_TABLE_POSTS_TAGS,
				array('post' => $id, 'tag' => $at['id'])
			);
		}

		return true;

		// Valid date given (YYYY-MM-DD)?
		/*
		if(
			preg_match('/^\d{4}.\d{2}.\d{2}.+\d{2}.\d{2}$/', $created) &&
			strtotime($created)
		) {
			$data['created'] = $created;

			$initial = $this->getPost($id);

			// OK, this sucks hard. If the date changed, we may have to move the thumb and image
			// into another path and make sure to not overwrite any other imagess.
			$initialPath = date( 'Y/m', $initial['created'] );
			$newPath = date( 'Y/m', strtotime($created) );
			if( $initialPath != $newPath && !empty($initial['image']) ) {
				$newImageDir = ASAPH_PATH.Asaph_Config::$images['imagePath'].$newPath;
				$newThumbDir = ASAPH_PATH.Asaph_Config::$images['thumbPath'].$newPath;
				$newImageName = $this->getUniqueFileName( $newImageDir, basename($initial['image']['image'] ));
				$newThumbName = $this->getUniqueFileName( $newThumbDir, basename($initial['image']['thumb'] ));

				$initialImagePath = ASAPH_PATH.Asaph_Config::$images['imagePath'].$initialPath.'/'.basename($initial['image']['image']);
				$initialThumbPath = ASAPH_PATH.Asaph_Config::$images['thumbPath'].$initialPath.'/'.basename($initial['image']['thumb']);
				$newImagePath = $newImageDir.'/'.$newImageName;
				$newThumbPath = $newThumbDir.'/'.$newThumbName;

				echo $newImageDir."<br/>";
				echo $newImageName."<br/>";
				echo $initialImagePath."<br/>";
				echo $newImagePath."<br/>";

				$imageData = array();
				$imageData ['image'] = $newImageName;
				$imageData ['thumb'] = $newThumbName;

				$this->db->updateRow(
							ASAPH_TABLE_IMAGES,
							array( 'id' => $initial['image']['id'] ),
							$imageData
						);

				if(
					!$this->mkdirr($newImageDir) ||
					!$this->mkdirr($newThumbDir) ||
					!@rename($initialImagePath, $newImagePath) ||
					!@rename($initialThumbPath, $newThumbPath)
				) {
					return false;
				}
			}
		}
		*/
	}


	public function deletePost( $id ) {
		$post = $this->getPost($id);

		// Delete thumbnail and image from disk
		if( !empty($post['image']) ) {
			@unlink($post['image']['image']);
			@unlink($post['image']['thumb']);
			$this->db->query( 'DELETE FROM '.ASAPH_TABLE_IMAGES.' WHERE id = :1', $post['image']['id']);
		}
		if( !empty($post['video']) ) {
			$this->db->query( 'DELETE FROM '.ASAPH_TABLE_VIDEOS.' WHERE id = :1', $post['video']['id']);
		}
		if( !empty($post['quote']) ) {
			$this->db->query( 'DELETE FROM '.ASAPH_TABLE_QUOTES.' WHERE id = :1', $post['quote']['id']);
		}
		$this->db->query( 'DELETE FROM '.ASAPH_TABLE_POSTS.' WHERE id = :1', $id );
		return true;
	}

  public function deletePreviewImage($id) {
    $this->db->query( 'UPDATE '.ASAPH_TABLE_POSTS.' SET image_url=NULL WHERE id = :1', $id);
    return true;
  }

	public function getUser( $id ) {
		$user = $this->db->getRow( 'SELECT id, name FROM '.ASAPH_TABLE_USERS.' WHERE id = :1', $id );
		$user['name'] = htmlspecialchars( $user['name'] );
		return $user;
	}


	public function updateUser( $id, $name, $pass, $pass2 ) {
		if( empty($name) ) {
			return 'username-empty';
		}

		$userData = array(
			'name' => $name,
			//'loginId' => ''
		);

		if( !empty($pass) ) {
			if( $pass != $pass2 ) {
				return 'passwords-not-equal';
			}
			$userData['pass'] = md5($pass);
		}

		$this->db->updateRow(
			ASAPH_TABLE_USERS,
			array( 'id' => $id ),
			$userData
		);

		return true;
	}

	protected function getUniqueFileName( $directory, $initialName ) {
		$newName = $initialName;
		$path = $directory .'/'. $initialName;

		// Do we already have a file with this name -> Add a numerical prefix
		for( $i = 1; file_exists($path); $i++ ) {
			$newName = $i . '-' . $initialName;
			$path = $directory .'/'. $newName;
		}

		return $newName;
	}


	protected function mkdirr( $pathname ) {
		if( empty($pathname) || is_dir($pathname) ) {
			return true;
		}
		if ( is_file($pathname) ) {
			return false;
		}

		$nextPathname = substr( $pathname, 0, strrpos( $pathname, '/' ) );
		if( $this->mkdirr( $nextPathname ) ) {
			if( !file_exists( $pathname ) ) {
				$oldUmask = umask(0);
				$success = @mkdir( $pathname, Asaph_Config::$defaultChmod );
				umask( $oldUmask );
				return $success;
			}
		}
		return false;
	}
}

?>
