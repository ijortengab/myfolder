<?php
$project_directory=__DIR__;
$target_directory = '/mnt/c/cygwin64/home/IjorTengab/github.com/ijortengab/rcm';
// $target_directory = '/mnt/c/Windows/System32/drivers';
// $target_directory=__DIR__;
// $target_directory=$_SERVER['HOME'];
rtrim($target_directory, '/');
// Based on Symfony Request version 2.8.18.
class Request {
    protected $server;
    protected $requestUri;
    protected $basePath;
    protected $pathInfo;
    protected $baseUrl;
    public function __construct()
    {
        $this->server = $_SERVER;
    }
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }
        return $this->pathInfo;
    }
    public function getBasePath()
    {
        if (null === $this->basePath) {
            $this->basePath = $this->prepareBasePath();
        }
        return $this->basePath;
    }
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->prepareBaseUrl();
        }
        return $this->baseUrl;
    }
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }
    public function getPort()
    {
        if (!$host = isset($this->server['HTTP_HOST']) ? $this->server['HTTP_HOST'] : null) {
            return $this->server['SERVER_PORT'];
        }
        if ($host[0] === '[') {
            $pos = strpos($host, ':', strrpos($host, ']'));
        } else {
            $pos = strrpos($host, ':');
        }
        if (false !== $pos) {
            return (int) substr($host, $pos + 1);
        }
        return 'https' === $this->getScheme() ? 443 : 80;
    }
    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();
        if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
            return $this->getHost();
        }
        return $this->getHost().':'.$port;
    }
    public function getRequestUri()
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }
        return $this->requestUri;
    }
    public function getSchemeAndHttpHost()
    {
        return $this->getScheme().'://'.$this->getHttpHost();
    }
    public function isSecure()
    {
        $https = isset($this->server['HTTPS']) ? $this->server['HTTPS'] : null;
        return !empty($https) && 'off' !== strtolower($https);
    }
    public function getHost()
    {
        if (!$host = isset($this->server['HTTP_HOST']) ? $this->server['HTTP_HOST'] : null) {
            if (!$host = isset($this->server['SERVER_NAME']) ? $this->server['SERVER_NAME'] : null) {
                $host = isset($this->server['SERVER_ADDR']) ? $this->server['SERVER_ADDR'] : null;
            }
        }
        // trim and remove port number from host
        // host is lowercase as per RFC 952/2181
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));
        // as the host can come from the user (HTTP_HOST and depending on the configuration, SERVER_NAME too can come from the user)
        // check that it does not contain forbidden characters (see RFC 952 and RFC 2181)
        // use preg_replace() instead of preg_match() to prevent DoS attacks with long host names
        if ($host && '' !== preg_replace('/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/', '', $host)) {
            return '';
        }
        return $host;
    }
    protected function prepareRequestUri()
    {
        $requestUri = '';
        if (isset($this->server['HTTP_X_ORIGINAL_URL'])) {
            // IIS with Microsoft Rewrite Module
            $requestUri = $this->server['HTTP_X_ORIGINAL_URL'];
            unset($this->server['HTTP_X_ORIGINAL_URL']);
            unset($this->server['UNENCODED_URL']);
            unset($this->server['IIS_WasUrlRewritten']);
        } elseif (isset($this->server['HTTP_X_REWRITE_URL'])) {
            // IIS with ISAPI_Rewrite
            $requestUri = $this->server['HTTP_X_REWRITE_URL'];
            unset($this->server['HTTP_X_REWRITE_URL']);
        } elseif (
            isset($this->server['IIS_WasUrlRewritten']) &&
            $this->server['IIS_WasUrlRewritten'] == '1' &&
            isset($this->server['UNENCODED_URL']) &&
            $this->server['UNENCODED_URL'] != ''
        ) {
            // IIS7 with URL Rewrite: make sure we get the unencoded URL (double slash problem)
            $requestUri = $this->server['UNENCODED_URL'];
            unset($this->server['UNENCODED_URL']);
            unset($this->server['IIS_WasUrlRewritten']);
        } elseif (isset($this->server['REQUEST_URI'])) {
            $requestUri = $this->server['REQUEST_URI'];
            // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path, only use URL path
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif (isset($this->server['ORIG_PATH_INFO'])) {
            // IIS 5.0, PHP as CGI
            $requestUri = $this->server['ORIG_PATH_INFO'];
            if (isset($this->server['QUERY_STRING']) && '' != $this->server['QUERY_STRING']) {
                $requestUri .= '?'.$this->server['QUERY_STRING'];
            }
            unset($this->server['ORIG_PATH_INFO']);
        }
        // normalize the request URI to ease creating sub-requests from this request
        $this->server['REQUEST_URI'] = $requestUri;
        return $requestUri;
    }
    protected function prepareBaseUrl()
    {
        $filename = basename($this->server['SCRIPT_FILENAME']);
        if (basename($this->server['SCRIPT_NAME']) === $filename) {
            $baseUrl = $this->server['SCRIPT_NAME'];
        } elseif (basename($this->server['PHP_SELF']) === $filename) {
            $baseUrl = $this->server['PHP_SELF'];
        } elseif (basename($this->server['ORIG_SCRIPT_NAME']) === $filename) {
            $baseUrl = $this->server['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
        } else {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path = isset($this->server['PHP_SELF']) ? $this->server['PHP_SELF'] : '';
            $file = isset($this->server['SCRIPT_FILENAME']) ? $this->server['SCRIPT_FILENAME'] : '';
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos);
        }
        // Does the baseUrl have anything in common with the request_uri?
        $requestUri = $this->getRequestUri();
        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) {
            // full $baseUrl matches
            return $prefix;
        }
        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, rtrim(dirname($baseUrl), '/'.DIRECTORY_SEPARATOR).'/')) {
            // directory portion of $baseUrl matches
            return rtrim($prefix, '/'.DIRECTORY_SEPARATOR);
        }
        $truncatedRequestUri = $requestUri;
        if (false !== $pos = strpos($requestUri, '?')) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }
        $basename = basename($baseUrl);
        if (empty($basename) || !strpos(rawurldecode($truncatedRequestUri), $basename)) {
            // no match whatsoever; set it blank
            return '';
        }
        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of baseUrl. $pos !== 0 makes sure it is not matching a value
        // from PATH_INFO or QUERY_STRING
        if (strlen($requestUri) >= strlen($baseUrl) && (false !== $pos = strpos($requestUri, $baseUrl)) && $pos !== 0) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }
        return rtrim($baseUrl, '/'.DIRECTORY_SEPARATOR);
    }
    protected function prepareBasePath()
    {
        $filename = basename($this->server['SCRIPT_FILENAME']);
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }
        if (basename($baseUrl) === $filename) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }
        if ('\\' === DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }
        return rtrim($basePath, '/');
    }
    protected function preparePathInfo()
    {
        $baseUrl = $this->getBaseUrl();
        if (null === ($requestUri = $this->getRequestUri())) {
            return '/';
        }
        // Remove the query string from REQUEST_URI
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        $pathInfo = substr($requestUri, strlen($baseUrl));
        if (null !== $baseUrl && (false === $pathInfo || '' === $pathInfo)) {
            // If substr() returns false then PATH_INFO is set to an empty string
            return '/';
        } elseif (null === $baseUrl) {
            return $requestUri;
        }
        return (string) $pathInfo;
    }
    private function getUrlencodedPrefix($string, $prefix)
    {
        if (0 !== strpos(rawurldecode($string), $prefix)) {
            return false;
        }
        $len = strlen($prefix);
        if (preg_match(sprintf('#^(%%[[:xdigit:]]{2}|.){%d}#', $len), $string, $match)) {
            return $match[0];
        }
        return false;
    }
}

// https://github.com/symfony/symfony/blob/2.8/src/Symfony/Component/HttpFoundation/Response.php

class Response {

}
class Application {

}
$request = new \Request;
$path_info = $request->getPathInfo();
$base_path = $request->getBasePath();
// $app = new \Application($request,);
// $app->post();
// $app->get('/', function(){
	// return new \Response("Hello world");
// });
// $app->run();

if (is_dir($target_directory.$path_info)) {
    if (substr($path_info, -1) != '/') {
        header('Location: ' . $base_path.$path_info.'/');
        exit;
    }
}
if (is_file($target_directory.$path_info)) {
    $file = $target_directory.$path_info;
    header('Content-Type: ' . mime_content_type($file));
    readfile($file);
    exit;
}

// $debugname = 'path_info'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)){ $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') {        echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; }
// $debugname = 'base_path'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)){ $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') {        echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; }
// die('a');
$config = [
    'path_info' => $request->getPathInfo(),
    'base_path' => $request->getBasePath(),
];
$config_json = json_encode($config);
if (isset($_POST['action']) && $_POST['action'] == 'ls -la') {
    // $current_directory=$target_directory.$path_info;
    $current_directory=$target_directory.$_POST['directory'];
    // $debugname = 'current_directory'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)){ $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') {        echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; }

    $ls = scandir($current_directory);

    $ls = array_diff($ls, ['.','..']);
    // $debugname = 'ls'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)){ $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') {        echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; }
    $ls_la = [];
    foreach ($ls as $each) {
        $_ls_la = [
            'name' => $each,
            'mtime' => '',
            'size' => '',
            'type' => '.', // dot means directory
        ];
        if (is_file($current_directory.$each)) {
            $file = $current_directory.$each;
            $_ls_la['mtime'] = filemtime($file);
            $_ls_la['size'] = filesize($file);
            $_ls_la['type'] = pathinfo($file, PATHINFO_EXTENSION);
        }
        $ls_la[] = $_ls_la;
    }
    header("Content-Type: application/json");
    $ls_la_json = json_encode($ls_la);
    echo $ls_la_json;
    exit;
}
if (isset($_POST['action']) && $_POST['action'] == 'ls') {
    $current_directory=$target_directory.$_POST['directory'];
    $list_directory = scandir($current_directory);
    $list_directory = array_diff($list_directory, ['.','..']);
    $list_directory = array_values($list_directory);
    $list_directory_json = json_encode($list_directory);
    header("Content-Type: application/json");
    echo $list_directory_json;
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<!-- <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css"> -->
</head >
<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">MyFolder</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Dropdown
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Action</a></li>
              <li><a class="dropdown-item" href="#">Another action</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled">Disabled</a>
          </li>
        </ul>
        <form class="d-flex" role="search">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
      </div>
    </div>
  </nav>
<table id="table-main" class="table" data-toggle="table" data-search="true">
  <thead>
    <tr>
      <th scope="col" data-field="id">#</th>
      <th scope="col" data-field="name">Name</th>
      <th scope="col" data-field="date-modified">Date Modified</th>
      <th scope="col" data-field="date-modified">Type</th>
      <th scope="col" data-field="date-modified">Size</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">&nbsp;</th>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
</table>
<?php
// $a = scandir($target_directory);
// $debugname = 'project_directory'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)){ $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') {        echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; }
// $debugname = 'target_directory'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)){ $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') {        echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; }
// $debugname = 'a'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)){ $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') {        echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; }
?>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>
<!-- <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script> -->
<script>
config=JSON.parse('<?=$config_json?>')
url=config.base_path+config.path_info
var data = {
    action: 'ls',
    directory: config.path_info
}
function ls() {
    // url=config.base_path+config.path_info
    // console.log(url);
    var data = {
        action: 'ls',
        directory: config.path_info
    }
    var jqxhr = $.ajax({
      type: "POST",
      url: url,
      data: data
    });
    jqxhr.done(function (data) {
        drawColumnName(data).then(drawColumnOther)
    });

}
console.log(config);
var jqxhr = $.ajax({
  type: "POST",
  url: url,
  data: data
});

function tempe(event) {
    // console.log(this);
    // console.log(e);
    // console.log(this);
    $this = $(this);
    if ($this.data('type') == '.') {
        event.preventDefault();
        console.log(config.path_info);
        var name = $this.data('info').name
        console.log(this);
        config.path_info = config.path_info + name + '/'
        console.log(config);
        ls();
        // history.replaceState({}, "", name + '/');
        history.pushState({path_info: config.path_info}, "", name + '/');

        // const url = new URL(location);
// url.searchParams.set("foo", "bar");
// history.pushState({}, "", url);

    }

}

addEventListener("popstate", (event) => {});
onpopstate = (event) => {
    console.log(event);
    console.log(this);
    console.log('ok');
    var path_info = event.state.path_info;
    if (typeof path_info == 'undefined') {
        config.path_info = '/'
    }
    else {
        config.path_info = path_info
    }
    console.log(path_info);
    ls()
};

function drawColumnName(data) {
    var defer = $.Deferred();
    console.log('drawColumnName()');
    $table = $('#table-main');
    $tbody = $table.find('tbody').empty();
    for (i in data) {
        var $tr = $('<tr></tr>').data('info',data[i]).html('<th scope="row"></th>').appendTo($tbody);
        // var $tr = $('<tr><th scope="row"></th></tr>').appendTo($tbody);
        var $td = $('<td></td>').appendTo($tr);
        var href = config.base_path+config.path_info+data[i]
        var $a = $('<a></a>').on('click',tempe).text(data[i]).attr('href',href).appendTo($td);
        $('<td class="mtime"></td><td class="type"></td><td class="size"></td>').appendTo($tr);
    }
    console.log('slow 1 detik bosque');
    // setTimeout(function () {
        defer.resolve();
    // }, 1000);
    return defer;
}

function drawColumnOther() {
    console.log('drawColumnOther()');
    var data = {
        action: 'ls -la',
        directory: config.path_info
    }
    var jqxhr = $.ajax({
      type: "POST",
      url: url,
      data: data
    });

    function checkAge(age) {
        console.log(age);
    }

    jqxhr.done(function (data) {

    $table = $('#table-main');
    $tbody = $table.find('tbody');
        for (i in data) {
            var info = data[i]
            var ehm = $tbody.find('tr').filter(function (i) {
                var $this = $(this);
                if ($this.data('info') == info.name) {
                    // console.log(info.name);
                    // console.log($this);
                    $this.find("td.mtime").text(info.mtime)
                    $this.find("td.size").text(info.size)
                    if (info.type == '.') {
                        $this.find("td.type").text('File folder')
                        $a = $this.find("td > a");
                        var href = $a.attr('href');
                        $a.attr('href', href+'/');
                        $a.data('info',info);
                        $a.data('type',info.type);
                    }
                    else {
                        $this.find("td.type").text(info.type)

                    }
                }
            });//.data(info.name);
            // console.log(ehm);

            // var $tr = $('<tr><th scope="row"></th></tr>').appendTo($tbody);
            // var $td = $('<td></td>').appendTo($tr);
            // var href = config.base_path+config.path_info+data[i]
            // var $a = $('<a></a>').text(data[i]).attr('href',href).appendTo($td);
            // $('<td></td><td></td><td></td>').appendTo($tr);
        }

    });
}
jqxhr.done(function (data) {
    drawColumnName(data).then(drawColumnOther)
});
$('a.navbar-brand').attr('href',config.base_path);

history.pushState({ name: "Example" }, "pushState example", "");
</script>
</body>
</html>
