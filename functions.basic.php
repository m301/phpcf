<?PHP

/**
 * in_2darray()
 * 
 * checks if each element array of $src is in $dest
 * 
 * @param mixed array $src, array of elements to search for
 * @param mixed array $dest, a 2d array
 * @return boolean
 */
function in_2darray($src, $dest)
{
    foreach ($src as $text)
        if (!in_array($text, $dest)) return 0;
    return 1;
}

function strposa($haystack, $needle, $offset=0) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $query) {
        if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
    }
    return false;
}

function striposa($haystack, $needle, $offset=0) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $query) 
        if(stripos($haystack, $query, $offset) !== false) return true; // stop on first true result
    
    return false;
}


function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}