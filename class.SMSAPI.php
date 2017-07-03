<?PHP
include_once ("config.php");
include_once ("class.DataHandler.php");
include_once ("class.DNDCheck.php");
include_once ("class.MobileExtractor.php");

    // TODO: Check to validate username & password
    // TODO: Send SMS in parts,large mobile database
    // TODO: Break Long SMS in parts
    // TODO: Check Balance
/**
 * SMSAPI
 * 
 * @package default
 * @author Madhurendra Sachan
 * @copyright 2013 Madhurendra Sachan
 * @version v1.01
 * @access public
 */
class SMSAPI
{

    /**
     * SMSAPI::sendSMS()
     * 
     * Sends SMS from different API's
     * 
     * Response array is described below:
     * Element name | Description
     * sent         | 0 if not sent,1 if sent according to server response, This response is not based on indiviadual numbers,
     *                it is just according to response from server,it may state that numbers are only submitted.
     * total        | total mobile numbers
     * response     | response of server
     * message      | message which was submitted to server
     * numbers      | string conating mobile numbers,delimiter can be according to server
     * gateway      | integer number of gateway used
     * gateway_name | name of gateway used
     * response_code| 0 : everything went well
     *                1 : invalid gateway number
     *                2 : nothing data in to field
     *                3 : Time not favorable
     * 
     * @param string $to,reciever mobile numbers in any format
     * @param string $msg, message to send
     * @param integer $gateway, password of the id.Supported Gateway:KAPSYSTEM=>1,TXTGURU=>0
     * @param int $dnd, to check for dnd & remove dnd ones
     * @param string $usr, username of site txtguru.in
     * @param string $pwd, password of the id.
     * @param string $senderId, sender id
     * @return array()
     */

    public static function sendSMS($to, $msg, $gateway = 0, $dnd = 0,$usr = "madsacsoft", $pwd = "19mk65mad", $senderId = "MADSAC")
    {   
        if(is_array($to))$to=implode(',',$to);
        if (strlen($to)<10) return array('response_code' => 2);
        if ($gateway < 0 || $gateway > 1) return array('response_code' => 1);
        elseif ($gateway == 0) $gateway_name = 'TXTGURU';
        elseif ($gateway == 1) $gateway_name = 'KAPSYSTEM';
        
        
        $msg = urlencode($msg);
        $sent = 0;
        $response = '';

        if (date('H:i:s') > date('H:i:s', strtotime('09:00:00')) && date('H:i:s') < date('H:i:s', strtotime('20:30:00'))) {

            if ($gateway == 0) {
                $mobiles = MobileExtractor::extract($to, ',', 1, $code = '91');
                if($dnd) $mobiles['numbers']=DNDCheck::checkMobiles($mobiles['numbers'],',');
                $response = self::sendSMS_TXTGURU((($dnd)?$mobiles['numbers']['not_registered']:$mobiles['numbers']), $msg, $usr, $pwd);
                $sent = (stristr($response, 'QUERY FAILED')) ? 0 : 1;
            } elseif ($gateway == 1) {
                $mobiles = MobileExtractor::extract($to, ',', 1, $code = '91');
                if($dnd) $mobiles['numbers']=DNDCheck::checkMobiles($mobiles['numbers'],',');
                $response = self::sendSMS_KAPSYSTEM((($dnd)?$mobiles['numbers']['not_registered']:$mobiles['numbers']), $msg, $usr, $pwd);
                $sent = 1;
            }

        }else{
            return array('response_code' => 3);
        }
        return array(
            'sent' => $sent,
            'total' => $mobiles['total'],
            'response' => $response,
            'message' => urldecode($msg),
            'numbers' => $mobiles['numbers'],
            'gateway' => $gateway,
            'gateway_name' => $gateway_name,
            'response_code' => 0);
        /*
        response_code
        0 => request done to server
        1 => invalid gateway
        */
    }
    
    //Limit : 100 SMS
    private static function sendSMS_TXTGURU($to, $msg, $usr, $pwd)
    {   
        if(strlen($to)>9)
        return DataHandler::postContent("http://www.txtguru.in/imobile/api.php?", "username=" . $usr . "&password=" . $pwd . "&source=MADSAC&dmobile=" . $to . "&message=" . $msg);
        return 'QUERY FAILED';
    }
    //Limit : 300 SMS
    private static function sendSMS_KAPSYSTEM($to, $msg, $usr, $pwd)
    {
        if(strlen($to)>9)
        return DataHandler::getContent("http://203.129.203.254/sms/user/urlsms.php?" . "username=" . $usr . "&pass=" . $pwd . "&senderid=060000&message=" . $msg . "&dest_mobileno=" . $to . "&response=N");
        return 0;
    }
}
//print_r(SMSAPI::sendSMS('8888888888', 'hi....',0,1));