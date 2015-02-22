<?PHP
define("LOGGER_REMOTE_URL","htp://google.com");
define("LOGGER_STORE_MODE" , false);
define("LOGGER_STORE_FILE",)
class SMMTriggers
{
    static $Logger;
    static $isInited = false;
    static $logStoreMode = false;
    static $remoteTriggerURL;

    static function onNewUsersFound($id, $owner, $keyword, $plugin, $newUser)
    {
        $data = array("id" => $id, "owner" => $owner, "keyword" => $keyword, "plugin" => $plugin, "newUsers" => $newUser);
        self::log(__FUNCTION__, json_encode($data));
        self::addToStore(__FUNCTION__, $data);

    }

    static function log($event, $data)
    {
        self::init();
        fwrite(self::$Logger, date(DATE_RFC2822) . "[" . $event . "]\n" . $data . "\n");
    }

    static function init($storeMode = false)
    {
        if (self::$isInited) return;
        self::$Logger = fopen('tik_triggers_log.txt', 'a');

        if (self::$logStoreMode === false)
            self::setStoreMode($storeMode);
    }

    static function setStoreMode($storeMode)
    {
        if ($storeMode)
            self::$logStoreMode = array();
        else
            self::$logStoreMode = false;

    }

    static function addToStore($function, $data)
    {
        if (!is_array(self::$logStoreMode)) return;

        //make sure key exist
        if (!isset(self::$logStoreMode[$function]))
            self::$logStoreMode[$function] = array();

        //add data to key
        self::$logStoreMode[$function][] = $data;
    }

    static function pushToStore()
    {
        //Store data in var -> forward request -> reset the var
        //Ensures if multiple instances are running no problem
        $data = (json_encode(self::$logStoreMode));
        //self::log(__FUNCTION__,DataHandler::postContent(self::$remoteTriggerURL,"data=".urlencode($data)));
        self::log(__FUNCTION__,$data);
        self::$logStoreMode = array();
    }

    static function onTaskProcess($task)
    {
        $task['response'] = "__ommited__";
        self::log(__FUNCTION__, json_encode($task));
    }

    static function onTasksProcess($tasks)
    {
        self::log(__FUNCTION__, "length : " . count($tasks));
    }

    static function onError($taskID, $plugin, $error)
    {
        $data = array("taskID" => $taskID, "plugin" => $plugin, "error" => $error);
        self::log(__FUNCTION__, json_encode($data));
        self::addToStore(__FUNCTION__, $data);
        //DataHandler::getContent("http://tikaj.com/gcm.php");
    }

}