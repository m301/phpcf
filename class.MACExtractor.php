<?PHP

/**
 * URLExtractor
 *
 * @package default
 * @author Madhurendra Sachan
 * @copyright 2014 Madhurendra Sachan
 * @version v1.01
 * @access public
 */
class URLExtractor
{
    /**
     *
     * Extracts all url from $raw
     *
     * @param string $raw, Raw data from which urls are to be extracted
     * @param array $options,
     *                  'delimiter' using which data is to separated.
     *                  'remove_duplicate' to remove duplicate links
     * @return array
     */
    public static function extract($raw, $options=array())
    {
        //Merge new array with default values.This will overwrite default values
        //extract Declares all index as variables
        extract(array_merge(array(
            'delimiter' => '<br/>',
            'remove_duplicate' => 1,
            'any_url'=>0),$options));



        if ($delimiter == "" || $delimiter == " ")
            $delimiter = ";";

        //check if use any URL is allowed
        //if($any_url)
        //    $pattern='#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#';
        //else
        //    $pattern='#(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#';

        //$pattern='@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@';

        $pattern='/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i';//use PREG_PATTERN_ORDER
        //match all
        preg_match_all ($pattern,$raw, $emails,PREG_PATTERN_ORDER);
        $emails=$emails[0];


        if ($remove_duplicate == "1")
            $count = self::removeDuplicate($emails);
        else
            $count = count($emails);

        echo(implode($delimiter,$emails));

        return array('emails' => $emails, 'total' => $count);
    }


    /**
     *
     * returns total number of unique urls extracted from raw to raw using specified delimiter
     *
     * @param mixed $raw, raw data from which duplicate data is to be removed
     * @return integer, total numbers or unique urls
     */
    private static function removeDuplicate(&$raw)
    {
        $matches = $raw;
        $raw=array();
        $count=0;
        //iterate through all variables & remove duplicate
        for ($i = 0; $i < count($matches); $i++) {
            $exist = 0;
            if (strlen($matches[$i]) > 3) {
                for ($x = 0; $x < $i; $x++)
                    if ($matches[$x] == $matches[$i]) $exist++;
                if ($exist == 0) {
                    $raw[]= $matches[$i];
                    $count++;
                }

            }
        }
        return $count;
    }

}