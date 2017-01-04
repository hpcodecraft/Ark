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
  </head>
  <body>
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

    <div class="imprint">
      This micro blog is powered by <a href="https://github.com/hpcodecraft/Ark">Ark</a>
    </div>
  </body>
</html>
