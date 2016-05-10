<?php

/* The Asaph class hosts all functions to select and process posts for
the front page.

To integrate Asaph within other systems, just define your ASAPH_PATH
and include this file. You can then create a new Asaph object and fetch
the newest $numberOfPosts posts to an array.

$asaph = new Asaph( $numberOfPosts );
$asaphPosts = $asaph->getPosts( $pageToFetch ); */

require_once( ASAPH_PATH.'config/'.$_SERVER['SERVER_NAME'].'.php' );
require_once( ASAPH_PATH.'lib/db.class.php' );
date_default_timezone_set("Europe/Berlin");


class Asaph {
	protected $db = null;
	protected $postsPerPage = 0;
	protected $currentPage = 0;
	protected $settings = null;

	public function __construct( $postsPerPage = 25 ) {
		$this->postsPerPage = $postsPerPage;
		$this->db = new DB(
			Asaph_Config::$db['host'],
			Asaph_Config::$db['database'],
			Asaph_Config::$db['user'],
			Asaph_Config::$db['password']
		);

		$this->getSettings();
	}

	public function getSettings() {
		$raw_settings = $this->db->query('SELECT * FROM '.ASAPH_TABLE_SETTINGS);
		$settings = array();

		foreach($raw_settings as $rs) {
			$settings[$rs['setting_key']] = (int)$rs['setting_value'];
		}

		$this->settings = $settings;
		return $this->settings;
	}

	public function getPosts( $page ) {
		$this->currentPage = abs( intval($page) );

		$nsfw_query = "";
		if($this->settings['public_page_show_nsfw_content'] == 0) {
			$nsfw_query = " AND p.nsfw=0 ";
		}

		$posts = $this->db->query(
			'SELECT SQL_CALC_FOUND_ROWS
				UNIX_TIMESTAMP(p.created) as created,
				p.id, p.type, p.source, p.description, p.image, p.image_url, p.video, p.quote, p.title, p.public, p.nsfw, u.name AS user
			FROM
				'.ASAPH_TABLE_POSTS.' p
			LEFT JOIN '.ASAPH_TABLE_USERS.' u
				ON u.id = p.userId
			WHERE p.public=1'.$nsfw_query.'
			ORDER BY
				created DESC
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

	public function getPost( $id ) {
		$nsfw_query = "";
		if($this->settings['public_page_show_nsfw_content'] == 0) {
			$nsfw_query = " AND p.nsfw=0 ";
		}

		$post = $this->db->getRow(
			'SELECT
				UNIX_TIMESTAMP(p.created) as created,
				p.id, p.type, p.source, p.description, p.image, p.image_url, p.title, p.video, p.quote, p.public, p.nsfw, u.name AS user
			FROM
				'.ASAPH_TABLE_POSTS.' p
			LEFT JOIN '.ASAPH_TABLE_USERS.' u
				ON u.id = p.userId
			WHERE
				p.public = 1'.$nsfw_query.'
			AND
				p.id = :1',
			$id
		);
		if( empty($post) ) {
			return array();
		}
		$this->processPost( $post );
		return $post;
	}

	public function getPages() {
		$pages = array(
			'current' => 1,
			'total' => 1,
			'prev' => false,
			'next' => false,
		);
		if( $this->totalPosts > 0 ) {
			$pages['current'] = $this->currentPage + 1;
			$pages['total'] = ceil($this->totalPosts / $this->postsPerPage );
			if( $this->currentPage > 0 ) {
				$pages['prev'] = $this->currentPage;
			}
			if( $this->totalPosts > $this->postsPerPage * $this->currentPage + $this->postsPerPage ) {
				$pages['next'] = $this->currentPage + 2;
			}
		}

		return $pages;
	}

  public function getFeaturedCollections() {
    $collections = $this->db->query(
			'SELECT SQL_CALC_FOUND_ROWS id, name
			FROM
				'.ASAPH_TABLE_COLLECTIONS.'
			WHERE featured=1'.$nsfw_query.'
			ORDER BY name ASC'
		);

    return $collections;
  }

  public function getRandomCollectionCover($collection_id) {
    $post = $this->db->query(
      'SELECT UNIX_TIMESTAMP(p.created) as created,
      p.id FROM '.ASAPH_TABLE_POSTS.' p
      WHERE p.collection = :1 AND p.image IS NOT NULL
      ORDER BY RAND() LIMIT 1',
      $collection_id
    );

    if($post[0] && $post[0]['id']) {
      $datePath = date( 'Y/m/', $post[0]['created'] );
      return $this->queryImage($post[0]['id'], $datePath);
    }

    return false;
  }

	protected function queryImage($image,$datePath)
	{
		$query = 'SELECT SQL_CALC_FOUND_ROWS
				id, image, thumb, width, height
			FROM
				'.ASAPH_TABLE_IMAGES.'
			WHERE
				id = '.$image.';';
		$img = $this->db->query($query);
		$img = $img[0];
		$img['thumb'] =
				Asaph_Config::$absolutePath
				.Asaph_Config::$images['thumbPath']
				.$datePath
				.$img['thumb'];

		$img['image'] =
				Asaph_Config::$absolutePath
				.Asaph_Config::$images['imagePath']
				.$datePath
				.$img['image'];
		return $img;
	}

	protected function queryVideo($video)
	{
		$query = 'SELECT SQL_CALC_FOUND_ROWS
				id, src, width, height, type, thumb
			FROM
				'.ASAPH_TABLE_VIDEOS.'
			WHERE
				id = '.$video.';';
		$img = $this->db->query($query);
		$img = $img[0];
		return $img;
	}

	protected function queryQuote($quote)
	{
		$query = 'SELECT SQL_CALC_FOUND_ROWS
				id, quote,speaker
			FROM
				'.ASAPH_TABLE_QUOTES.'
			WHERE
				id = '.$quote.';';
		$img = $this->db->query($query);
		$img = $img[0];
		return $img;
	}

	protected function processPost( &$post ) {
		$urlParts = parse_url( $post['source'] );
		$datePath = date( 'Y/m/', $post['created'] );
		$post['sourceDomain'] = $urlParts['host'];
		$post['source'] = htmlspecialchars( $post['source'] );
		$post['title'] = htmlspecialchars( $post['title'] );
		$post['description'] = $post['description'];

		if( $post['image']) {
			$post['image'] = $this->queryImage($post['image'],$datePath);
		}

		if( $post['video']) {
			$post['video'] = $this->queryVideo($post['video']);
		}

		if( $post['quote']) {
			$post['quote'] = $this->queryQuote($post['quote']);
		}

		$post['tags'] = $this->db->query(
			'SELECT pt.tag AS id, t.tag FROM asaph_posts_tags pt LEFT JOIN asaph_tags t ON t.id = pt.tag WHERE pt.post = :1',
			$post['id']
		);
	}
}

?>
