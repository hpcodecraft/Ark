<?php ob_start(); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?= $settings['site_title'] ?></title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php echo ASAPH_LINK_PREFIX; ?>feed" />

		<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
		<link href='https://fonts.googleapis.com/css?family=Cookie|Source+Sans+Pro:400,300,200,200italic,300italic,400italic,600,600italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="<?php echo Asaph_Config::$absolutePath; ?>templates/ark/css/normalize.css">
		<link rel="stylesheet" href="<?php echo Asaph_Config::$absolutePath; ?>templates/ark/css/responsive.css">
    <link rel="shortcut icon" href="<?php echo Asaph_Config::$absolutePath; ?>templates/ark/img/favicon.ico" />
		<script src="<?php echo Asaph_Config::$absolutePath;?>templates/ark/js/vendor/modernizr-2.6.2.min.js"></script>
		<meta property="og:site_name" content="<?= $settings['site_title'] ?>"/>
		<?php if(!isset($pages)):
			$p = $posts[0];

			$x = new DOMDocument;
			if(trim($p['description'])=="") {
				$title = "";
				$text = "";
			}
			else {
				$x->loadHTML('<?xml encoding="utf-8" ?>' .$p['description']);

				$titleNode = $x->documentElement->childNodes->item(0)->childNodes->item(0);
				$title = extractText($titleNode);
				/*foreach($titleNode->childNodes as $n)
				{
					$title = $title.$x->saveXML($n);
				}
				//echo trim($title);*/
				$text = "";
				$number = $titleNode = $x->documentElement->childNodes->item(0)->childNodes->length;
				for($i=1;$i<$number;$i++)
				{
					$text = $text.extractText($x->documentElement->childNodes->item(0)->childNodes->item($i));
				}
			}
			//echo trim($text);
		?>
			<?php if( !$p['quote'] ): ?>
				<meta property="og:title" content="<?php echo trim($title); ?>" />
				<meta property="og:description" content="<?php echo trim($text); ?>" />
			<?php else: ?>
				<?php
					$x -> loadHTML('<?xml encoding="utf-8" ?>' .$p['quote']['quote']);
					$text = "";
					$number = $titleNode = $x->documentElement->childNodes->item(0)->childNodes->length;
					for($i=0;$i<$number;$i++)
					{
						$text = $text.extractText($x->documentElement->childNodes->item(0)->childNodes->item($i));
					}
				?>
				<meta property="og:title" content="Quote by <?php echo $p['quote']['speaker'] ?>" />
				<meta property="og:description" content="<?php echo $text; ?>" />
			<?php endif; ?>

			<?php if( $p['image'] ): ?>
				<meta property="og:type" content="image" />
				<meta property="og:image" content="http://<?php echo Asaph_Config::$domain; ?><?php echo $p['image']['image'];?>"/>
			<?php elseif( $p['video'] ): ?>
				<meta property="og:type" content="video"/>
				<meta property="og:video" content="<?php echo $p['video']['src'];?>"/>
				<meta property="og:video:type" content="<?php echo $p['video']['type'];?>"/>
				<meta property="og:video:width" content="<?php echo $p['video']['width'];?>"/>
				<meta property="og:video:height" content="<?php echo $p['video']['height'];?>"/>
				<meta property="og:image" content="<?php echo $p['video']['thumb'];?>"/>
			<?php elseif( $p['quote'] ) : ?>
				<meta property="og:descrition" content=" "/>
			<?php endif; ?>
		<?php endif; ?>
	</head>
	<body lang="en">
		<div class="header">
			<h1 class="logo">
        <a href="<?php echo ASAPH_LINK_PREFIX ?>">
			    <?= $settings['site_title'] ?>
			  </a>
      </h1>
      <div class="slogan">
        <?= $settings['site_slogan'] ?>
      </div>
		</div>

    <? if(count($collections) > 0): ?>
    <h2>featured collections</h2>
    <ul class="featured-collections">
      <? foreach ($collections as $c): ?>
        <li class="collection">
          <a href="<?= Asaph_Config::$absolutePath ?>collection/<?= $c['id'] ?>">
            <? $cover = $asaph->getRandomCollectionCover($c['id'])['thumb'];
              if($cover): ?>
                <div class="cover" style="background-image:url(<?= $cover ?>)"></div>
            <? else: ?>
                <div class="cover">?</div>
            <? endif; ?>
            <label class="name"><?= $c['name'] ?></label>
          </a>
        </li>
      <? endforeach; ?>
    </ul>
    <? endif; ?>


    <? if($display_mode == 'collection'): ?>
      <h2>Collection "<?= $collection_name ?>"</h2>
    <? endif; ?>

		<?=renderPagination($display_mode, $pages, $collection)?>

		<ul class="posts">
			<?php foreach( $posts as $p ): ?>
				<li class="post">
          <div class="type">
            <?php echo strtoupper($p['type']); ?>
          </div>

					<?php if($p['type'] == 'url'): ?>
						<a class="content" href="<?php echo $p['source'];?>" target="_blank">
					<?php endif; ?>

					<?php if($p['type'] == 'image'): ?>
            <div class="image-container">
						  <img src="<?php echo $p['image']['image']; ?>" />
            </div>

					<?php elseif($p['type'] == 'video'): ?>
						<embed src="<?php echo $p['video']['src']; ?>" type="<?php echo $p['video']['type'];?>" width="612" height="<?php echo ($p['video']['height']*612/$p['video']['width']);?>" />

					<?php elseif($p['type'] == 'quote'): ?>
						<quote <?php if(strlen($p['quote']['quote'])>200) echo "length=\"long\""; ?>>
							<pre><?php echo $p['quote']['quote']; ?></pre>
							<div style="font-style:normal;line-height:200%;font-size:80%;">- <?php echo $p['quote']['speaker']; ?> -</div>
						</quote>

					<?php elseif($p['type'] == 'url' && $p['image_url'] != null): ?>
						<div class="image-container">
              <img src="<?php echo $p['image_url']; ?>" />
            </div>
					<?php endif; ?>


					<?php if(strlen($p['title']) > 0): ?>
					<h3><?php echo $p['title']; ?></h3>
					<?php endif; ?>

					<?php if(strlen($p['description']) > 0): ?>
					<div class="description"><?php echo $p['description']; ?></div>
					<?php endif; ?>

					<?php if($p['type'] == 'url'): ?>
					</a>
					<?php endif; ?>

					<div class="footer">
						- posted by <a href="<?php echo Asaph_Config::$absolutePath; ?>post/<?php echo $p['id'];?>">
						<strong><?php echo $p['user'] ?></strong> on <?php echo date("m/d/Y", $p['created']); ?></a>

						<?php if($p['source']!= "") {?>
							via <a class="via" href="<?php echo $p['source'];?>"><?php echo $p['sourceDomain']; ?></a>
						<?php } ?>
					</div>
				</li>
			<?php endforeach; ?>

		</ul>

		<?=renderPagination($display_mode, $pages, $collection)?>

		<div class="imprint">
			This micro blog is powered by <a href="https://github.com/hpcodecraft/Ark">Ark</a>
    </div>

		<script src="<?php echo Asaph_Config::$absolutePath; ?>templates/ark/js/vendor/jquery-1.10.2.min.js"></script>
		<script src="<?php echo Asaph_Config::$absolutePath; ?>templates/ark/js/main.js"></script>
	</body>
</html>
<?php
	$html = ob_get_clean();
	echo $html;
?>
