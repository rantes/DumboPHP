#!/usr/bin/php
<?php

$dumboSystemPath = '/etc/dumbophp';
$path = dirname(__FILE__);
$dumboSystemPathSrc = $dumboSystemPath.'/src';
$pathSrc = $path.'/src';
$binPath = '/usr/bin';

echo 'Installing DumboPHP. Please be patient...'.PHP_EOL;
if(!file_exists($dumboSystemPath)) {
	mkdir($dumboSystemPath);
	$d = dir($path);
	while (false !== ($entry = $d->read())) {
	   if($entry != '.' && $entry != '..' && $entry != 'install.php' && $entry != 'src'){
	   		echo 'copying '.$entry.PHP_EOL;
	   		copy($path.'/'.$entry.' '.$dumboSystemPath.'/'.$entry);
	   }
	}
	$d->close();

	mkdir($dumboSystemPathSrc);
	$d = dir($pathSrc);
	while (false !== ($entry = $d->read())) {
	   if($entry != '.' && $entry != '..'){
	   		echo 'copying '.$entry.PHP_EOL;
	   		copy($pathSrc.'/'.$entry.' '.$dumboSystemPathSrc.'/'.$entry);
	   }
	}
	$d->close();

	echo 'Creating bin file.'.PHP_EOL;
	symlink($dumboSystemPath.'/dumbo '.$binPath.'/dumbo');
} else {
	die('Install failed: DumboPHP is already installed in '.$dumboSystemPath.PHP_EOL);
}

?>