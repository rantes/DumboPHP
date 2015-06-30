#!/usr/bin/php
<?php

$dumboSystemPath = '/etc/dumbophp';
$path = dirname(__FILE__);
$dumboSystemPathSrc = $dumboSystemPath.'/src';
$pathSrc = $path.'/src';
$binPath = '/usr/bin';

echo 'Installing DumboPHP. Please be patient...'.PHP_EOL;
if(file_exists($dumboSystemPath)){
	echo 'Updating DumboPHP...',PHP_EOL;
} else {
	mkdir($dumboSystemPath);
}

$d = dir($path);
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..' && $entry != 'install.php' && $entry != 'src' && $entry != '.git' && $entry != '.gitignore'){
   		echo 'copying '.$entry.PHP_EOL;
   		copy($path.'/'.$entry, $dumboSystemPath.'/'.$entry);
   }
}
$d->close();

if(!file_exists($dumboSystemPath)){
	mkdir($dumboSystemPathSrc);
}

$d = dir($pathSrc);
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..'){
   		echo 'copying '.$entry.PHP_EOL;
   		copy($pathSrc.'/'.$entry, $dumboSystemPathSrc.'/'.$entry);
   }
}
$d->close();

echo 'Creating bin file.'.PHP_EOL;
file_exists($dumboSystemPath.'/dumbo') or symlink($dumboSystemPath.'/dumbo', $binPath.'/dumbo');

echo 'Install complete.'.PHP_EOL;
?>