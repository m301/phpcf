<?PHP

/**
 * Class GCM
 *
 * This class makes it easy to send push notification on android via GCM
 *
 */
class GCM{
    private static $API_KEYS=array("WAKEME"=>"AIzaSyClfeV2NB6y3jqQuyi03i9QflXeGD1I3-s");
    public static function sendPush($registrationIDs,$data,$api_key="WAKEME")
    {
        if(strlen($api_key)<20)
            $api_key=self::$API_KEYS[$api_key];

        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => $registrationIDs,
            'data' => $data,
        );

        $headers = array('Authorization: key=' . $api_key,
            'Content-Type: application/json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


}

//GCM::sendPush(array("APA91bGgpsGid2DO7x4JL1PKbh-liLwkkpCzSaZOpvZoBgTRGvo3p4JBw1B311o2qPSSzJe-boQb7HJr4Xl67_p19_J52iKi3UQEknOa6EQMyjFrUV0aT8AyKkUNRJjV4l7AEhoQbx5UWwUWsdhTDIBaYENwR3XEwQ"),array('message'=>'Hello from madhurendra'));
