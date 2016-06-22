#!/usr/bin/php
<?php

$dumboSystemPath = '/etc/dumbophp';
$path = dirname(__FILE__);
$dumboSystemPathSrc = $dumboSystemPath.'/src';
$dumboSystemPathLib = $dumboSystemPath.'/lib';
$pathSrc = $path.'/src';
$pathLib = $path.'/lib';
$binPath = '/usr/local/bin';

echo 'Installing DumboPHP. Please be patient...'.PHP_EOL;

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
   echo 'This is a server using Windows! (we recomend GNU/Linux)'.PHP_EOL;
   $dumboSystemPath = shell_exec('echo %SYSTEMROOT%');
   $dumboSystemPath = str_replace(array("\n","\r"), '', $dumboSystemPath);
   $dumboSystemPath.= '/dumbophp';
   $binPath = '%SYSTEMROOT%/system32';
   defined('IS_WIN') or define('IS_WIN', true);
} else {
   echo 'Great!!! this is a server not using Windows!'.PHP_EOL;
   defined('IS_WIN') or define('IS_WIN', false);
}

file_exists($dumboSystemPath) || mkdir($dumboSystemPath, 0777, TRUE);

$d = dir($path);
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..' && $entry != 'install.php' && $entry != 'src' && $entry != 'lib' && $entry != '.git' && $entry != '.gitignore'){
   		echo 'copying '.$path.'/'.$entry.' to '.$dumboSystemPath.'/'.$entry.PHP_EOL;
   		file_exists($dumboSystemPath.'/'.$entry) && unlink($dumboSystemPath.'/'.$entry);
   		copy($path.'/'.$entry, $dumboSystemPath.'/'.$entry) or die('Could not copy file.');
   }
}
$d->close();

file_exists($dumboSystemPathSrc) || mkdir($dumboSystemPathSrc, 0777, TRUE);

$d = dir($pathSrc);
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..' && !is_dir($pathSrc.'/'.$entry)){
         echo 'copying '.$pathSrc.'/'.$entry.' to '.$dumboSystemPathSrc.'/'.$entry.PHP_EOL;
         file_exists($dumboSystemPathSrc.'/'.$entry) && unlink($dumboSystemPathSrc.'/'.$entry);
         copy($pathSrc.'/'.$entry, $dumboSystemPathSrc.'/'.$entry) or die('Could not copy file.');
   }
}
$d->close();

file_exists($dumboSystemPathLib) ||	mkdir($dumboSystemPathLib, 0777, TRUE);

$d = dir($pathLib);
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..' && !is_dir($pathLib.'/'.$entry)){
         echo 'copying '.$pathLib.'/'.$entry.' to '.$dumboSystemPathLib.'/'.$entry.PHP_EOL;
         file_exists($dumboSystemPathLib.'/'.$entry) && unlink($dumboSystemPathLib.'/'.$entry);
         copy($pathLib.'/'.$entry, $dumboSystemPathSrc.'/'.$entry) or die('Could not copy file.');
   }
}
$d->close();

file_exists($dumboSystemPathLib.'/db_drivers') || mkdir($dumboSystemPathLib.'/db_drivers', 0777, TRUE);

$d = dir($pathLib.'/db_drivers');
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..' && !is_dir($entry)){
         echo 'copying '.$pathLib.'/db_drivers/'.$entry.' to '.$dumboSystemPathLib.'/db_drivers/'.$entry.PHP_EOL;
         file_exists($dumboSystemPathLib.'/db_drivers/'.$entry) && unlink($dumboSystemPathLib.'/db_drivers/'.$entry);
         copy($pathLib.'/db_drivers/'.$entry, $dumboSystemPathLib.'/db_drivers/'.$entry) or die('Could not copy file.');
   }
}
$d->close();

print('Creating bin file.'.PHP_EOL);
file_exists($binPath.'/dumbo') && unlink($binPath.'/dumbo');

(IS_WIN && copy($dumboSystemPath.'/dumbo.bat', $binPath.'/dumbo.bat')) or symlink($dumboSystemPath.'/dumbo', $binPath.'/dumbo');
IS_WIN or chmod($binPath.'/dumbo', 0775);

echo 'Install complete.'.PHP_EOL;

?>