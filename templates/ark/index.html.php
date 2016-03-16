<?php
//dl("tidy.so");
ob_start();

function renderPagination($pages) {
	$html = '';

	if(isset($pages)) {
		$html.= '<div class="navigation">';

		if( $pages['prev']) {
			$html.= '<a href="'.ASAPH_LINK_PREFIX.'page/'.$pages['prev'].'" class="pageleft">«</a>';
		}
		else {
			$html.= '<a href="" style="visibility:hidden" class="pageleft">«</a>';
		}

		$html.= '<div class="all-pages">';

		for($i=1; $i<=$pages['total']; $i++) {
			if($i == $pages['current']) {
				$html.= '<a class="active" href="'.ASAPH_LINK_PREFIX.'page/'.$i.'">'.$i.'</a> ';
			}
			else {
				$html.= '<a href="'.ASAPH_LINK_PREFIX.'page/'.$i.'">'.$i.'</a> ';
			}
		}

		$html.= '</div>
		<a href="#" class="jump-to-page">jump</a>';

		if( $pages['next']) {
				$html.= '<a href="'.ASAPH_LINK_PREFIX.'page/'.$pages['next'].'" class="pageright">»</a>';
		}
		else {
				$html.= '<a href="" style="visibility:hidden" class="pageright">»</a>';
		}

		$html.= '</div>';
	}

	return $html;
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Ark</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php echo ASAPH_LINK_PREFIX; ?>feed" />

		<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
		<link href='https://fonts.googleapis.com/css?family=Cookie|Source+Sans+Pro:400,300,200,200italic,300italic,400italic,600,600italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="<?php echo Asaph_Config::$absolutePath; ?>templates/ark/css/normalize.css">
		<link rel="stylesheet" href="<?php echo Asaph_Config::$absolutePath; ?>templates/ark/css/responsive.css">
		<script src="<?php echo Asaph_Config::$absolutePath;?>templates/ark/js/vendor/modernizr-2.6.2.min.js"></script>


		<meta property="og:site_name" content="ark.hpcodecraft.me"/>
		<?php if(!isset($pages)):
			$p = $posts[0];


			function extractText($node)
			{
				return $node->textContent;
				if($node->nodeType == XML_TEXT_NODE)
					return $node->textContent;
				else if($node->nodeType == XML_ELEMENT_NODE)
				{

					$text = "";
					foreach($node->childNodes as $n)
					{
						$text=$text.extractText($n);
					}
					if($node->nodeName == "p"){
						$text = $text." \n";
					}
					if($node->nodeName == "br"){
						$text = $text." \n";
					}
					return $text;
				}
				else
					return "";

			}

			$x = new DOMDocument;
			if(trim($p['description'])=="")
			{
				$title = "";
				$text = "";
			}
			else
			{
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
		<!--[if lt IE 7]>
			<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
		<![endif]-->

		<div class="header">
			<h1><a href="<?php echo ASAPH_LINK_PREFIX ?>">Ark</a></h1>
		</div>

		<?=renderPagination($pages)?>

		<ul class="posts">
			<?php foreach( $posts as $p ): ?>
				<li>

					<?php if($p['type'] == 'url'): ?>
						<a class="content" href="<?php echo $p['source'];?>" target="_blank">
					<?php endif; ?>

					<?php if($p['type'] == 'image'): ?>
						<img class="content" src="<?php echo $p['image']['image']; ?>" />

					<?php elseif($p['type'] == 'video'): ?>
						<embed src="<?php echo $p['video']['src']; ?>" type="<?php echo $p['video']['type'];?>" width="612" height="<?php echo ($p['video']['height']*612/$p['video']['width']);?>" />

					<?php elseif($p['type'] == 'quote'): ?>
						<quote <?php if(strlen($p['quote']['quote'])>200) echo "length=\"long\""; ?>>
							<pre><?php echo $p['quote']['quote']; ?></pre>
							<div style="font-style:normal;line-height:200%;font-size:80%;">- <?php echo $p['quote']['speaker']; ?> -</div>
						</quote>

					<?php elseif($p['type'] == 'url' && $p['image_url'] != 'null'): ?>
						<img src="<?php echo $p['image_url']; ?>" />
					<?php endif; ?>


					<?php if(strlen($p['title']) > 0): ?>
					<h2><?php echo $p['title']; ?></h2>
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

		<?=renderPagination($pages)?>

		<div class="imprint">
			This micro blog is powered by <a href="https://github.com/hpcodecraft/Ark">Ark</a>
    </div>

		<script src="<?php echo Asaph_Config::$absolutePath; ?>templates/ark/js/vendor/jquery-1.10.2.min.js"></script>
		<script src="<?php echo Asaph_Config::$absolutePath; ?>templates/ark/js/main.js"></script>
	</body>
</html>
<?php
	$html = ob_get_clean();
	/*$x = new DOMDocument;
	$x->loadHTML($html);
	$clean = $x->saveXML();
	//echo $html;
	echo $clean;*/
	// Specify configuration
	$config = array('indent' => true, 'new-blocklevel-tags' => 'quote', 'vertical-space' => false, 'wrap' => 0);

	// Tidy
	// $tidy = new tidy;
	// $tidy->parseString($html, $config, 'utf8');
	// $tidy->cleanRepair();

	// Output
	echo $html;
?>
