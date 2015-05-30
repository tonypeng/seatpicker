<?php

function join_paths()
{
    $paths = array();

    foreach (func_get_args() as $arg) {
        if ($arg !== '') { $paths[] = $arg; }
    }

    return preg_replace('#/+#','/', join('/', $paths));
}

function linkto($link)
{
    return join_paths('/', SITE_ROOT, $link);
}

function fq_linkto($link)
{
    return SITE_URL . join_paths('/', SITE_ROOT, $link);
}

function makelink($link, $title, $dangerousAttrInjection='')
{
    $path = linkto($link);

    return '<a href="'.$path.'"'.$dangerousAttrInjection.'>'.$title.'</a>';
}

function esco($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

class DatabaseException extends Exception { }