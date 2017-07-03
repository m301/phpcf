<?PHP

function getMYSQLIConnection()
{
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    // check connection
    if ($conn->connect_error)
        trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);

    return $conn;
}
