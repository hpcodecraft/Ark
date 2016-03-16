<?php
	$anchor = 'post-success';
	if($_POST['type'] == 'image') $anchor = 'image-success';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Post: Ark</title>
	<link rel="stylesheet" type="text/css" href="<?php echo ASAPH_POST_CSS; ?>" />
</head>
<body class="Asaph_Post">
	<h1 id="Asaph_PostSuccess">
		Posted!
	</h1>
	<script type="text/javascript">
		setTimeout(function() {
			if( parent ) {
				parent.location = "<?php echo addslashes($_POST['xhrLocation']) ?>#<?php echo $anchor ?>";
			}
		}, 500);
	</script>
</body>
</html>