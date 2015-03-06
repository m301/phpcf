<?PHP
//Provides password or key whatever is needed.
class KeyProvider{
	public static getUsernamePassword($service){
			$keys  = array("cpanel://ika.pw"=>array("username"=>"ika","password"=>"qwe@0987"));
			
			$keys["ftp://ftp.ika.pw"] = $keys["cpanel://ika.pw"];
			
			
			return $keys[$service];
	}
}
