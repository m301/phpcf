<?PHP
/**
 * DataHandler
 * 
 * @package default
 * @author Madhurendra Sachan
 * @copyright 2014 Madhurendra Sachan
 * @version v1.01
 * @access public
 */
class DataHandler
{
    /**
     * getContent()
     * 
     * fetches a url & returns the data/html
     * 
     * @param mixed $url
     * @return string
     */
    public static function getContent($url)
    {
        $ch = curl_init();
        curl_setopt_array($ch,array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
           // CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10));

        $result= curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * postContent()
     * 
     * makes a post request on given url with given data & returns the response
     * 
     * @param mixed $url
     * @param mixed $data,data to attach in post request
     * @return string
     */
    public static function postContent($url, $data)
    {
        $ch = curl_init();
        curl_setopt_array($ch,array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
          //  CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data));
        $result= curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function downloadFile($url,$filepath){
        file_put_contents($filepath, fopen($url, 'r'));
    }
}
