<?php
	$link = mysql_connect('localhost', 'root', '380349');

	if (!$link) {
		die('Connect error: ' . mysql_error());
	}
	echo 'OK';

	$sql = "CREATE TABLE IF NOT EXISTS `threads` (
		`id` int(16) unsigned NOT NULL auto_increment,
		`name` text NOT NULL default '',
		`feedback` text NOT NULL default '',
		`geo` text NOT NULL default '',
		`ip` text unsigned NOT NULL default '',
		`message` text NOT NULL default '',
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8";

	mysql_query($sql);

	mysql_close($link);
?>
