<?PHP
include_once("class.Template.php");
include_once("class.UI.php");

function isValidAccess($accessLevel,$message="You donot have access to that page !"){
    if(!array_key_exists('access_level',$_SESSION))
        redirectTo('http://'.DOMAIN_NAME."/login.php?");
    $definedAccess=$_SESSION['access_level'];

    if($definedAccess!=$accessLevel && $definedAccess!=1)
        redirectTo('http://'.DOMAIN_NAME."/warning.php?message=".urlencode($message));
}

function isValidInput($data,$type){
    //if(array_key_exists('isInvalidValidInput',$_SESSION))
    //$_SESSION['isInvalidValidInput']=1;
    return 1;
}
function redirectTo($url, $options = array())
{
    $code = (isset($options['permanent']) ? 301 : 302);
    if (isset($options['staus_code']))
        $code = $options['staus_code'];

    if($code=="303")
        header('HTTP/1.1 303 See Other');

    if (isset($options['time']))
        header('Refresh: ' . $options['time'] . ';url=' . $url);
    else
        header('Location: ' . $url, true, $code);
    die();

}


class RMLI{
    var $db;

    //$this->db=mysqli_connect("localhost",$this->db_user,$this->db_password,$this->db_name);
    
    //install();
    function __construct($USER_NAME="madhur"){
        $db_user=$USER_NAME.'_rmli';
        $db_password='test@123';
        $db_name=$USER_NAME.'_rmli';
        $this->db=mysql_connect("localhost",$db_user,$db_password);
        mysql_select_db($db_name,$this->db);
    }

    function install(){
        $query  ='CREATE TABLE IF NOT EXISTS '.TABLE_PREFIX.'users (username CHAR(20) NOT NULL PRIMARY KEY,password CHAR(35),access INT);';
        $query .='CREATE TABLE IF NOT EXISTS '.TABLE_PREFIX.'patients (name CHAR(50),uid MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,registeredOn TIMESTAMP,details BLOB,medical BLOB);';
        $query .='CREATE TABLE IF NOT EXISTS '.TABLE_PREFIX.'tokens (tokenno MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,generatedon TIMESTAMP,foruser MEDIUMINT NOT NULL);';
        echo $query;
        if (mysql_query($query,$this->db))
         echo "Tables created !";
        else
            $this->debugMysql();
    }       
    
    
    function registerUser($user,$password,$access){
        $_access=array('admin'=>1,'staff'=>2,'paramedical'=>3,'doctor'=>4);
        $access=$_access[$access];
       // $user=$this->db->real_escape_string($user);
        
        $query="INSERT INTO ".TABLE_PREFIX."users(username,password,access)VALUES('$user','".md5($password)."',$access);";
        if( mysql_query($query,$this->db))
            return 1;
        else
            $this->debugMysql();
        return 0;
    
    }
    
    function login($user,$password){
        $table_name=TABLE_PREFIX."users";
        $query="SELECT access FROM $table_name where username='$user' AND password='".md5($password)."';";
        $result=mysql_query($query,$this->db);
        if($result){
            $res=mysql_fetch_assoc($result);
            if (!empty($res['access'])) {
                return $res['access'];
            }
        }else{
            return 0;
        }
    }

    
    function registerNewPatient($details){
        $table_name=TABLE_PREFIX."patients";
    
        $name = $details['name'];
        $details = json_encode($details);
        // $user=$this->db->real_escape_string($user);
        $query="INSERT INTO $table_name (name,details,medical,uid,registeredOn)VALUES('$name','$details','',null,now());";
        if( mysql_query($query,$this->db)){
            return mysql_insert_id ($this->db);
        }else{
            debugMysql(mysql_error($this->db));
        }
        return 0;
    }

    function generateTokenFor($uid){
         $table_name=TABLE_PREFIX."tokens";
         $query="INSERT INTO $table_name (tokenno,generatedon,foruser)VALUES(null,now(),".$uid.");";
    
         if( mysql_query($query,$this->db)){
             return mysql_insert_id ($this->db);
         }else{
             debugMysql(mysql_error($this->db));
         }
         return 0;
     }
    
    function debugMysql(){
        $text=mysql_error($this->db);
        echo "<div class='mysql-error'> MYSQL Error : ".$text."</div><br/>";
    }
    
    function getUserDetail($uid){
        $table_name=TABLE_PREFIX."patients";
        $result =mysql_query("SELECT * FROM $table_name WHERE uid=$uid;",$this->db);
        if (!mysql_num_rows($result)) return 0;
        $rows = mysql_fetch_assoc($result);
    
        $rows['details']=json_decode($rows['details']);
        $rows['medical']=json_decode($rows['medical']);
        mysql_free_result($result);
        return $rows;
    }
    function getUserIDByToken($token){
        $table_name=TABLE_PREFIX."tokens";
        $result =mysql_query("SELECT foruser FROM $table_name WHERE tokenno=$token;",$this->db);
        if (!mysql_num_rows($result)) return 0;
        $rows = mysql_fetch_assoc($result);
    
        mysql_free_result($result);
        return $rows['foruser'];
    }

    function newReportByID($uid,$paramedical){
        $table_name=TABLE_PREFIX."reports";
        $paramedical = json_encode($paramedical);

        $query="INSERT INTO $table_name (forUID,reportID,paramedical,medical,generatedOn)VALUES($uid,null,'$paramedical','',now());";
        if( mysql_query($query,$this->db)){

            return mysql_insert_id ($this->db);
        }else{
            debugMysql(mysql_error($this->db));
        }
        return 0;
    }

    function __destruct(){
        mysql_close($this->db);
    }

}