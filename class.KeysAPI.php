<?PHP
include_once("class.DataHandler.php");

class KeysAPI{
	public static function getUsernamePassword($service){
		return json_decode(DataHandler::getContent("http://keys.tik.bz/?url=".$service),true);
	}
}
