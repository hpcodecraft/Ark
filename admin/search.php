<?php
define( 'ASAPH_PATH', '../' );
require_once( ASAPH_PATH.'lib/asaph_admin.class.php' );

header( 'Content-type: application/json; charset=utf-8' );

$query = $_GET['q'];

$asaphAdmin = new Asaph_Admin( Asaph_Config::$adminPostsPerPage );

$reply = array();

$reply['query'] = $query;
$reply['posts'] = $asaphAdmin->searchPosts($query);
echo json_encode($reply);
?>
