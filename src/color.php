<?php

function _say($msg = '', $newLine = true, $pad = 0, $color = COL_WHITE, $alignRight = false)
{
    if (defined('SILENT'))
    {
        return;
    }

    echo
        $color,
        ($pad > 0 ? str_pad($msg, $pad, ' ', ($alignRight ? STR_PAD_LEFT : STR_PAD_RIGHT)) : $msg),
        COL_RESET;

    if ($newLine)
    {
        echo "\n";
    }
    flush();
}

function _green($msg = '', $newLine = true, $pad = 0, $alignRight = false)
{
    _say($msg, $newLine, $pad, COL_GREEN, $alignRight);
}

function _red($msg = '', $newLine = true, $pad = 0, $alignRight = false)
{
    _say($msg, $newLine, $pad, COL_RED, $alignRight);
}

function _blue($msg = '', $newLine = true, $pad = 0, $alignRight = false)
{
    _say($msg, $newLine, $pad, COL_BLUE, $alignRight);
}

function _yellow($msg = '', $newLine = true, $pad = 0, $alignRight = false)
{
    _say($msg, $newLine, $pad, COL_YELLOW, $alignRight);
}

function _error($msg)
{
    file_put_contents('php://stderr', "$msg\n");
    flush();
}

function _repeat($msg = '=', $pad = 5, $newLine = true, $color = null)
{
    echo
        (!is_null($color) ? $color : ''),
        str_repeat($msg, $pad),
        COL_RESET;

        if ($newLine)
        {
            echo "\n";
        }
        flush();
}

function _read_data($keys)
{
    $res = array();
    foreach ($keys as $k)
    {
        _say("Enter $k:");
        $res[$k] = readline();
    }
    return $res;
}

function lock_or_die($id)
{
    $filename = dirname(__FILE__).'/locks/'.$id.'.lock';

    $f = fopen($filename, 'w');
    if (!$f) {
        locking_error($filename, 'open');
    }

    /* keep reference to lock file,
       otherwise at the end of this function file unlocks */
    $GLOBALS['lock_file_handles'][] = $f;

    $l = flock($f, LOCK_EX | LOCK_NB);
    if (!$l) {
        locking_error($filename, 'lock');
    }
}

function unlock_all($id) {
    foreach ($GLOBALS['lock_file_handles'] as $f) {
        fclose($f);
    }
}

function locking_error($filename, $operation)
{
    _error("Could not $operation $filename.");
    die();
}

function set_resource_limits($time, $memory)
{
    set_time_limit($time);
    ini_set('memory_limit', $memory);
}

function remove_resource_limits()
{
    set_time_limit(0);
    ini_set('memory_limit', -1);
}

function am_i_root()
{
    if (get_os() == 'win')
    {
        return true;
    }
    $output = shell_exec('id -u');
    return trim($output) == '0';
}

function get_os()
{
    static $os;

    if (!$os)
    {
        if (preg_match('/win/i', PHP_OS))
        {
            $os = 'win';
        }
        else if (file_exists('/etc/redhat_version') || file_exists('/etc/redhat-release'))
        {
            $os = 'redhat';
        }
        else if (file_exists('/etc/debian_version') || file_exists('/etc/debian_release'))
        {
            $os = 'debian';
        }
        else if (file_exists('/etc/SUSE-release') || file_exists('/etc/SuSE-release'))
        {
            $os = 'suse';
        }
        else
        {
            $os = 'unknown';
        }
    }
    return $os;
}

function get_apache_user()
{
    static $user;

    if (!$user)
    {
        switch (get_os())
        {
            case 'redhat':
                $user = 'apache';
                break;
            case 'debian':
                $user = 'www-data';
                break;
            case 'suse':
                $user = 'wwwrun';
                break;
        }
    }
    return $user;
}

function getRequiredArgument($args, $key, $infoMessage)
{
    if (!isset($args[$key])) {
        _red($infoMessage);
        exit;
    }

    return $args[$key];
}

function getArgument($args, $key, $default = null)
{
    if (!isset($args[$key])) {
        $args[$key] = $default;
    }

    return $args[$key];
}

if (!function_exists('readline'))
{
    function readline()
    {
        return trim(fgets(STDIN));
    }
}

if (!function_exists('readline_add_history'))
{
    function readline_add_history()
    {
        // dummy
    }
}

if (get_os() == 'win' || isset($argv) && in_array('-nc', $argv))
{
    define('ESC_SEQ',    '');
    define('COL_RESET',  '');
    define('COL_WHITE',  '');
    define('COL_RED',    '');
    define('COL_GREEN',  '');
    define('COL_YELLOW', '');
    define('COL_BLUE',   '');
    define('COL_CYAN',   '');
}
else
{
    define('ESC_SEQ',    "\x1b[");
    define('COL_RESET',  ESC_SEQ . '39;49;00m');
    define('COL_WHITE',  ESC_SEQ . '37;01m');
    define('COL_RED',    ESC_SEQ . '31;01m');
    define('COL_GREEN',  ESC_SEQ . '32;01m');
    define('COL_YELLOW', ESC_SEQ . '33;01m');
    define('COL_BLUE',   ESC_SEQ . '34;01m');
    define('COL_CYAN',   ESC_SEQ . '35;01m');
}
