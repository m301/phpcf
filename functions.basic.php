<?PHP

function in_2darray($src, $dest)
{
    foreach ($src as $text)
        if (!in_array($text, $dest)) return 0;
    return 1;
}

function strposa($haystack, $needle, $offset = 0)
{
    if (!is_array($needle)) $needle = array($needle);
    foreach ($needle as $query) {
        if (strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
    }
    return false;
}

function striposa($haystack, $needle, $offset = 0)
{
    if (!is_array($needle)) $needle = array($needle);
    foreach ($needle as $query)
        if (stripos($haystack, $query, $offset) !== false) return true; // stop on first true result

    return false;
}


function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


function isDefault($data)
{
    return ($data == '' | $data == null);
}

function getArrayedError($code, $message)
{
    return (array("error" => array("code" => $code, "message" => $message)));
}

function getJSONedError($code, $message)
{
    return json_encode(getArrayedError($code, $message));
}

function isValidUsername($str)
{
    return preg_match('/^[a-zA-Z0-9_]+$/', $str);
}