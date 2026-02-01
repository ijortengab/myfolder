<?php

namespace IjorTengab\MyFolder\Core;

/**
 * Based on Symfony Request version 2.8.18.
 * Disederhanakan karena yang dibutuhkan hanyalah $pathInfo dan $basePath.
 * https://github.com/symfony/symfony/blob/2.8/src/Symfony/Component/HttpFoundation/Request.php
 */
class Request extends SymfonyRequest
{
    public static $rewrite_url;

    public function __construct()
    {
        $script_php = Application::$script_php;
        if (null === $script_php) {
            throw new \RuntimeException('The instance of Application has not been initialized.');
        }
        // $script_php bernilai index.php atau nama lain yang dapat direname
        // sesuai kebutuhan user.
        $script_php = basename($script_php);

        parent::__construct();
        $base_url = $this->getBaseUrl();
        $base_path = $this->getBasePath();
        $path_info = $this->getPathInfo();
        $SCRIPT_NAME = $this->server->get('SCRIPT_NAME');
        $SCRIPT_FILENAME = $this->server->get('SCRIPT_FILENAME');
        $HTTP_HOST = $this->server->get('HTTP_HOST');
        $REQUEST_URI = $this->server->get('REQUEST_URI');
        $DOCUMENT_ROOT = $this->server->get('DOCUMENT_ROOT');
        // echo '<pre>';
        // echo 'Pada mesin dengan PHP_SAPI: '.PHP_SAPI.', maka request sbb: '.PHP_EOL;
        // echo 'http://'.$HTTP_HOST.$REQUEST_URI.PHP_EOL;
        // echo 'menghasilkan nilai variable sbb:'.PHP_EOL;
        // echo '$base_url: '.$base_url.PHP_EOL;
        // echo '$base_path: '.$base_path.PHP_EOL;
        // echo '$path_info: '.$path_info.PHP_EOL;
        // echo '$SCRIPT_NAME: '.$SCRIPT_NAME.PHP_EOL;
        // echo '$SCRIPT_FILENAME: '.$SCRIPT_FILENAME.PHP_EOL;
        // echo '$DOCUMENT_ROOT: '.$DOCUMENT_ROOT.PHP_EOL;

        if (str_starts_with($SCRIPT_FILENAME, $DOCUMENT_ROOT)) {
            $is_script_filename_inside_document_root = true;
        }
        else {
            $is_script_filename_inside_document_root = true;
        }
        // Terdapat 7 koreksi nilai $base_path dan $path_info.
        $correction = 0;
        $rewrite_url = null;
        $base_path_before = $base_path;
        $path_info_before = $path_info;
        // Kesemua logic dibawah ini harus berhasil membuat nilai variable
        // $rewrite_url menjadi boolean.
        do {
            if ($is_script_filename_inside_document_root) {
                if ($DOCUMENT_ROOT.DIRECTORY_SEPARATOR.$script_php == $SCRIPT_FILENAME) {
                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://ijortengab.id.localhost/
                    // menghasilkan nilai variable sbb:
                    // $base_url:
                    // $base_path:
                    // $path_info: /
                    // $SCRIPT_NAME: /index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://ijortengab.id.localhost/blog/
                    // menghasilkan nilai variable sbb:
                    // $base_url:
                    // $base_path:
                    // $path_info: /blog/
                    // $SCRIPT_NAME: /index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://ijortengab.id.localhost/blog/2017/
                    // menghasilkan nilai variable sbb:
                    // $base_url:
                    // $base_path:
                    // $path_info: /blog/2017/
                    // $SCRIPT_NAME: /index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://ijortengab.id.localhost/blog/2017/01-24-screen-solusi-remote-connection.md?html
                    // menghasilkan nilai variable sbb:
                    // $base_url:
                    // $base_path:
                    // $path_info: /blog/2017/01-24-screen-solusi-remote-connection.md
                    // $SCRIPT_NAME: /index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web
                    if ($base_url == '') {
                        $rewrite_url = true;
                        $correction = 1;
                        break;
                    }
                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://ijortengab.id.localhost/index.php
                    // menghasilkan nilai variable sbb:
                    // $base_url: /index.php
                    // $base_path:
                    // $path_info: /
                    // $SCRIPT_NAME: /index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://ijortengab.id.localhost/index.php/
                    // menghasilkan nilai variable sbb:
                    // $base_url: /index.php
                    // $base_path:
                    // $path_info: /
                    // $SCRIPT_NAME: /index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://ijortengab.id.localhost/index.php/blog/
                    // menghasilkan nilai variable sbb:
                    // $base_url: /index.php
                    // $base_path:
                    // $path_info: /blog/
                    // $SCRIPT_NAME: /index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://ijortengab.id.localhost/index.php/blog/2017/
                    // menghasilkan nilai variable sbb:
                    // $base_url: /index.php
                    // $base_path:
                    // $path_info: /blog/2017/
                    // $SCRIPT_NAME: /index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://ijortengab.id.localhost/index.php/blog/2017/01-24-screen-solusi-remote-connection.md?html
                    // menghasilkan nilai variable sbb:
                    // $base_url: /index.php
                    // $base_path:
                    // $path_info: /blog/2017/01-24-screen-solusi-remote-connection.md
                    // $SCRIPT_NAME: /index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web
                    if ($base_path == '') {
                        $base_path = $base_url;
                        $rewrite_url = false;
                        $correction = 2;
                        break;
                    }
                }
                if ($DOCUMENT_ROOT.$base_path.DIRECTORY_SEPARATOR.$script_php == $SCRIPT_FILENAME) {
                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://x.ijortengab.id.localhost:8033/web/
                    // menghasilkan nilai variable sbb:
                    // $base_url: /web
                    // $base_path: /web
                    // $path_info: /
                    // $SCRIPT_NAME: /web/index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://x.ijortengab.id.localhost:8033/web/blog/
                    // menghasilkan nilai variable sbb:
                    // $base_url: /web
                    // $base_path: /web
                    // $path_info: /blog/
                    // $SCRIPT_NAME: /web/index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://x.ijortengab.id.localhost:8033/web/blog/2017/
                    // menghasilkan nilai variable sbb:
                    // $base_url: /web
                    // $base_path: /web
                    // $path_info: /blog/2017/
                    // $SCRIPT_NAME: /web/index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://x.ijortengab.id.localhost:8033/web/blog/2017/01-24-screen-solusi-remote-connection.md?html
                    // menghasilkan nilai variable sbb:
                    // $base_url: /web
                    // $base_path: /web
                    // $path_info: /blog/2017/01-24-screen-solusi-remote-connection.md
                    // $SCRIPT_NAME: /web/index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost
                    if ($base_url == $base_path) {
                        $rewrite_url = true;
                        $correction = 3;
                        break;
                    }
                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://x.ijortengab.id.localhost:8033/web/index.php
                    // menghasilkan nilai variable sbb:
                    // $base_url: /web/index.php
                    // $base_path: /web
                    // $path_info: /
                    // $SCRIPT_NAME: /web/index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://x.ijortengab.id.localhost:8033/web/index.php/
                    // menghasilkan nilai variable sbb:
                    // $base_url: /web/index.php
                    // $base_path: /web
                    // $path_info: /
                    // $SCRIPT_NAME: /web/index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://x.ijortengab.id.localhost:8033/web/index.php/blog/
                    // menghasilkan nilai variable sbb:
                    // $base_url: /web/index.php
                    // $base_path: /web
                    // $path_info: /blog/
                    // $SCRIPT_NAME: /web/index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://x.ijortengab.id.localhost:8033/web/index.php/blog/2017/
                    // menghasilkan nilai variable sbb:
                    // $base_url: /web/index.php
                    // $base_path: /web
                    // $path_info: /blog/2017/
                    // $SCRIPT_NAME: /web/index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

                    // Pada mesin dengan PHP_SAPI: fpm-fcgi, maka request sbb:
                    // http://x.ijortengab.id.localhost:8033/web/index.php/blog/2017/01-24-screen-solusi-remote-connection.md?html
                    // menghasilkan nilai variable sbb:
                    // $base_url: /web/index.php
                    // $base_path: /web
                    // $path_info: /blog/2017/01-24-screen-solusi-remote-connection.md
                    // $SCRIPT_NAME: /web/index.php
                    // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
                    // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost
                    if ($base_url == $base_path.DIRECTORY_SEPARATOR.$script_php) {
                        $base_path = $base_url;
                        $rewrite_url = false;
                        $correction = 4;
                    }
                }
            }

            // cd /home/ijortengab/public_html/ijortengab.id.localhost/web
            // php -S localhost:8888 index.php

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:8888/
            // menghasilkan nilai variable sbb:
            // $base_url:
            // $base_path:
            // $path_info: /
            // $SCRIPT_NAME: /index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

            // Result: $correction = 1;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:8888/blog/
            // menghasilkan nilai variable sbb:
            // $base_url:
            // $base_path:
            // $path_info: /blog/
            // $SCRIPT_NAME: /index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

            // Result: $correction = 1;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:8888/blog/2017/
            // menghasilkan nilai variable sbb:
            // $base_url:
            // $base_path:
            // $path_info: /blog/2017/
            // $SCRIPT_NAME: /index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

            // Result: $correction = 1;

            if (substr($SCRIPT_FILENAME, 0, 1) == '/') {
                $is_script_filename_correct = true;
            }
            else {
                $is_script_filename_correct = false;
            }
            $is_script_filename_not_correct = !$is_script_filename_correct;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:8888/blog/2017/01-24-screen-solusi-remote-connection.md?html
            // menghasilkan nilai variable sbb:
            // $base_url:
            // $base_path:
            // $path_info: /blog/2017/01-24-screen-solusi-remote-connection.md?html
            // $SCRIPT_NAME: /blog/2017/01-24-screen-solusi-remote-connection.md?html
            // $SCRIPT_FILENAME: index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web
            if ($is_script_filename_not_correct) {
                $script_filename_revision = $DOCUMENT_ROOT.DIRECTORY_SEPARATOR.$SCRIPT_FILENAME;
                if ($DOCUMENT_ROOT.DIRECTORY_SEPARATOR.$script_php == $script_filename_revision) {
                     if ($base_url == '') {
                        $rewrite_url = true;
                        $correction = 5;
                        break;
                    }
                }
            }

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:8888/index.php
            // menghasilkan nilai variable sbb:
            // $base_url: /index.php
            // $base_path:
            // $path_info: /
            // $SCRIPT_NAME: /index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

            // Result: $correction = 2;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:8888/index.php/
            // menghasilkan nilai variable sbb:
            // $base_url: /index.php
            // $base_path:
            // $path_info: /
            // $SCRIPT_NAME: /index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

            // Result: $correction = 2;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:8888/index.php/blog/
            // menghasilkan nilai variable sbb:
            // $base_url: /index.php
            // $base_path:
            // $path_info: /blog/
            // $SCRIPT_NAME: /index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

            // Result: $correction = 2;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:8888/index.php/blog/2017/
            // menghasilkan nilai variable sbb:
            // $base_url: /index.php
            // $base_path:
            // $path_info: /blog/2017/
            // $SCRIPT_NAME: /index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

            // Result: $correction = 2;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:8888/index.php/blog/2017/01-24-screen-solusi-remote-connection.md?html
            // menghasilkan nilai variable sbb:
            // $base_url: /index.php
            // $base_path:
            // $path_info: /blog/2017/01-24-screen-solusi-remote-connection.md?html
            // $SCRIPT_NAME: /index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost/web

            // Result: $correction = 2;

            // cd /home/ijortengab/public_html/ijortengab.id.localhost
            // php -S localhost:9999 web/index.php

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/
            // menghasilkan nilai variable sbb:
            // $base_url:
            // $base_path:
            // $path_info: /
            // $SCRIPT_NAME: /
            // $SCRIPT_FILENAME: web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: INVALID REQUEST URI (MUST PREFIX WITH /web).

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/blog/
            // menghasilkan nilai variable sbb:
            // $base_url:
            // $base_path:
            // $path_info: /blog/
            // $SCRIPT_NAME: /blog/
            // $SCRIPT_FILENAME: web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: INVALID REQUEST URI (MUST PREFIX WITH /web).

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/blog/2017/
            // menghasilkan nilai variable sbb:
            // $base_url:
            // $base_path:
            // $path_info: /blog/2017/
            // $SCRIPT_NAME: /blog/2017/
            // $SCRIPT_FILENAME: web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: INVALID REQUEST URI (MUST PREFIX WITH /web).

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/blog/2017/01-24-screen-solusi-remote-connection.md?html
            // menghasilkan nilai variable sbb:
            // $base_url:
            // $base_path:
            // $path_info: /blog/2017/01-24-screen-solusi-remote-connection.md?html
            // $SCRIPT_NAME: /blog/2017/01-24-screen-solusi-remote-connection.md?html
            // $SCRIPT_FILENAME: web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: INVALID REQUEST URI (MUST PREFIX WITH /web).

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/web
            // menghasilkan nilai variable sbb:
            // $base_url:
            // $base_path:
            // $path_info: /web
            // $SCRIPT_NAME: /web/index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            if ($DOCUMENT_ROOT.$path_info.DIRECTORY_SEPARATOR.$script_php == $SCRIPT_FILENAME) {
                $base_path = $path_info;
                $path_info = '/';
                $rewrite_url = true;
                $correction = 6;
                break;
            }

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/web/
            // menghasilkan nilai variable sbb:
            // $base_url: /web
            // $base_path: /web
            // $path_info: /
            // $SCRIPT_NAME: /web/index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: $correction = 3;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/web/blog/
            // menghasilkan nilai variable sbb:
            // $base_url: /web
            // $base_path: /web
            // $path_info: /blog/
            // $SCRIPT_NAME: /web/index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: $correction = 3;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/web/blog/2017/
            // menghasilkan nilai variable sbb:
            // $base_url: /web
            // $base_path: /web
            // $path_info: /blog/2017/
            // $SCRIPT_NAME: /web/index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: $correction = 3;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/web/blog/2017/01-24-screen-solusi-remote-connection.md?html
            // menghasilkan nilai variable sbb:
            // $base_url:
            // $base_path:
            // $path_info: /web/blog/2017/01-24-screen-solusi-remote-connection.md?html
            // $SCRIPT_NAME: /web/blog/2017/01-24-screen-solusi-remote-connection.md?html
            // $SCRIPT_FILENAME: web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost
            if ($is_script_filename_not_correct) {
                $residu = substr($SCRIPT_FILENAME, 0, -1*(strlen($script_php)));
                if ($residu != '') {
                    $residu = '/'.rtrim($residu, '/');
                    if (str_starts_with($SCRIPT_NAME, $residu)) {
                        $path_info = substr($path_info, strlen($residu));
                        $base_path = $residu;
                        $rewrite_url = true;
                        $correction = 7;
                        // $base_path: /web
                        // $path_info: /blog/2017/01-24-screen-solusi-remote-connection.md
                    }
                    break;
                }
            }

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/web/index.php
            // menghasilkan nilai variable sbb:
            // $base_url: /web/index.php
            // $base_path: /web
            // $path_info: /
            // $SCRIPT_NAME: /web/index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: $correction = 4;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/web/index.php/
            // menghasilkan nilai variable sbb:
            // $base_url: /web/index.php
            // $base_path: /web
            // $path_info: /
            // $SCRIPT_NAME: /web/index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: $correction = 4;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/web/index.php/blog/
            // menghasilkan nilai variable sbb:
            // $base_url: /web/index.php
            // $base_path: /web
            // $path_info: /blog/
            // $SCRIPT_NAME: /web/index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: $correction = 4;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/web/index.php/blog/2017/
            // menghasilkan nilai variable sbb:
            // $base_url: /web/index.php
            // $base_path: /web
            // $path_info: /blog/2017/
            // $SCRIPT_NAME: /web/index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: $correction = 4;

            // Pada mesin dengan PHP_SAPI: cli-server, maka request sbb:
            // http://localhost:9999/web/index.php/blog/2017/01-24-screen-solusi-remote-connection.md?html
            // menghasilkan nilai variable sbb:
            // $base_url: /web/index.php
            // $base_path: /web
            // $path_info: /blog/2017/01-24-screen-solusi-remote-connection.md?html
            // $SCRIPT_NAME: /web/index.php
            // $SCRIPT_FILENAME: /home/ijortengab/public_html/ijortengab.id.localhost/web/index.php
            // $DOCUMENT_ROOT: /home/ijortengab/public_html/ijortengab.id.localhost

            // Result: $correction = 4;
        }
        while (false);
        if ($base_path != $base_path_before) {
            $this->basePath = $base_path;
        }
        if ($path_info != $path_info_before) {
            $this->pathInfo = $base_path;
        }
        self::$rewrite_url = $rewrite_url;
    }
}
