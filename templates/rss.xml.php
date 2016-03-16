<?php 
	header('Content-Type: application/xml; charset=utf-8'); 
	echo '<?xml version="1.0" encoding="utf-8"?>';
	echo "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
	<atom:link href="http://<?php echo Asaph_Config::$domain.ASAPH_LINK_PREFIX ?>feed" rel="self" type="application/rss+xml" />
	<title><?php echo htmlspecialchars( Asaph_Config::$title ); ?></title>
	<link><?php echo ASAPH_BASE_URL; ?></link>
	<description><?php echo htmlspecialchars( Asaph_Config::$title ); ?></description>
	<language>en</language>
	
	<?php foreach( $posts as $p ) { ?>
		<item>
			<title><?php echo $p['title']; ?></title>
			<link><?php echo $p['source']; ?></link>
			<description>
				<?php echo "<![CDATA[";?>
				<?php if( $p['image'] ): ?>
						<a href="http://<?php echo Asaph_Config::$domain.$p['image']['image']; ?>">
						<img src="http://<?php echo Asaph_Config::$domain.$p['image']['thumb']; ?>"; alt="">
					</a>
				<?php elseif( $p['video'] ): ?>
					<embed src="<?php echo $p['video']['src']; ?>" type="<?php echo $p['video']['type'];?>" width="612" height="<?php echo ($p['video']['height']*612/$p['video']['width']);?>" />
				<?php elseif( $p['quote'] ) : ?>
					<quote <?php if(strlen($p['quote']['quote'])>200) echo "length=\"long\""; ?>>
						»<?php echo $p['quote']['quote']; ?>«
						<div style="font-style:normal;line-height:200%;font-size:80%;">- <?php echo $p['quote']['speaker']; ?> -</div>
					</quote>          
				<?php endif; ?>
				
				<?php echo $p['description']; ?>
				<?php echo "]]>";?>
			</description>
			<pubDate><?php echo date('r', $p['created']); ?></pubDate>
			<guid isPermaLink="false"><?php echo ASAPH_BASE_URL.$p['id']; ?></guid>
		</item>
	<?php } ?>

</channel>
</rss>
