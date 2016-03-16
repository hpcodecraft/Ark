<!DOCTYPE html>
<html>
	<head>
		<title>Login: Ark</title>
		<link href='https://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="<?php echo Asaph_Config::$absolutePath; ?>admin/templates/css/admin.css" />
		<link rel="Shortcut Icon" href="<?php echo Asaph_Config::$absolutePath; ?>admin/templates/asaph.ico" />
	</head>
	<body class="Asaph_Admin login">

		<div id="menu">
			<h1>Ark</h1>
		</div>

		<div id="content">
			<h2>Please log in to continue.</h2>
			<form action="<?php echo Asaph_Config::$absolutePath; ?>admin/" method="post">
				<input type="hidden" name="login" value="1"/>

				<?php if( !empty($loginError) ) { ?><span class="warn">The name or password was not correct!</span><?php } ?>
				<ul>
					<li>
						<label>Name</label>
						<input type="text" name="name" value="" autofocus />
					</li>
					<li>
						<label>Password</label>
						<input type="password" name="pass" value="" />
					</li>
					<li>
						<button type="submit" name="login">login</button>
					</li>
				</ul>
			</form>
		</div>
	</body>
</html>
