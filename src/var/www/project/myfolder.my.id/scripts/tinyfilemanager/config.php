<?php
include(__DIR__.'/../config.php');

// Rewrite URL.
// Arahkan agar "https://public.$domain/?p=mnt" redirect ke
// https://public.$domain/mnt/. Hati-hati terhadap unlimited self redirect.
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $request_uri = $_SERVER['REQUEST_URI'];
    $parts = parse_url($request_uri);
    if (isset($parts['query'])) {
       parse_str($parts['query'], $query);
       $result = array_diff_key($query, array('p' => ''));
       if (empty($result)) {
            // Hanya ada query `p` saja, maka:
            $selected_directory = $query['p'];
            switch ($selected_directory) {
                case '':
                    header('Location: https://'.$_SERVER['HTTP_HOST'].'/');
                    break;
                default:
                    header('Location: https://'.$_SERVER['HTTP_HOST'].'/'.trim($selected_directory, '/').'/');
                    break;
            }
            exit;
       }
    }
}

do {
    if ('admin.'.$domain == $_SERVER['HTTP_HOST']) {
        if ($_SERVER['REMOTE_USER'] != 'admin') {
            http_response_code(403);
            die('Forbidden.');
        }
        // For experienced user, you must add key query `all` in URL to
        // show excluded items.
        // Example:
        // - https://admin.$domain/?p=&all
        // - https://admin.$domain/?all
        // - https://admin.$domain/storage/user?all
        // - https://admin.$domain/?p=storage/user&all
        if (isset($_GET['p']) && in_array($_GET['p'], array('','/'))) {
            $exclude_items = array(
                '.htpasswd',
                'web',
            );
            if (isset($_GET['all'])) {
                $exclude_items = array();
            }
        }
        if (isset($_GET['p']) && preg_match('/^\/?scripts\/?$/', $_GET['p'])) {
            $exclude_items = array(
                'tinyfilemanager',
                'adduser.sh',
            );
            if (isset($_GET['all'])) {
                $exclude_items = array();
            }
        }
        if (isset($_GET['p']) && preg_match('/^\/?storage\/[^\/]+\/?$/', $_GET['p'])) {
            $exclude_items = array(
                'scripts',
            );
            if (isset($_GET['all'])) {
                $exclude_items = array();
            }
        }
        $root_path = '/var/www/project/'.$domain;
        $global_readonly = false;
        $use_auth = false;
        break;
    }
    if ('public.'.$domain == $_SERVER['HTTP_HOST']) {
        // Variable $CONFIG untuk subdomain public di konfigurasi disini, karena
        // subdomain admin akan mengambil alih Variable $CONFIG yang disimpan di
        // script tinyfilemanager.php
        $CONFIG = '{"lang":"en","error_reporting":false,"show_hidden":false,"hide_Cols":true,"theme":"ligth"}';
        $root_path = '/var/www/project/'.$domain.'/public';
        $global_readonly = true;
        $use_auth = false;
        $home_url = 'https://public.'.$domain;
        // Arahkan agar download file tidak menggunakan PHP, langsung direct via Nginx.
        if (isset($_GET['dl'])) {
            // Redirect.
            $uri = $_SERVER['REQUEST_URI'];
            $parts = parse_url($uri);
            $parent_directory = '';
            if (isset($parts['path'])) {
                $parent_directory = trim($parts['path'],'/');
            }
            if (isset($parts['query'])) {
                parse_str($parts['query'], $query);
                if (isset($query['p'])) {
                    $parent_directory = $query['p'];
                }
            }
            $parent_directory = empty($parent_directory) ? $parent_directory : '/'.$parent_directory;
            header('Location: https://'.$_SERVER['HTTP_HOST'].$parent_directory.'/'.$_GET['dl'].'?filename='.$_GET['dl'].'&download=1');
            exit;
        }
        break;
    }
    if ($domain == $_SERVER['HTTP_HOST']) {
        if ($_SERVER['REMOTE_USER'] == 'admin') {
            header('Location: https://'.$_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW'].'@admin.'.$domain);
            exit;
        }
        $user_storage = '/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER'];
        $root_path = '/var/www/project/'.$domain.'/web';
        $use_auth = false;
        $global_readonly = true;
        if (!is_dir($user_storage)) {
            if (!mkdir($user_storage, 0755, true)) {
                die('Failed to create directories...');
            }
        }
        $public = $user_storage.'/public';
        if (!is_dir($public)) {
            if (!mkdir($public, 0755, true)) {
                die('Failed to create directories...');
            }
        }
        $private = $user_storage.'/private';
        if (!is_dir($private)) {
            if (!mkdir($private, 0755, true)) {
                die('Failed to create directories...');
            }
            // Buat symbolic link relative.
            chdir($private);
            if (!is_link('public')) {
                symlink('../public', 'public');
            }
        }
        $scripts = $user_storage.'/scripts';
        if (!is_dir($scripts)) {
            if (!mkdir($scripts, 0755, true)) {
                die('Failed to create directories...');
            }
        }
        $file = '/var/www/project/'.$domain.'/scripts/tinyfilemanager/tinyfilemanager.php';
        $newfile = $scripts.'/tinyfilemanager.php';
        if (!is_link($newfile)) {
            symlink($file, $newfile);
        }
        $file = '/var/www/project/'.$domain.'/scripts/tinyfilemanager/config.tpl.php';
        $newfile = $scripts.'/config.php';
        if (!is_file($newfile)) {
            if (!copy($file, $newfile)) {
                echo "failed to copy $file...\n";
            }
            include_once($newfile);
        }
        else {
            $file = new SplFileObject($newfile);
            $n = 0;
            while (!$file->eof()) {
                $line = trim($file->fgets());
                if ($n++ == 2) {
                    break;
                }
            }
            preg_match("/^.+'(.+)'.+$/", $line, $matches);
            $CONFIG = $matches[1];
        }
        $file = '/var/www/project/'.$domain.'/scripts/tinyfilemanager/translation.json';
        $newfile = $scripts.'/translation.json';
        if (!is_link($newfile)) {
            symlink($file, $newfile);
        }
        break;
    }
    preg_match('/^(?<user>[^-]+)\.'.preg_quote($domain).'$/', $_SERVER['HTTP_HOST'], $matches);
    if ($matches) {
        $root_path = '/var/www/project/'.$domain.'/storage/'.$matches['user'].'/public';
        $global_readonly = true;
        $use_auth = false;
        // Arahkan agar download file tidak menggunakan PHP, langsung direct via Nginx.
        if (isset($_GET['dl'])) {
            // Redirect.
            $uri = $_SERVER['REQUEST_URI'];
            $parts = parse_url($uri);
            $parent_directory = '';
            if (isset($parts['path'])) {
                $parent_directory = trim($parts['path'],'/');
            }
            if (isset($parts['query'])) {
                parse_str($parts['query'], $query);
                if (isset($query['p'])) {
                    $parent_directory = $query['p'];
                }
            }
            $parent_directory = empty($parent_directory) ? $parent_directory : '/'.$parent_directory;
            header('Location: https://'.$_SERVER['HTTP_HOST'].$parent_directory.'/'.$_GET['dl'].'?filename='.$_GET['dl'].'&download=1');
            exit;
        }
        break;
    }
    preg_match('/^(?<user>.+)-(?<scope>public|private)\.'.preg_quote($domain).'$/', $_SERVER['HTTP_HOST'], $matches);
    if ($matches) {
        if ($_SERVER['REMOTE_USER'] != $matches['user']) {
            header('Location: https://'.$domain.'/');
            exit;
        }
        $user_config = '/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER']. '/scripts/config.php';
        include_once($user_config);
        $use_auth = false;
        $root_path = '/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER'].'/'.$matches['scope'];
        $global_readonly = false;
        $home_url = 'https://'.$domain;
        // Browse ke directory symlink public tidak diperbolehkan
        // dan perlu diredirect ke subdomain public.
        // User nanti bisa mengcopy link dari directory public dan menduga
        // itu bisa diakses public.
        if ($_SERVER['REQUEST_METHOD'] == 'GET' &&
            $matches['scope'] == 'private' &&
            isset($_GET['p']) &&
            ($path = trim($_GET['p'], '/')) != ''
            ) {
            $dirs = explode('/', $path);
            $first = array_shift($dirs);
            $parent_directory = implode('/', $dirs);
            $realpath = realpath('/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER'].'/'.$matches['scope'].'/'.$first);
            if ($realpath == '/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER'].'/public') {
                header('Location: https://'.$_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW'].'@'.$_SERVER['REMOTE_USER'].'-public.'.$domain.'/'.$parent_directory);
                exit;
            }
        }
        // Symlink ke arah directory public tidak boleh di hapus.
        // Di-rename masih boleh.
        if ($matches['scope'] == 'private' && isset($_GET['del']) && isset($_GET['p']) && $_GET['p'] == '') {
            $del = $_GET['del'];
            $realpath = realpath('/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER'].'/'.$matches['scope'].'/'.$del);
            if ($realpath == '/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER'].'/public') {
                http_response_code(403);
                die('Forbidden.');
            }
        }
        // Ambil alih save setting. Buat agar tidak mengubah file utama.
        // Save Config
        if (isset($_POST['type']) && $_POST['type'] == "settings") {
            class FM_Config_Alt {
                var $data;
                function __construct($filename) {
                    $file = new SplFileObject($filename);
                    $n = 0;
                    while (!$file->eof()) {
                        $line = trim($file->fgets());
                        if ($n++ == 2) {
                            break;
                        }
                    }
                    preg_match("/^.+'(.+)'.+$/", $line, $m);
                    $json = $m[1];
                    $this->data = json_decode($json, true);
                }
                function save($filename) {
                    $fm_file = $filename;
                    $var_name = '$CONFIG';
                    $var_value = var_export(json_encode($this->data), true);
                    $config_string = "<?php" . chr(13) . chr(10) . "//Default Configuration".chr(13) . chr(10)."$var_name = $var_value;" . chr(13) . chr(10);
                    if (is_writable($fm_file)) {
                        $lines = file($fm_file);
                        if ($fh = @fopen($fm_file, "w")) {
                            @fputs($fh, $config_string, strlen($config_string));
                            for ($x = 3; $x < count($lines); $x++) {
                                @fputs($fh, $lines[$x], strlen($lines[$x]));
                            }
                            @fclose($fh);
                        }
                    }
                }
            }
            // Alternative fm_get_translations([]);
            $newLng = $_POST['js-language'];
            do {
                if (!is_file('translation.json')) {
                    $newLng = 'en';
                    break;
                }
                $content = @file_get_contents('translation.json');
                $r = preg_match_all('/'.preg_quote('"code": "'.$newLng.'"').'/', $content, $m);
                if ($r === 0) {
                    $newLng = 'en';
                    break;
                }
            }
            while (false);
            $erp = isset($_POST['js-error-report']) && $_POST['js-error-report'] == "true" ? true : false;
            $shf = isset($_POST['js-show-hidden']) && $_POST['js-show-hidden'] == "true" ? true : false;
            $hco = isset($_POST['js-hide-cols']) && $_POST['js-hide-cols'] == "true" ? true : false;
            $te3 = $_POST['js-theme-3'];
            $cfg = new FM_Config_Alt($user_config);
            if ($cfg->data['lang'] != $newLng) {
                $cfg->data['lang'] = $newLng;
            }
            if ($cfg->data['error_reporting'] != $erp) {
                $cfg->data['error_reporting'] = $erp;
            }
            if ($cfg->data['show_hidden'] != $shf) {
                $cfg->data['show_hidden'] = $shf;
            }
            if ($cfg->data['show_hidden'] != $shf) {
                $cfg->data['show_hidden'] = $shf;
            }
            if ($cfg->data['hide_Cols'] != $hco) {
                $cfg->data['hide_Cols'] = $hco;
            }
            if ($cfg->data['theme'] != $te3) {
                $cfg->data['theme'] = $te3;
            }
            $cfg->save($user_config);
            // Redirect.
            $uri = $_SERVER['REQUEST_URI'];
            $parts = parse_url($uri);
            $parent_directory = '';
            if (isset($parts['path'])) {
                $parent_directory = trim($parts['path'],'/');
            }
            if (isset($parts['query'])) {
                parse_str($parts['query'], $query);
                if (isset($query['p'])) {
                    $parent_directory = $query['p'];
                }
            }
            header('Location: https://'.$_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW'].'@'.$_SERVER['REMOTE_USER'].'-'.$matches['scope'].'.'.$domain.'/'.$parent_directory);
            exit;
        }
        // Setelah save setting, ternyata:
        // - modal tidak ketutup sendiri.
        // - perubahan tidak segera terlihat karena adanya
        //   proses menulis file script .php
        // Kita pakai tricy dengan cara:
        // - redirect ke domain utama
        // - beri tambahan waktu delay (dengan sleep)
        //   agar perubahan dapat dirasakan setelah save.
        if (preg_match('/settings=1/', $_SERVER['HTTP_REFERER']) && preg_match('/settings=1/', $_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
            // Trigger save setting button on click.
            $uri = $_SERVER['REQUEST_URI'];
            $parts = parse_url($uri);
            $parent_directory = '';
            if (isset($parts['path'])) {
                $parent_directory = trim($parts['path'],'/');
            }
            if (isset($parts['query'])) {
                parse_str($parts['query'], $query);
                if (isset($query['p'])) {
                    $parent_directory = $query['p'];
                }
            }
            // Beri waktu, agar perubahan file dapat dibaca ulang.
            sleep(2);
            header('Location: https://'.$_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW'].'@'.$domain.'/'.$matches['scope'].'/'.$parent_directory);
            exit;
        }
        break;
    }
    die('Host not allowed: '.$_SERVER['HTTP_HOST']).'.';
} while (false);
