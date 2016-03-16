<?php 
// Asaph v1.0 - www.phoboslab.org 
date_default_timezone_set("Europe/Berlin");
define( 'ASAPH_PATH', '../../' );

//show description text or not
define( 'SHOW_DESCRIPTION', 0);

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
$id = $params[0];	
$asaph = new Asaph( Asaph_Config::$postsPerPage );
$p = $asaph->getPost($id);
?>
<?php if( $p['image'] ) { ?>
	<?php if($p['image']['width']<=640){ ?>
		<img onclick = "javascript:iv.hide();" class="content" src="<?php echo $p['image']['image']; ?>" width="<?php echo $p['image']['width'];?>" height="<?php echo $p['image']['height'];?>"/>
	<?php } else { ?>
		<img onclick = "javascript:iv.hide();" class="content" src="<?php echo $p['image']['image']; ?>" width="640" height="<?php echo $p['image']['height']*640/$p['image']['width'];?>"/>
	<?php } ?>
<?php } elseif( $p['video'] ) {?>
	<?php if($p['video']['width']<=640){ ?>
		<embed src="<?php echo $p['video']['src']; ?>" type="<?php echo $p['video']['type'];?>" width="<?php echo $p['video']['width'];?>" height="<?php echo $p['video']['height'];?>"/>
	<?php } else { ?>
		<embed src="<?php echo $p['video']['src']; ?>" type="<?php echo $p['video']['type'];?>" width="640" height="<?php echo $p['video']['height']*640/$p['video']['width'];?>"/>
	<?php } ?>
<?php } elseif( $p['quote'] ) { ?>
	<div onclick="javascript:iv.hide();">
		<quote style="background-color:white;display:block;line-height:150%;width:550px;padding:25px;color:black;font-size:14pt;font-family:'Georgia',serif;font-style:italic;text-align:center;" <?php if(strlen($p['quote']['quote'])>200) echo "length=\"long\""; ?>>»<?php echo $p['quote']['quote']; ?>«</quote>			
		<p style="background-color:white;text-align:right;color:grey;padding-bottom:25px;"><?php echo $p['quote']['speaker'];?></p>
	</div>
<?php } else { ?>
	<p>
		<a href="<?php echo $p['source']; ?>"><?php echo ($p['title']); ?></a>
	</p>

<?php } ?>
<?php if(SHOW_DESCRIPTION){ ?>
<p style="width:<?php echo min(640,isset($p['quote'])*640+$p['image']['width']+$p['video']['width']);?>px;"class="description">
<h1 class="description"><?php echo nl2br($p['title']); ?></h1>
<style>
	p{
		width:<?php echo min(640,isset($p['quote'])*640+$p['image']['width']+$p['video']['width']);?>px;
		padding:0px;
	}
</style>
<?php echo nl2br($p['description']); ?>
<p style="text-align:right;width:<?php echo min(640,isset($p['quote'])*640+$p['image']['width']+$p['video']['width']);?>px;" class="description">
	via <a href="<?php echo $p['source']; ?>"><?php echo $p['sourceDomain']; ?></a>
</p>
<?php } ?>