<?PHP
/**
 * MobileExtractor
 * 
 * @package default
 * @author Madhurendra Sachan
 * @copyright 2014 Madhurendra Sachan
 * @version v1.01
 * @access public
 */
class MobileExtractor
{
    /**
     * MobileExtractor::extract()
     * 
     * @param mixed $raw, raw data from which mobile numbers need to be extracted
     * @param string $delimiter, delimiter to use between each number
     * @param integer $remove_duplicate, 0 for false,1 for true
     * @param string $code, country code which will be concatenated before each number
     * @return array('numbers' => array of mobile numbers, 'total' => total numbers of mobile)
     */
    public static function extract($raw, $delimiter = '\n', $remove_duplicate = 1, $code = '91')
    {

        $numbers = "";
        if ($delimiter == "" || $delimiter == " ") $delimiter = ";";

        $pattern = '/91[9|8|7][0-9]{9}/';
        $numbers = self::phoneExtract($raw, $pattern, $delimiter, 2, $code);
        $pattern = '/0[9|8|7][0-9]{9}/';
        $numbers .= self::phoneExtract($raw, $pattern, $delimiter, 1, $code);
        $pattern = '/[9|8|7][0-9]{9}/';
        $numbers .= self::phoneExtract($raw, $pattern, $delimiter, 0, $code);

        $numbers=array_unique(explode($delimiter,$numbers));
        $count = count($numbers);
        return array('numbers' => $numbers, 'total' => $count);
    }


    /**
     * MobileExtractor::phoneExtract()
     *
     * Returns mobile numbers of given pattern extracted from raw string & remove those numbers from $raw
     *
     * @param $raw
     * @param string $pattern , a valid regex patter
     * @param string $delimiter , delimiter to use to seperate extracted numbers
     * @param integer $charTrim , characters to remove from beginning of each number
     * @param string $before , string to concatenate at beginning of each number
     * @internal param string $refrence $raw, raw string with data to be extracted
     * @return string
     */
    private static function phoneExtract(&$raw, $pattern, $delimiter, $charTrim, $before = '')
    {
        preg_match_all($pattern, $raw, $matches);
        $raw = preg_replace($pattern, " ", $raw);

        $numbers = '';
        for ($i = 0; $i < count($matches[0]); $i++) {
            $matches[0][$i] = substr($matches[0][$i], $charTrim);
            $numbers .= $before . $matches[0][$i] . $delimiter;
        }
        return $numbers;
    }

}
