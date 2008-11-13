<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta id="routedURI" name="routedURI" content="<?php echo url::site().router::$routed_uri; ?>" />
<?php
	echo html::script(array(
		    'media/js/jquery-1.2.6.js',
		    'media/js/jquery.url.js',
		    'media/js/hasharray.js',
			'media/js/superfish',
		), FALSE);
	echo html::stylesheet(array('media/css/site',),array('screen',));
	echo html::stylesheet(array('media/css/menus',),array('screen',));
	echo html::stylesheet(array('media/css/forms',),array('screen',));
?>
<script type="text/javascript">
	  $(document).ready(function() {
        $('ul.sf-menu').superfish();
    });
</script>
<title><?php echo html::specialchars($title) ?></title>
</head>
<body>
<div id="wrapper">
<div id="banner">
<span>Indicia</span>
</div>
<ul class="sf-menu">
<?php
	$temp=array_keys($menu);
	$lastitem = $temp[count($menu)-1];
	foreach ($menu as $toplevel => $submenu):
		if ($toplevel==$lastitem)
			echo '<li class="last">'.$toplevel;
		else
			echo '<li>'.$toplevel;

		if (count($submenu)>0)
		{
			echo '<ul>';
			foreach ($submenu as $menuitem => $url):
				echo '<li>'.html::anchor($url, $menuitem).'</li>';
			endforeach;
			echo '</ul>';
		}
		echo '</li>';
	endforeach;
	 ?>
</ul>
<div id="content">
<h1><?php echo $title ?></h1>
<?php echo $content ?>
</body>
</html>

