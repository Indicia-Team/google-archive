<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta id="routedURI" name="routedURI" content=<?php echo "'".router::$routed_uri."'" ?> />
<script type="text/javascript" src="<?php echo url::base(); ?>jquery-1.2.6.js"></script>
<script type="text/javascript" src="<?php echo url::base(); ?>jquery.url.js"></script>
<script type="text/javascript" src="<?php echo url::base(); ?>hasharray.js"></script>
<?php echo html::stylesheet(array('media/css/site',),array('screen',)); ?>
<?php echo html::stylesheet(array('media/css/forms',),array('screen',)); ?>
</script>
<title><?php echo html::specialchars($title) ?></title>
</head>
<body>
<div id="wrapper">
<div id="banner">
<span>Indicia</span>
</div>
<div id="menu">
<ul>
<?php foreach ($links as $link => $url): ?>
<li><?php echo html::anchor($url, $link) ?></li>
<?php endforeach; ?>
</ul>
</div>
<div id="content">
<h1><?php echo $title ?></h1>
<?php echo $content ?>
</body>
</html>

