<?PHP
include_once ("config.php");
include_once ("class.DataHandler.php");

/**
 * DNDCheck
 *  Check an indian number for DND service.
 * @package default
 * @author Madhurendra Sachan
 * @copyright 2013 Madhurendra Sachan
 * @version v1.01
 * @access public
 */
class DNDCheck
{
    /**
     * DNDCheck::isRegistered()
     * 
     * Checks the passed number in DND registry database & returns following code 
     * Code | Meaning
     * -1   | Not registered
     * -2   | Unknown Response,other than known string 'The number is not registered in NCPR','The number is registered in NCPR'
     * -3   | Invalid mobile number,Computed using length of mobile number
     * -4   | Length of mobile number is 0;
     * >=0  | Registered in DND
     * 
     * @param mixed $number,mobile number to check for DND,it can be +91XXXXXXXXXX,91XXXXXXXXXX,0XXXXXXXXXX,XXXXXXXXXX
     * @return integer
     */
    public static function isRegistered($number)
    {
        $number = trim($number);
        $len = strlen($number);
        if($len==0) return -4;
        if ($len > 13 || $len < 10 ) return -3;
        if ($len != 10) $number = substr($number, $len-10);
        $response = DataHandler::postContent('http://nccptrai.gov.in/nccpregistry/saveSearchSub.misc', 'phoneno=' . $number);

        if (stristr($response, 'The number is not registered in NCPR')) return -1;
        elseif (stristr($response, 'The number is registered in NCPR')) return 0;
        else  return -2;
    }
    
    
    /**
     * DNDCheck::checkMobiles()
     * 
     * Checks a collection of mobile numbers for registery in DND & returns an array of response 
     * response can be a string or array according to input,if string then given delimiter delimited else array conating those numbers
     * Response array element | meaning 
     * registered             | list of numbers registered in DND
     * not_registered         | list of numbers Not registered in DND
     * invalid                | list of invalid mobile numbers
     * unknown_response       | list of numbers whose responses were unknown to me,may rarely conatin any number
     * 
     *
     * @example DNDCheck::checkMobiles('919876543210,9876543,9876543210,09876543210,+919876543210')
     * @example DNDCheck::checkMobiles('919876543210;9876543;9876543210;09876543210;+919876543210',';')
     * @example DNDCheck::checkMobiles(array('919876543210','9876543','9876543210','09876543210','+919876543210'))
     * 
     * @param string $numbers,can be an array of mobile numbers or a string of mobile numbers seperated by a delimiter
     * @param string $delimiter,if string then a delimiter,default:','
     * @return array()
     */
    public static function checkMobiles($numbers, $delimiter = ',')
    {
        $registered = $not_reg = $invalid = $unknown = array();
        $was_array = (is_array($numbers)) ? 1 : 0;
        if (!$was_array) $numbers = explode($delimiter, $numbers);
        
        foreach ($numbers as $number) {
            
                $resp = self::isRegistered($number);
                if($resp==-4);
                elseif ($resp == -3) $invalid[] = $number;
                elseif ($resp == -2) $unknown[] = $number;
                elseif ($resp == -1) $not_reg[] = $number;
                else  $registered[] = $number;
            
        }
        
        if (!$was_array) {
            $registered = implode($delimiter, $registered);
            $not_reg = implode($delimiter, $not_reg);
            $invalid = implode($delimiter, $invalid);
            $unknown = implode($delimiter, $unknown);
        }

        return array(
            'registered' => $registered,
            'not_registered' => $not_reg,
            'invalid' => $invalid,
            'unknown_response' => $unknown);
    }

}
print_r(DNDCheck::checkMobiles('917275240944,9999212,8888888888,09450313528,+919451430071'));
//print_r(DNDCheck::checkMobiles(array('917275240944','9999212','8888888888','09450313528','+919451430071')));
