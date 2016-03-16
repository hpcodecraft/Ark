<?php
	$getArr = array();
	foreach($_GET as $k => $v) {
		array_push($getArr, $k.'='.urlencode($v));
	}
	$getStr = implode('&', $getArr);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Login: Ark</title>
	<link rel="stylesheet" type="text/css" href="<?php echo ASAPH_POST_CSS; ?>" />
</head>
<body class="Asaph_Post login">
	<form action="post.php?<?php echo $getStr ?>" method="post">
		<h2>Please log in to continue.</h2>

		<ul>
			<li>
				<label>Name</label>
				<input id="name" type="text" name="name" value="" autofocus />
			</li>
			<li>
				<label>Password</label>
				<input type="password" name="pass" value="" />
			</li>
		</ul>

		<?php if( isset($loginError) ) { ?><span class="warn">The name or password was not correct!</span><?php } ?>

		<div class="post-footer">
			<button type="submit" name="login">login</button>
		</div>
	</form>
</body>
</html>
