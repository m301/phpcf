<?PHP
include("functions.basic.php");
$processWrapper = new ProcessWrapper();

echo $processWrapper->setCommand("vim")."";

class ProcessWrapper{
	// Command name
	var $command = null;
	
	/*
	 * Keys are treated as argument, set null to exclude from passing
	 * If value is an array and index `nokey` then only arg value is passed. 
	 */
	var $args = array();
	
	
	
	// String which is followed by args key. 
	var $precedor = "--";
						 
	// Functions to validate parameter passed
	var $optionValidator = array();
	
	
	
	
	public function __construct(){
		$this->optionValidator[1] = function($value){
									return 1;
								};
								
		$this->optionValidator["arg1"] = function($value){
									if(strlen($value)<10) return 0;
									else return 1;
								};
								
	}
	
	
	function setOption($key, $value){
		//validate option
		if(!isset($this->args[$key]))
			return -1;
		
		//If option validator is set and is a function and doesn't returns invalid 
		if(isset($this->optionValidator[$key]) && is_callable($this->optionValidator[$key]) && !$this->optionValidator[$key]($value)) 
				return 0;
		
		$this->args[$key] = $value;
		return 1;
	}	
	
	
	/**
	 * Sets validator function 
	 * 
	 * Returns :
	 * -1 : if arg has not been defined
	 *  0 : if value is not a function 
	 *  1 : all succeeded
	 */
	private function setValidator($key, $value, $nokey=0){
		if(!isset($this->args[$key]))
			return -1;
		
		if(!is_callable($value))
			return 0;
		
		if($nokey) 
			$value = array("value"=> $value, "nokey"=>1);
		
		$this->optionValidator[$key] = $value;
		return 1;
	}
	
	
	/**
	 * Set the precedor which is followed by argument.
	 * Default is `--`, some commands use `-`.
	 */
	private function setPrecedor($precedor){
		$this->precedor = $precedor;
	}
	
	
	/**
	 * Set main command to be executed
	 * 
	 * Returns:
	 * -1 : if command is not found 
	 *  1 : if validated
	 */
	public function setCommand($command, $nocheck = 0){
		if(!(`which $command`) && !$nocheck)
			return -1;
		
		$this->command = $command;
		return 1;	
	}
	
	
	function contructCommand(){
	
	}
	
	
	/**
	 * No output
	 */
	function exec(){
	
	}
	
	
	/**
	 * String as output
	 */
	function sexec(){
	
	}
	
	
	/**
	 * return object containing 
	 */
	function oexec(){
	
	}
	
	
}
