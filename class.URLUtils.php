<?PHP

/**
 * URLUtils
 *
 * @package default
 * @author Madhurendra Sachan
 * @copyright 2014 Madhurendra Sachan
 * @version v1.01
 * @access public
 */


/**
 * Class URLExtractor
 * Using method extract() you Extract all URLS from raw text.
 *
 *
 */
class URLExtractor
{

    /**
     *
     * Extracts all url from $raw
     *
     * @param $raw , Raw data from which urls are to be extracted
     * @param array $options ,
     *                  'delimiter' using which data is to separated.
     *                  'remove_duplicate' to remove duplicate links
     * @return array
     */
    public static function extract($raw, $options=array())
    {

        //Array of extracted urls
        $urls='';

        //Merge new array with default values.This will overwrite default values
        //extract : Declares all index as variables
        //removed extract because of name confliction
        $options=array_merge(array(
            'delimiter' => '',
            'remove_duplicate' => 1,
            'any_url'=>0),$options);

        //A good regex pattern
        $pattern='/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i';//use PREG_PATTERN_ORDER
        //Extract all matched
        preg_match_all ($pattern,$raw, $urls,PREG_PATTERN_ORDER);
        $urls=$urls[0];

        if ($options['remove_duplicate'] == "1")
            $urls = array_keys(array_flip($urls));

        $count = count($urls);

        if($options['delimiter']) $urls=implode($options['delimiter'],$urls);

        return array('urls' => $urls, 'total' => $count);

    }

}


/**
 * Class URLValidator
 */
class URLValidator{
    public static function validate($url){
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}

if(key_exists('example',$_GET)) {
///Example :
    $arr = (URLExtractor::extract($_POST['raw']));
    for ($i = 0; $i < count($arr['urls']); $i++)
        echo $arr['urls'][$i] . '<br/>';

    ?>
    <form method="post">
        <textarea name="raw"></textarea>
        <input type="submit" value="ok"/>
    </form>

<?PHP
}
    ?>