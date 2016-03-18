<!DOCTYPE html>
<html>
<head>
	<title>Post: Ark</title>
	<link rel="stylesheet" type="text/css" href="<?php echo ASAPH_BASE_URL; ?>admin/templates/css/jquery.tagsinput.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo ASAPH_POST_CSS; ?>" />
</head>
<body class="Asaph_Post">

	<?php if( !empty($status) ): ?>
		<div class="warn">
			<?php if( $status == 'not-logged-in' ) { ?>The name or password was not correct!<?php } ?>
			<?php if( $status == 'download-failed' ) { ?>Couldn't load the image!<?php } ?>
			<?php if( $status == 'duplicate-image' ) { ?>This image was already posted!<?php } ?>
			<?php if( $status == 'thumbnail-failed' ) { ?>Couldn't create a thumbnail of the image!<?php } ?>
		</div>
	<?php endif; ?>


  <?php if( !empty($_POST['video']) || !empty($_GET['video']) ) { ?>
		<!-- <embed
			src="<?php printReqVar('video'); ?>"
			type="<?php printReqVar('type'); ?>"
			width="350"
			height="350"
			style="float:left;margin-right:10px;max-width: 350px;max-height 350px;background-color:black;border-top-left-radius: 10px;"
		/> -->
		<img id="image" style="background-color:rgb(64,64,64);background-image:url(<?php printReqVar('thumb'); ?>);background-size:cover;
    	width: 350px;
    	height:350px;
    	float: left;
    	margin-right: 10px;
    	background-position:center;
    	border-top-left-radius: 10px;
    	margin-bottom:0px;
    	" src="<?php echo Asaph_Config::$absolutePath;?>templates/ark/playbutton.svg"/>
  <?php } ?>

	<?php if($_GET['type'] == 'image' && $_GET['image'] == 'upload'): ?>
	<form action="post.php" method="post" style="display: block;" enctype="multipart/form-data">
	<?php else: ?>
	<form action="post.php" method="post" style="display: block;">
	<?php endif; ?>

		<input type="hidden" name="type" value="<?php printReqVar('type'); ?>"/>
		<input type="hidden" name="xhrLocation" value="<?php printReqVar('xhrLocation'); ?>"/>
		<?php if( !empty($loginError) ) { ?><span class="warn">The name or password was not correct!</span><?php } ?>


		<?php if($_GET['type'] == 'link'): ?>
		<div class="post link">
			<input type="hidden" name="url" value="<?php printReqVar('url'); ?>"/>
			<input type="hidden" name="image_url" value="<?php printReqVar('image'); ?>"/>

			<div class="post-image" id="image" style="background-image:url(<?php printReqVar('image'); ?>);"></div>
			<ul class="post-form">
				<li>
					<input type="text" placeholder="Title" id="title" name="title" value="<?php printReqVar('title'); ?>" />
				</li>
				<li>
					<textarea id="description" type="text" name="description"><?php printReqVar('description'); ?></textarea>
				</li>
			</ul>
		</div>

		<?php elseif($_GET['type'] == 'image'):
			$image = $_GET['image'];
		?>

		<?php if($image == 'upload'): ?>
			<div class="post image upload">
				<input type="file" name="image" class="upload" accept="image/*" />
				<input type="hidden" name="source" value="upload"/>
		<?php else: ?>
			<div class="post image">
				<input type="hidden" name="image" value="<?php printReqVar('image'); ?>"/>
				<input type="hidden" name="source" value="<?php printReqVar('image'); ?>"/>
				<div class="post-image">
					<img src="<?php printReqVar('image'); ?>" />
				</div>
			<?php endif; ?>

			<ul class="post-form">
				<li>
					<input type="text" placeholder="Title" id="title" name="title" value="<?php printReqVar('title'); ?>" />
				</li>
				<li>
					<textarea id="description" type="text" name="description">
						<?php printReqVar('description'); ?>
					</textarea>
				</li>
			</ul>
		</div>

		<?php elseif($_GET['type'] == 'quote'): ?>
		<div class="post quote">
			<input type="hidden" name="source" value="<?php printReqVar('source'); ?>"/>

			<ul class="post-form">
				<li>
					<textarea id="quote" type="text" placeholder="A Deep and Meaningful Quote" name="quote"><?php printReqVar('quote'); ?></textarea>
				</li>
				<li>
					<input id="speaker" type="text" name="speaker" placeholder="by an intelligent Person" value="<?php printReqVar('speaker'); ?>"/>
				</li>
			</ul>
		</div>

		<?php elseif($_GET['type'] == 'story'): ?>
		<div class="post story">
			<ul class="post-form">
				<li>
					<input type="text" placeholder="Title" id="title" name="title" value="<?php printReqVar('title'); ?>" />
				</li>
				<li>
					<textarea id="description" type="text" name="description"><?php printReqVar('description'); ?></textarea>
				</li>
			</ul>
		</div>
		<?php endif; ?>









		<?php if( !empty($_POST['video']) || !empty($_GET['video']) ) { ?>
			<dl>
				<!-- <dt>Title:</dt> -->
				<!--<dd><input id="title" type="text" name="title" value="<?php printReqVar('title'); ?>"/></dd>-->

				<input type="hidden" name="video" value="<?php printReqVar('video'); ?>"/>
				<input type="hidden" name="thumb" value="<?php printReqVar('thumb'); ?>"/>

				<!-- <dt>Source:</dt> -->
				<dd>
					<input type="hidden" name="source" value="<?php printReqVar('source'); ?>"/>
				</dd>
				<!-- <dt>Description:</dt> -->
				<dd><textarea id="description" type="text" name="description"><?php printReqVar('description'); ?></textarea></dd>

				<input type="hidden" name="height" value="<?php printReqVar('height'); ?>"/>
				<input type="hidden" name="width" value="<?php printReqVar('width'); ?>"/>
				<input type="hidden" name="video_type" value="<?php printReqVar('video_type'); ?>"/>
			</dl>
		<?php } ?>

		<div class="post-tags">
      <input type="text" id="post-tags" name="tags" class="long" placeholder="Tags, Go, Here" value="" />
    </div>

		<div class="post-collection">
			<select name="collection">
				<option value="0">Select a collection...</option>
				<?php foreach($collections as $c): ?>
				<option value="<?php echo $c['id'] ?>"<?php if($c['id'] == $post['collection']) echo ' selected' ?>><?php echo $c['name'] ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="post-footer">
			<div class="well">
				<label>
					<input type="checkbox" name="public" checked />
					public
				</label>

				<label>
					<input type="checkbox" name="nsfw" />
					nsfw
				</label>
			</div>
			<button class="add" type="submit">add</button>
		</div>
	</form>

	<script src="<?php echo ASAPH_BASE_URL;?>admin/templates/js/jquery-1.10.2.min.js"></script>
	<script src="<?php echo ASAPH_BASE_URL;?>admin/templates/js/jquery.tagsinput.min.js"></script>

	<script>
    $(function() {
      $('#post-tags').tagsInput({
         'height':'40px',
         'width':'315px',
         'interactive':true,
         'defaultText':'add a tag',
         'removeWithBackspace' : true,
         'minChars' : 0,
         'maxChars' : 0, // if not provided there is no limit
         'placeholderColor' : '#666666'
      });
    });
  </script>
</body>
</html>
