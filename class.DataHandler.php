<?PHP

/**
 * DataHandler
 *
 * @package default
 * @author Madhurendra Sachan
 * @copyright 2014 Madhurendra Sachan
 * @version v1.02
 */
class DataHandler
{
    /**
     * getContent()
     *
     * fetches a url & returns the data/html
     *
     * @param mixed $url
     * @param array $options
     * @return string
     * @throws Exception
     */
    public static function getContent($url, $options = array(),$retry = 0 )
    {
        //echo $url;
        $retry++;

		$ch = curl_init();

		$options = array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER => false,
				// CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0",
				CURLOPT_AUTOREFERER => true,
				CURLOPT_CONNECTTIMEOUT => 120,
				CURLOPT_TIMEOUT => 120,
				CURLOPT_MAXREDIRS => 10) + $options;

		curl_setopt_array($ch, $options);
		
		 do{
			$result = curl_exec($ch);

			//If there is any error & this is last try i.e. retry=1 then throw exception
			if ($c_error_no = curl_errno($ch)){
				if($retry<=1)
					throw new Exception("[$c_error_no]" . curl_error($ch));
			}else{
			//if no error set retry to 1;
				$retry = 1;
			}

			curl_close($ch);			
		}while($retry--);
		 
        return $result;
    }

    /**
     * postContent()
     *
     * makes a post request on given url with given data & returns the response
     *
     * @param mixed $url
     * @param mixed $data ,data to attach in post request
     * @param array $options
     * @return string
     * @throws Exception
     */
    public static function postContent($url, $data, $options = array())
    {
        $ch = curl_init();
        $options = array(
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
                CURLOPT_POSTFIELDS => $data) + $options;
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        if ($c_error_no = curl_errno($ch))
            throw new Exception("[$c_error_no]" . curl_error($ch));


        curl_close($ch);
        return $result;
    }

    public static function downloadFile($url, $filepath)
    {
        file_put_contents($filepath, fopen($url, 'r'));
    }
}
