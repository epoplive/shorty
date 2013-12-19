<?php
require_once('../src/Shorty/Shorty.php');
$shorty = new Shorty\Shorty();

function full_url($s)
{
    $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
    $sp = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port = $s['SERVER_PORT'];
    $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
    $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : $s['SERVER_NAME'];
    $path = explode('?', $s['REQUEST_URI'])[0];
    return $protocol . '://' . $host . $port.$path;// . $s['REQUEST_URI'];
}

if(array_key_exists('q', $_GET)){
    $shorty->redirect($_GET['q']);
} else {
    include_once('shortener.php');
}