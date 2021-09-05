#!/usr/bin/php
<?php

$dumboSystemPath = '/etc/dumbophp';
$path = dirname(__FILE__);
$pathSrc = $path.'/src';
$pathLib = $path.'/lib';
$pathBin = $path.'/bin';
$binPath = '/usr/local/bin';

fwrite(STDOUT, 'Installing DumboPHP. Please be patient...'.PHP_EOL);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    fwrite(STDOUT, 'This is a server using Windows! (we recomend GNU/Linux)'.PHP_EOL);
    $dumboSystemPath = shell_exec('echo %SYSTEMROOT%');
    $dumboSystemPath = str_replace(array("\n","\r"), '', $dumboSystemPath);
    $dumboSystemPath.= '/dumbophp';
    $binPath = '%SYSTEMROOT%/system32';
    defined('IS_WIN') or define('IS_WIN', true);
} else {
    fwrite(STDOUT, 'Great!!! this is a server not using Windows!'.PHP_EOL);
    defined('IS_WIN') or define('IS_WIN', false);

    is_dir($binPath) or mkdir($binPath);
}

$dumboSystemPathSrc = $dumboSystemPath.'/src';
$dumboSystemPathBin = $dumboSystemPath.'/bin';
$dumboSystemPathLib = $dumboSystemPath.'/lib';
file_exists($dumboSystemPath) || mkdir($dumboSystemPath, 0777, TRUE);

$d = dir($path);
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..' && $entry != 'install.php' && !is_dir($entry) && $entry != 'src' && $entry != 'lib' && $entry != '.git' && $entry != '.gitignore'){
        fwrite(STDOUT, 'copying '.$path.'/'.$entry.' to '.$dumboSystemPath.'/'.$entry.PHP_EOL);
        file_exists($dumboSystemPath.'/'.$entry) && unlink($dumboSystemPath.'/'.$entry);
        copy($path.'/'.$entry, $dumboSystemPath.'/'.$entry) or die('Could not copy file.');
   }
}
$d->close();

file_exists($dumboSystemPathSrc) || mkdir($dumboSystemPathSrc, 0777, TRUE);

$d = dir($pathSrc);
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..' && !is_dir($pathSrc.'/'.$entry)){
        fwrite(STDOUT, 'copying '.$pathSrc.'/'.$entry.' to '.$dumboSystemPathSrc.'/'.$entry.PHP_EOL);
        file_exists($dumboSystemPathSrc.'/'.$entry) && unlink($dumboSystemPathSrc.'/'.$entry);
        copy($pathSrc.'/'.$entry, $dumboSystemPathSrc.'/'.$entry) or die('Could not copy file.');
   }
}
$d->close();

file_exists($dumboSystemPathBin) || mkdir($dumboSystemPathBin, 0777, true);

$d = dir($pathBin);
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..' && !is_dir($pathBin.'/'.$entry)){
        fwrite(STDOUT, 'copying '.$pathBin.'/'.$entry.' to '.$dumboSystemPathBin.'/'.$entry.PHP_EOL);
        file_exists($dumboSystemPathBin.'/'.$entry) && unlink($dumboSystemPathBin.'/'.$entry);
        copy($pathBin.'/'.$entry, $dumboSystemPathBin.'/'.$entry) or die('Could not copy file.');
   }
}
$d->close();

file_exists($dumboSystemPathLib) || mkdir($dumboSystemPathLib, 0777, TRUE);

$d = dir($pathLib);
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..' && !is_dir($pathLib.'/'.$entry)){
       fwrite(STDOUT, 'copying '.$pathLib.'/'.$entry.' to '.$dumboSystemPathLib.'/'.$entry.PHP_EOL);
       file_exists($dumboSystemPathLib.'/'.$entry) && unlink($dumboSystemPathLib.'/'.$entry);
       copy($pathLib.'/'.$entry, $dumboSystemPathLib.'/'.$entry) or die('Could not copy file.');
   }
}
$d->close();

file_exists($dumboSystemPathLib.'/db_drivers') || mkdir($dumboSystemPathLib.'/db_drivers', 0777, TRUE);

$d = dir($pathLib.'/db_drivers');
while (false !== ($entry = $d->read())) {
   if($entry != '.' && $entry != '..' && !is_dir($entry)){
       fwrite(STDOUT, 'copying '.$pathLib.'/db_drivers/'.$entry.' to '.$dumboSystemPathLib.'/db_drivers/'.$entry.PHP_EOL);
       file_exists($dumboSystemPathLib.'/db_drivers/'.$entry) && unlink($dumboSystemPathLib.'/db_drivers/'.$entry);
       copy($pathLib.'/db_drivers/'.$entry, $dumboSystemPathLib.'/db_drivers/'.$entry) or die('Could not copy file.');
   }
}
$d->close();

file_exists($dumboSystemPathLib.'/Timothy') || mkdir($dumboSystemPathLib.'/Timothy', 0777, TRUE);

$d = dir($pathLib.'/Timothy');
while (false !== ($entry = $d->read())) {
    if($entry != '.' && $entry != '..' && !is_dir($entry)){
        fwrite(STDOUT, 'copying '.$pathLib.'/Timothy/'.$entry.' to '.$dumboSystemPathLib.'/Timothy/'.$entry.PHP_EOL);
        file_exists($dumboSystemPathLib.'/Timothy/'.$entry) && unlink($dumboSystemPathLib.'/Timothy/'.$entry);
        copy($pathLib.'/Timothy/'.$entry, $dumboSystemPathLib.'/Timothy/'.$entry) or die('Could not copy file.');
    }
}
$d->close();

fwrite(STDOUT, 'Creating bin files.'.PHP_EOL);
file_exists($binPath.'/dumbo') && unlink($binPath.'/dumbo');
file_exists($binPath.'/dumboTest') && unlink($binPath.'/dumboTest');
file_exists('/etc/bash_completion.d/dumbophp') && unlink('/etc/bash_completion.d/dumbophp');

(IS_WIN && copy($dumboSystemPath.'/dumbo.bat', $binPath.'/dumbo.bat')) or symlink($dumboSystemPathBin.'/dumbo', $binPath.'/dumbo');
IS_WIN or chmod($binPath.'/dumbo', 0775);
fwrite(STDOUT, 'Created dumbo bin file '.PHP_EOL);

symlink($dumboSystemPathBin.'/dumboTest', $binPath.'/dumboTest');
chmod($binPath.'/dumboTest', 0775);
fwrite(STDOUT, 'Created dumboTest bin file '.PHP_EOL);

symlink($dumboSystemPathBin.'/autocomplete.sh', '/etc/bash_completion.d/dumbophp');
fwrite(STDOUT, 'Created bash autocomplete bin file. --- Please restart your console! ---'.PHP_EOL);

fwrite(STDOUT, 'Install complete'.PHP_EOL);
?>