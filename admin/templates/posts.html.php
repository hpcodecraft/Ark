<?php include(ASAPH_PATH.'admin/templates/head.html.php'); ?>
<?php include(ASAPH_PATH.'admin/templates/partial-collections.html.php'); ?>
<?php // include(ASAPH_PATH.'admin/templates/partial-inspector.html.php'); ?>
<?php include(ASAPH_PATH.'admin/templates/partial-dialog.html.php'); ?>

<div id="filters">
	<button type="button" class="active" data-value="all">All items</button>
	<button type="button" data-value="image">Images</button>
	<button type="button" data-value="url">Websites</button>
	<button type="button" data-value="quote">Quotes</button>

	<input type="search" id="search" placeholder="type to filter, hit return to search" />
</div>

<div id="content">
	<?php foreach( $posts as $i => $p ):
		$imageAttr = '';
		$quoteAttr = '';
		$urlLink 	 = '';

		$title = empty($p['title']) ? 'untitled' : $p['title'];
		$title2 = $title;

		if($p['type'] == 'quote') {
			$title = '';
			$title2 = '- '.$p['quote']['speaker'].' -';
			$quoteAttr = ' data-quote="'.$p['quote']['quote'].'" data-speaker="'.$p['quote']['speaker'].'" ';
		}

		if($p['type'] == 'image') {
			$imageAttr = ' data-image="'.$p['image']['image'].'" ';
		}

		if($p['type'] == 'url') {
			$urlLink = '<a class="url-link" href="'.$p['source'].'" target="_blank">';

			if($p['image_url'] != 'null') $urlLink.= '<img src="'.$p['image_url'].'" />';
			else $urlLink.= '<h4>WEBSITE</h4>';

			$urlLink.= '</a>';
		}

		$tags = [];
		foreach($p['tags'] as $t) {
			array_push($tags, $t['tag']);
		}
	?>



	<div class="post"
		data-id="<?= $p['id']; ?>"
		data-type="<?= $p['type'] ?>"
		data-title="<?= $title ?>"
		data-created="<?= $p['created'] ?>"
		data-source="<?= $p['source'] ?>"
		data-tags="<?= implode(',', $tags) ?>"
		<?= $imageAttr ?>
		<?= $quoteAttr ?>>
		<div class="post-image">

			<?= $urlLink ?>

			<div class="post-actions">
				<span class="post-type"><?= strtoupper($p['type']) ?></span>
				<button type="button" class="edit"></button>
			</div>

			<?php if($p['type'] == 'image'): ?>
				<img src="<?= $p['image']['thumb']; ?>" alt="" />
			<?php elseif($p['type'] == 'video'): ?>
				<img src="<?= $p['video']['thumb']; ?>" alt="" />
			<?php elseif($p['type'] == 'quote'): ?>
				<em><?= nl2br($p['quote']['quote']); ?></em>
			<?php endif; ?>
		</div>
		<div class="post-title">
				<em><?= $title2 ?></em>
		</div>
	</div>



	<?php endforeach; ?>

</div>

<div id="search-results"></div>

<?php
	$pgnStr = '?posts';
	if(isset($_GET['collection'])) $pgnStr.= '&collection='.$_GET['collection'];
	$pgnStr.= '&page=';
?>

<div id="pages">
	<div class="pageInfo">
		page <?= $pages['current']; ?> of <?= $pages['total']; ?>
	</div>

	<div class="pageLinks">
		<?php if( $pages['prev'] ) { ?>
			<a class="prev" href="<?= $pgnStr.$pages['prev']?>">&laquo; prev</a>
		<?php } else { ?>
			&laquo; prev
		<?php } ?>
		/
		<?php if( $pages['next'] ) { ?>
			<a class="next" href="<?= $pgnStr.$pages['next']?>">next &raquo;</a>
		<?php } else { ?>
			next &raquo;
		<?php } ?>
	</div>
</div>


<?php include(ASAPH_PATH.'admin/templates/foot.html.php'); ?>
