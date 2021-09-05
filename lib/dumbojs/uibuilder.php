<?php
$dir = dirname(realpath(__FILE__));
$pathArray = explode('/', $dir);
defined('INST_PATH') or define('INST_PATH', implode('/',$pathArray).'/');

class UIBuilder {
    public $shellOutput = true;
    // private $_command = null;
    // private $_commands = [
    //     'watch'
    // ];

    private function _logger($source, $message) {
        if (empty($source) or empty($message) or !is_string($source) or !is_string($message)):
            return false;
        endif;
        $logdir = INST_PATH.'tmp/logs/';
        is_dir($logdir) or mkdir($logdir, 775);
        $file = "{$source}.log";
        $stamp = date('d-m-Y i:s:H');

        file_exists("{$logdir}{$file}") and filesize("{$logdir}{$file}") >= 524288000 and rename("{$logdir}{$file}", "{$logdir}{$stamp}_{$file}");
        $this->shellOutput and fwrite(STDOUT, "{$message}\n");
        file_put_contents("{$logdir}{jaja$file}", "[{$stamp}] - {$message}\n", FILE_APPEND);
        return true;
    }

    private function _readFiles($path, $pattern, $goUnder = true) {
        $files = [];
        $dir = opendir($path);
        //first level, not subdirectories
        while(false !== ($file = readdir($dir))):
            $file !== '.' and $file !== '..' and is_file("{$path}{$file}") and preg_match($pattern, $file, $matches) === 1 and ($files[] = "{$path}{$file}");
        endwhile;
        closedir($dir);
        //Second level, subdirectories
        if ($goUnder):
            $dir = opendir($path);
            while(false !== ($file = readdir($dir))):
                $npath = "{$path}{$file}";
                if ($file !== '.' and $file !== '..' and is_dir($npath) and is_readable($npath)):
                    $dir1 = opendir("{$path}{$file}");
                    if(false !== $dir1):
                        while(false !== ($file1 = readdir($dir1))):
                            is_file("{$path}{$file}/{$file1}") and preg_match($pattern, $file1, $matches) === 1 and ($files[] = "{$path}{$file}/{$file1}");
                        endwhile;
                        closedir($dir1);
                    endif;
                endif;
            endwhile;
            closedir($dir);
        endif;
        sort($files);

        return $files;
    }

    private function _cleanJS($code) {
        $pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/';
        $code = preg_replace($pattern, '', $code);
        $search = [
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
           '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        ];
        $replace = [
            '>',
            '<',
           '\\1',
            ''
        ];
        return preg_replace($search, $replace, $code);
    }

    public function sass() {
        $sass = new Sass();
        $sass->setStyle(Sass::STYLE_EXPANDED);
        $sass->setIncludePath(INST_PATH.'ui-sources/base-sass');
        $files = $this->_readFiles(INST_PATH.'ui-sources/components/', '/(.+)\.scss/');
        array_unshift($files, INST_PATH.'ui-sources/styles.scss');
        $bigFile = '';
        if(sizeof($files) > 0):
            while (null !== ($file = array_shift($files))):
                $bigFile .= file_get_contents($file)."\n";
            endwhile;
        endif;
        $css = $sass->compile($bigFile);
        file_put_contents(INST_PATH.'app/webroot/css/styles.css', $css);
        $this->render = ['text' => 'done', 'layout'=>false];
    }

    public function setspecs() {
        $files = $this->_readFiles(INST_PATH.'ui-sources/components/', '/^(?=.*\.spec).+\.js$/');

        $bigFile = '';
        if(sizeof($files) > 0):
            while (null !== ($file = array_shift($files))):
                $name = basename($file);
                $bigFile .= file_get_contents($file)."\n";
            endwhile;
        endif;

        file_put_contents(INST_PATH.'app/webroot/js/specs.min.js', $bigFile);
        $this->render = ['text' => 'NOOP'];
    }

    public function setlibs() {
        file_exists('app/webroot/libs/') or mkdir('app/webroot/libs/', 0775);
        $filesjs = $this->_readFiles(INST_PATH.'ui-sources/libs/', '/(.+)\.js/');
        $filescss = $this->_readFiles(INST_PATH.'ui-sources/libs/', '/(.+)\.css/');

        if(sizeof($filesjs) > 0):
            while (null !== ($file = array_shift($filesjs))):
                $name = basename($file);
                copy($file, INST_PATH."app/webroot/libs/{$name}");
            endwhile;
        endif;

        if(sizeof($filescss) > 0):
            while (null !== ($file = array_shift($filescss))):
                $name = basename($file);
                copy($file, INST_PATH."app/webroot/libs/{$name}");
            endwhile;
        endif;
    }

    public function buildDirectives() {
        $this->render = ['text' => 'done', 'layout'=>false];
        $files = $this->_readFiles(INST_PATH.'ui-sources/components/', '/^(?=.*\.directive)(?!.*?\.spec).+\.js$/');
        file_exists(INST_PATH.'app/webroot/js/components.min.js') and unlink(INST_PATH.'app/webroot/js/components.min.js');
        if(sizeof($files) > 0):
            while (null !== ($file = array_shift($files))):
                $file = $this->_cleanJS($file);
                file_put_contents(INST_PATH.'app/webroot/js/components.min.js', file_get_contents($file)."\n", FILE_APPEND);
            endwhile;
        endif;
    }

    public function buildFactories() {
        $this->render = ['text' => 'done', 'layout'=>false];
        $files = $this->_readFiles(INST_PATH.'ui-sources/components/', '/^(?=.*\.factory)(?!.*?\.spec).+\.js$/');
        file_exists(INST_PATH.'app/webroot/js/factories.min.js') and unlink(INST_PATH.'app/webroot/js/factories.min.js');
        $classes = [];
        $requires = [];
        $fileClases = [];
        $filesToBuild = [];
        if(sizeof($files) > 0):
            while (null !== ($file = array_shift($files))):
                $fp = fopen($file, 'r');
                while(!feof($fp)):
                    $line = fread($fp, 1024);
                    preg_match('@class[\s]+([\w]+)[\s]+(?:extends[\s]+([\w]+))?@mix', $line, $matches);
                    if(!empty($matches[1])):
                        if(!empty($matches[2])):
                            $requires[$matches[1]] = $matches[2];
                        endif;
                        $classes[] = $matches[1];
                        $fileClases[$matches[1]] = $file;
                        break;
                    endif;
                endwhile;
                fclose($fp);
            endwhile;

            foreach($fileClases as $class => $file):
                if(!empty($requires[$class])):
                    $required = $requires[$class];
                    $filesToBuild[$required] = $fileClases[$required];
                    $fileClases[$required] = null;
                    unset($fileClases[$required]);
                endif;
                if(!empty($fileClases[$class]) and empty($filesToBuild[$class])):
                    $filesToBuild[$class] = $fileClases[$class];
                    $fileClases[$class] = null;
                    unset($fileClases[$class]);
                endif;
            endforeach;

            while (null !== ($file = array_shift($filesToBuild))):
                $file = $this->_cleanJS($file);
                file_put_contents(INST_PATH.'app/webroot/js/factories.min.js', file_get_contents($file)."\n", FILE_APPEND);
            endwhile;
        endif;
    }

    public function buildUI() {
        $this->setlibs();
        $this->sass();
        $this->buildFactories();
        $this->buildDirectives();
    }

    public function setTestPage() {
        $this->buildUI();
        $this->setspecs();
        $this->render = ['text' => 'done', 'layout'=>false];
        $style = file_exists('app/webroot/css/styles.css') ? file_get_contents('app/webroot/css/styles.css') : '';
        $jscomponents = file_exists('app/webroot/js/components.min.js') ? file_get_contents('app/webroot/js/components.min.js') : '';
        $jsfactories = file_exists('app/webroot/js/factories.min.js') ? file_get_contents('app/webroot/js/factories.min.js') : '';
        $dumbojs = file_exists('app/webroot/libs/dumbo.min.js') ? file_get_contents('app/webroot/libs/dumbo.min.js') : '';
        $dmbfactsjs = file_exists('app/webroot/libs/dmb-factories.min.js') ? file_get_contents('app/webroot/libs/dmb-factories.min.js') : '';
        $dmbcompsjs = file_exists('app/webroot/libs/dmb-components.min.js') ? file_get_contents('app/webroot/libs/dmb-components.min.js') : '';
        $specsjs = file_exists('app/webroot/js/specs.min.js') ? file_get_contents('app/webroot/js/specs.min.js') : '';
        $dumbocss = file_exists('app/webroot/libs/dmb-styles.css') ? file_get_contents('app/webroot/libs/dmb-styles.css') : '';
        $jasminelib = file_get_contents('app/webroot/libs/jasmine.js');
        $jasminehtml = file_get_contents('app/webroot/libs/jasmine-html.js');
        $jasmineboot = file_get_contents('app/webroot/libs/jasmine-boot.js');
        $jasminecss = file_get_contents('app/webroot/libs/jasmine.css');
        $page = <<<DUMBO
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Dumbo UI tests</title>
    <style type="text/css">
    :root {
        --primary: #2e1241;
        --primary-contrast: #FFFFFF;
        --primary-hover: #2e124199;
        --secondary: #f7f7f9;
        --secondary-contrast: #FFFFFF;
        --secondary-hover: #f7f7f999;
        --default: #e6e7e8;
        --default-contrast: #FFFFFF;
        --default-hover: #e6e7e899;
        --success: #46d45e;
        --success-contrast: #FFFFFF;
        --success-hover: #46d45e33;
        --information: #17a2b8;
        --information-contrast: #FFFFFF;
        --information-hover: #17a2b899;
        --warning: #ffc107;
        --warning-contrast: #FFFFFF;
        --warning-hover: #ffc10799;
        --error: #d6162d;
        --error-contrast: #FFFFFF;
        --error-hover: #d6162d55;
        --hover-opacity: 0.5;
    }

    {$dumbocss}

    {$style}

    {$jasminecss}
    </style>
</head>
<body>
        <div class="html-reporter">
            <div class="banner">
            </div>
            <ul class="symbol-summary"></ul>
            <div class="alert">
            </div>
            <div class="results">
            </div>
        </div>
        <div id="components">
        </div>
        <script type="text/javascript">
            {$dumbojs}
        </script>
        <script type="text/javascript">
            {$dmbfactsjs}
        </script>
        <script type="text/javascript">
            {$jsfactories}
        </script>
        <script type="text/javascript">
            {$dmbcompsjs}
        </script>
        <script type="text/javascript">
            {$jscomponents}
        </script>

        <script type="text/javascript">
        {$jasminelib}
        </script>
        <script type="text/javascript">
        {$jasminehtml}
        </script>
        <script type="text/javascript">
        {$jasmineboot}
        </script>
        <script type="text/javascript">
            {$specsjs}
        </script>
</body>
</html>
DUMBO;

        file_put_contents(INST_PATH.'app/webroot/test.html',$page);
    }

    public function testUI() {
        $this->render = ['text' => 'done', 'layout'=>false];
        $descriptorspec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['file', '/tmp/error-output.txt', 'a'],
        ];
        $cwd = '/tmp';
        $env = [];

        $process = proc_open('/opt/google/chrome/chrome --headless --disable-gpu --repl --run-all-compositor-stages-before-draw --virtual-time-budget=10000 file://' .INST_PATH. 'app/webroot/test.html', $descriptorspec, $pipes, $cwd, $env);
        if(is_resource($process)):
            $script = <<<DUMBO
let results = document.querySelector('.jasmine_html-reporter'), duration = results.querySelector('.jasmine-duration'), overall = results.querySelector('.jasmine-overall-result'), data = `\${duration.innerHTML} - \${overall.innerText}`; data;
DUMBO;

            fwrite($pipes[0], $script);
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $rvalue = proc_close($process);
            preg_match('@\{(?:.)+\}@', $output, $matches);
            $result = json_decode($matches[0])->result->value;
            preg_match('@((?:\d)+)\sfailures@', $result, $matches);
            $this->render['text'] = $result;
            $errors = (int)$matches[1];
            $this->_logger('dumbo_ui_unit_testing', $result);
            !!$errors and fwrite(STDERR, "{$matches[0]}\n");
        endif;
    }

    public function watchUI() {
        $this->_logger('dumbo_ui_watcher', 'Setting up files for watch...');
        $files = new ArrayObject();
        $list = [
            ...$this->_readFiles(INST_PATH.'ui-sources/components/', '/^(?=.*\.directive)(?!.*?\.spec).+\.js$/'),
            ...$this->_readFiles(INST_PATH.'ui-sources/components/', '/^(?=.*\.factory)(?!.*?\.spec).+\.js$/'),
            ...$this->_readFiles(INST_PATH.'ui-sources/components/', '/(.+)\.scss/'),
            ...$this->_readFiles(INST_PATH.'ui-sources/libs/', '/(.+)\.js/'),
            ...$this->_readFiles(INST_PATH.'ui-sources/libs/', '/(.+)\.css/'),
            ...$this->_readFiles(INST_PATH.'ui-sources/', '/(.+)\.scss/', false)
        ];
        $this->_logger('dumbo_ui_watcher', "Watching for changes in files: \n".implode("\n", $list));

        foreach($list as $file):
            $stats = stat($file);
            $files[] = ['path'=> $file, 'mtime' => $stats['mtime']];
        endforeach;
        $this->_logger('dumbo_ui_watcher', 'Watching files...');
        while(true):
            foreach($files as  $index => $file):
                $stats = stat($file['path']);
                if($stats['size'] > 0 and $file['mtime'] !== $stats['mtime']):
                    $this->_logger('dumbo_ui_watcher', "File changed {$file['path']}");
                    $files[$index]['mtime'] = $stats['mtime'];
                    $this->_logger('dumbo_ui_watcher', 'Runing tasks...');
                    $start = microtime(true);
                    $this->setTestPage();
                    $this->testUI();
                    $total = microtime(true) - $start;
                    $this->_logger('dumbo_ui_watcher', "Jobs finished, took {$total} seconds.");
                    break;
                endif;
            endforeach;
        endwhile;
        $this->render = ['text'=>'-', 'layout'=>false];
    }
}
?>