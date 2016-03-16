<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo htmlspecialchars( Asaph_Config::$title ); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo Asaph_Config::$absolutePath; ?>templates/whiteout/whiteout.css" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php echo ASAPH_LINK_PREFIX; ?>feed" />
	<link rel="Shortcut Icon" href="<?php echo Asaph_Config::$absolutePath; ?>templates/whiteout/asaph.ico" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo Asaph_Config::$absolutePath; ?>templates/whiteout/whitebox.js"></script>

</head>
<body>

<div id="title">
	<em><a href="<?php echo ASAPH_LINK_PREFIX; ?>about">about</a></em>
	<h1><a href="<?php echo Asaph_Config::$absolutePath; ?>"><?php echo htmlspecialchars( Asaph_Config::$title ); ?></a></h1>
</div>
<?php foreach( $posts as $p ) { ?>
	<div class="post">
		<?php if( $p['image'] ) { ?>
			<a href="<?php echo Asaph_Config::$absolutePath; ?>templates/whiteout/detailView.php?<?php echo $p['id'];?>" rel="whitebox" title="<?php echo $p['title']; ?>">
				<img src="<?php echo $p['image']['thumb']; ?>" alt="<?php echo $p['title']; ?>"/>
			</a>
		<?php } else if( $p['video'] ) {?>
			<div style="display:table-cell;vertical-align:middle;overflow:hidden;text-align:center;background-color:black;width:<?php echo Asaph_Config::$images['thumbWidth'];?>px;height:<?php echo Asaph_Config::$images['thumbHeight'];?>px;">
				<a href="<?php echo Asaph_Config::$absolutePath; ?>templates/whiteout/detailView.php?<?php echo $p['id'];?>" rel="whitebox" title="<?php echo $p['title']; ?>">
					<img style="max-width:<?php echo Asaph_Config::$images['thumbWidth'];?>px;max-height:<?php echo Asaph_Config::$images['thumbHeight'];?>px;" src="<?php echo $p['video']['thumb']; ?>" alt="<?php echo $p['title']; ?>"/>
					<img style="position:absolute;top:0px;left:0px;" src="<?php echo Asaph_Config::$absolutePath; ?>templates/whiteout/playbutton.svg" />
				</a>
			</div>
		<?php } else if( $p['quote'] ) {?>
			<a href="<?php echo Asaph_Config::$absolutePath; ?>templates/whiteout/detailView.php?<?php echo $p['id'];?>" rel="whitebox" title="<?php echo $p['title']; ?>">
				<div style="display:table-cell;vertical-align:middle;font-family:'Georgia',serif;font-style:italic;text-align:center;color:black;font-size:12pt;overflow:hidden;overflow:synapsis;width:<?php echo Asaph_Config::$images['thumbWidth'];?>px;height:<?php echo Asaph_Config::$images['thumbHeight'];?>px;">
					
						»<?php echo $p['quote']['quote'];?>«

					<!--<p style="font-family:'Georgia',serif;color:black;text-align:right;font-size:12pt;font-weight:bold;"><?php echo $p['quote']['speaker'];?></p>-->
				</div>
			</a>
		<?php } else { ?>
			<div style="display:table-cell;vertical-align:middle;text-align:center;font-size:14pt;width:<?php echo Asaph_Config::$images['thumbWidth'];?>px;height:<?php echo Asaph_Config::$images['thumbHeight'];?>px;">
				<a href="<?php echo $p['source']; ?>"><?php echo nl2br($p['title']); ?></a>
			</div>
		<?php } ?>
		<div class="postInfo">
			via: <a href="<?php echo $p['source']; ?>"><?php echo $p['sourceDomain']; ?></a>
		</div>
	</div>
<?php } ?>
<div class="clear"></div>

<div id="pages">
	<div class="pageInfo">
		page <?php echo $pages['current']; ?> of <?php echo $pages['total']; ?>
	</div>
	
	<div class="pageLinks">
		<?php if( $pages['prev'] ) { ?>
			<a href="<?php echo ASAPH_LINK_PREFIX.'page/'.$pages['prev']?>">&laquo; prev</a>
		<?php } else { ?>
			&laquo; prev
		<?php } ?>
		/
		<?php if( $pages['next'] ) { ?>
			<a href="<?php echo ASAPH_LINK_PREFIX.'page/'.$pages['next']?>">next &raquo;</a>
		<?php } else { ?>
			next &raquo;
		<?php } ?>
	</div>
	<div class="clear"></div>
</div>

</body>
</html>