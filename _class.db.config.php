<?PHP
define('DB_SERVER','aspisit.com');
define('DB_USERNAME','aspisit_rsa');
define('DB_PASSWORD','s3cur3th3w0rld');
define('DB_DATABASE','aspisit_rsa');
define('DB_TABLE_PREFIX','aspisit_');

function getMYSQLIConnection(){
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    // check connection
    if ($conn->connect_error) {
        trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
    }
    return $conn;

}