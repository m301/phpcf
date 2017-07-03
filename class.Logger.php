<?PHP
/*
 * This file is subject to the terms and conditions defined in file 'LICENSE.txt', which is part of this source code package.
 * If license file is missing, a copy of 'LICENSE.txt' can be found at http://license.tikaj.com/proprietary_code
 */

class Log{
	static $instance = null;
	static $isInit = false;
	
	public static function init(){
		if(!self::$isInit)
			self::$instance = new Logger();
		self::$isInit = true;
	}
	
	public static function e(){
		if(!self::$isInit)
			self::init();
		self::$instance->log(func_get_args());
	}
	
	
}

class Logger
{
	/*
	 * Todo:
	 * - Enable logging of file name.
	 * - Dumping objects
	 */
	var $LOG_TIMESTAMP_FORMAT = DATE_RFC2822;
	var $LOG_TIMESTAMP = true;
	
	var $LOG_FUNCTION_NAME = true;
	var $LOG_FILE_NAME = true;
	var $LOG_LINE_NUMBER = true;
	
	var $LOG_TO_FILE = false;
	var $LOG_FILE_PATH = false;
	var $LOG_FILE_BUFFER = 1; //Number of lines to wait to flush buffer
	
	var $FILE_BUFFER = '';
	var $LOG_FILE_HANDLE = null;
	
	var $events = array();
	
	public function __construct($filePath=null, $triggerURl=null){
		if($filePath == null)
			$this->LOG_FILE_PATH = $filePath = "\tmp\tik_phplogger.txt";
			
		$this->LOG_FILE_HANDLE = fopen($filePath, 'a');
	}

	
	public function __destruct() {
		fclose($this->LOG_FILE_HANDLE);
	}
	
	
	public function iLog($udata,$level,$depth=0){
		
		//print_r(debug_backtrace());
		//Process raw args
		if(count($udata)==1 && is_array($udata) && count($udata[0])>0)
			$udata=$udata[0];
		
		$data = array( "message"=> $udata[0]);
		array_shift($udata);
		
		
		
		foreach($udata as $key=>$value){
			if(is_object($value))
				$udata[$key] = serialize($value);	
		}
		
		//If only one extra param then use that element in data index.
		$udata = count($udata)==1?$udata[0]:$udata;
		
		
		//$debugBacktrace = null;
		//if($level==null){
		//	$debugBacktrace  = debug_backtrace();
		//	$level = $debugBacktrace[1]["function"];
		//}
		
		$data['log_level'] = $level;
		
		if($this->LOG_TIMESTAMP)
			$data['timestamp']= date($this->LOG_TIMESTAMP_FORMAT);
		
		if($this->LOG_FUNCTION_NAME || $this->LOG_FILE_NAME || $this->LOG_LINE_NUMBER){
			// if level is defined
			//if($debugBacktrace==null)
				$debugBacktrace  = debug_backtrace();
			//else
			//	$debugBacktrace  = $debugBacktrace[$depth];
			$btcount= count($debugBacktrace);
			$i = 2;
			while($i--)
				if(isset($debugBacktrace[$depth]['class']) && $debugBacktrace[$depth]['class']=="Logger") $depth++; 
			if($depth <= $btcount && isset($debugBacktrace[$depth]['class']) && $debugBacktrace[$depth]['class']=="Log") $depth++;
			if($depth == $btcount ) $depth--;
			
			$debugBacktrace = $debugBacktrace[$depth];
			
			if($this->LOG_FUNCTION_NAME)
				$data['function'] = $debugBacktrace['function'];
				
			if($this->LOG_FILE_NAME)
				$data['file_name'] = $debugBacktrace['file'];
				
			if($this->LOG_LINE_NUMBER)
				$data['line_number'] = $debugBacktrace['line'];
		}
		
		
		$data['data'] = $udata;
		//JSON encode log data.
		$data = json_encode($data);
		
		echo $data;
		
		//Push to file
		if($this->LOG_TO_FILE)
			writeLogToFile($data);
			
		//Fire triggers
		$this->fireEvent($level, $data);	
		$this->fireEvent(__FUNCTION__, $data);
	}
	
	/*
	 * Save all data to files.
	 */
	function flushFileBuffer(){
		
	}
	
	function writeLogToFile($data){
		fwrite($this->LOG_FILE_HANDLE, $data.',');
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
    
    
    function fireEvent($eventName, $data){
		$eventName = "on".ucfirst($eventName);
		if(isset($this->events[$eventName]))
			$this->events[$eventName]($data);
	}
        
        
    public function emergency(){
		$this->iLog(func_get_args(),__FUNCTION__);
	}

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert(){
		$this->iLog(func_get_args(),__FUNCTION__);
	}

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical(){
		$this->iLog(func_get_args(),__FUNCTION__);
	}

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error(){
		$this->iLog(func_get_args(),__FUNCTION__);
	}
	
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning(){
		$this->iLog(func_get_args(),__FUNCTION__);
	}
    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice(){
		$this->iLog(func_get_args(),__FUNCTION__);
	}

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info(){
		$this->iLog(func_get_args(),__FUNCTION__);
	}

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug(){
		$this->iLog(func_get_args(),__FUNCTION__);
	}

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log(){
		$this->iLog(func_get_args(),__FUNCTION__);
	}
}
