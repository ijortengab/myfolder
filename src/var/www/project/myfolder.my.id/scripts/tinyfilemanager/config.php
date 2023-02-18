<?php
include(__DIR__.'/../config.php');

// Populate `$arg_p`.
$arg_p = '';
if (isset($_GET['p'])) {
    $arg_p = trim($_GET['p'], '/');
}

// Populate `$parent_directory`.
// /path/to/folder => $parent_directory = /path/to/folder
// /path/to/folder?p= => $parent_directory = ''
// /path/to/folder?p=/home/ => $parent_directory = '/home'
// /public/?p=storage&kita=1 $parent_directory = '/storage'
$parts = parse_url($_SERVER['REQUEST_URI']);
$parent_directory = '';
if (isset($parts['path'])) {
    $parent_directory = trim($parts['path'],'/');
}
if (isset($parts['query'])) {
    parse_str($parts['query'], $query);
    if (isset($query['p'])) {
        $parent_directory = trim($query['p'], '/');
    }
}
$parent_directory = empty($parent_directory) ? $parent_directory : '/'.$parent_directory;

// Validate host.
$subdomain = null;
do {
    switch ($_SERVER['HTTP_HOST']) {
        case $domain:
            $root_path = '/var/www/project/'.$domain.'/web';
            $use_auth = false;
            $global_readonly = true;
            $home_url = 'https://'.$_SERVER['HTTP_HOST'];
            break 2;

        case 'admin.'.$domain:
            $subdomain = 'admin';
            $user_config = '/var/www/project/'.$domain.'/scripts/tinyfilemanager/config.'.$_SERVER['REMOTE_USER'].'.php';
            $post_redirect = 'https://admin.'.$domain.$parent_directory;
            $root_path = '/var/www/project/'.$domain;
            $use_auth = false;
            $home_url = 'https://'.$_SERVER['HTTP_HOST'];
            break 2;

        case 'public.'.$domain:
            $subdomain = 'public';
            $root_path = '/var/www/project/'.$domain.'/public';
            $global_readonly = true;
            $use_auth = false;
            $home_url = 'https://'.$_SERVER['HTTP_HOST'];
            break 2;
    }
    if (preg_match('/^(?<user>.+)-(?<scope>public|private)\.'.preg_quote($domain).'$/', $_SERVER['HTTP_HOST'], $matches)) {
        $subdomain = 'user';
        $matches_user = $matches['user'];
        $matches_scope = $matches['scope'];
        $user_config = '/var/www/project/'.$domain.'/storage/'.$matches_user. '/scripts/config.php';
        $post_redirect = 'https://'.$matches_user.'-'.$matches_scope.'.'.$domain.$parent_directory;
        $use_auth = false;
        $global_readonly = false;
        $home_url = 'https://'.$domain;
        break;
    }
    if (preg_match('/^(?<user>.+)\.'.preg_quote($domain).'$/', $_SERVER['HTTP_HOST'], $matches)) {
        $subdomain = 'user_public';
        $matches_user = $matches['user'];
        $global_readonly = true;
        $use_auth = false;
        $home_url = 'https://'.$_SERVER['HTTP_HOST'];
        break;
    }
    die('Host not allowed: '.$_SERVER['HTTP_HOST']).'.';
}
while (false);

// Rewrite URL.
// Arahkan agar "https://public.$domain/?p=mnt" redirect ke
// https://public.$domain/mnt/. Hati-hati terhadap unlimited self redirect.
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $parts = parse_url($_SERVER['REQUEST_URI']);
    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
        $result = array_diff_key($query, array('p' => ''));
        if (empty($result)) {
            // Hanya ada query `p` saja, maka:
            $query_p = $query['p'];
            $query_p = empty($query_p) ? $query_p : '/'.$query_p;
            header('Location: https://'.$_SERVER['HTTP_HOST'].$query_p.'/');
        }
    }
}

// Ambil alih save setting. Buat agar tidak mengubah file utama.
// Save Config
switch ($subdomain) {
    case 'user':
    case 'admin':
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
            header("Location: $post_redirect");
            exit;
        }
        break;
}

// Arahkan agar download file tidak menggunakan PHP, langsung direct via Nginx.
switch ($subdomain) {
    case 'public':
    case 'user_public':
        if (isset($_GET['dl'])) {
            // Pada nginx, variable $arg_filename tidak mengubah + nya urlencode menjadi spasi.
            // Sehingga perlu kita ubah manual disini.
            // add_header Content-disposition "attachment; filename=$arg_filename";
            header('Location: https://'.$_SERVER['HTTP_HOST'].$parent_directory.'/'.$_GET['dl'].'?filename='.str_replace('+','%20',urlencode($_GET['dl'])).'&download=1');
            exit;
        }
        break;
}

switch ($subdomain) {
    case 'admin':
        if ($_SERVER['REMOTE_USER'] != 'admin') {
            http_response_code(403);
            die('Forbidden.');
        }
        $file = '/var/www/project/'.$domain.'/scripts/tinyfilemanager/config.tpl.php';
        $newfile = '/var/www/project/'.$domain.'/scripts/tinyfilemanager/config.'.$_SERVER['REMOTE_USER'].'.php';
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

        // For experienced user, you must add key query `all` in URL to
        // show excluded items.
        // Example:
        // - https://admin.$domain/?p=&all
        // - https://admin.$domain/?all
        // - https://admin.$domain/storage/user?all
        // - https://admin.$domain/?p=storage/user&all
        $global_readonly = false;
        if ($arg_p == '') {
            $exclude_items = array(
                '.htpasswd',
                'web',
            );
            $global_readonly = true;
        }
        elseif ($arg_p == 'scripts') {
            $exclude_items = array(
                'InstaGallery',
                'tinyfilemanager',
                'adduser.sh',
                'config.php',
            );
            $global_readonly = true;
        }
        elseif (preg_match('/^storage\/[^\/]+$/', $arg_p)) {
            $exclude_items = array(
                'scripts',
            );
        }
        // Cancel all.
        if (isset($_GET['all'])) {
            $exclude_items = array();
        }
        break;

    case 'public':
        // Variable $CONFIG untuk subdomain public di konfigurasi disini, karena
        // subdomain admin akan mengambil alih Variable $CONFIG yang disimpan di
        // script tinyfilemanager.php
        $CONFIG = '{"lang":"en","error_reporting":false,"show_hidden":false,"hide_Cols":true,"theme":"ligth"}';
        $user_config = '/var/www/project/'.$domain.'/storage/public/scripts/config.php';
        if (is_file($user_config)) {
            include_once($user_config);
        }

        break;

    case 'user_public':
        $root_path = '/var/www/project/'.$domain.'/storage/'.$matches['user'].'/public';
        $parent_directory = empty($arg_p) ? $arg_p : '/'.$arg_p;
        if (is_file($root_path.$parent_directory.'/403.html')) {
            http_response_code(403);
            die('Forbidden.');
        }
        if (is_file($root_path.$parent_directory.'/gallery.html') &&
            is_file ('/var/www/project/'.$domain.'/scripts/InstaGallery/index.php')
        ) {
            chdir($root_path.$parent_directory);
            include('/var/www/project/'.$domain.'/scripts/InstaGallery/index.php');
            exit;
        }
        $user_config = '/var/www/project/'.$domain.'/storage/'.$matches['user']. '/scripts/config.php';
        include_once($user_config);
        break;

    case 'user':
        if ($_SERVER['REMOTE_USER'] != $matches_user) {
            header('Location: https://'.$domain.'/');
            exit;
        }
        $user_config = '/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER']. '/scripts/config.php';
        include_once($user_config);
        $root_path = '/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER'].'/'.$matches_scope;
        // Browse ke directory symlink public tidak diperbolehkan
        // dan perlu diredirect ke subdomain public.
        // User nanti bisa mengcopy link dari directory public dan menduga
        // itu bisa diakses public.
        if ($matches_scope == 'private' && $arg_p != '') {
            $dirs = explode('/', $arg_p);
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
        if ($matches_scope == 'private' && isset($_GET['del']) && $arg_p == '') {
            $del = $_GET['del'];
            $realpath = realpath('/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER'].'/'.$matches['scope'].'/'.$del);
            if ($realpath == '/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER'].'/public') {
                http_response_code(403);
                die('Forbidden. Link to Public Directory cannot delete.');
            }
        }
        break;

    default:
        if ($_SERVER['REMOTE_USER'] == 'admin') {
            header('Location: https://'.$_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW'].'@admin.'.$domain);
            exit;
        }
        $user_storage = '/var/www/project/'.$domain.'/storage/'.$_SERVER['REMOTE_USER'];
        if (!is_dir($user_storage)) {
            if (!mkdir($user_storage, 0755, true)) {
                die('Failed to create directories...');
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
        if ($_SERVER['REMOTE_USER'] == 'public') {
            // Buat symbolic link relative.
            chdir($user_storage);
            if (!is_link('public')) {
                symlink('../../public', 'public');
            }
            if ($arg_p == '') {
                $exclude_items = array(
                    'private',
                );
            }
            break;
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
}
