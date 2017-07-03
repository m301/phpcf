<?PHP

class Git{
	private $sourceDir;
	private $source;
	private $branch;
	
	function __construct($source, $branch="master"){
		$this->source = $source;
		$this->branch = $branch;
	
		//Create a temp. dir
		if(defined("TMP_DIR"))
			$this->sourceDir = TMP_DIR."tik-".md5($source.$branch).'/';
		else
			$this->sourceDir = "/tmp/tik-".md5($source.$branch).'/';			
	}

	
	function pullCode(){
		//if(file_exists($this->sourceDir))
		//	self::hardPull($this->branch);
		//else
		if(file_exists($this->sourceDir))
			rrmdir($this->sourceDir);
			
		self::cloneBranch($this->branch,1);

		
		return $this->sourceDir;
	}
	

	function takeSourceOwnership(){
		exec("chown -R `whoami`:`whoami` ".$this->sourceDir);
	}
	
	
	function cloneBranch($branch, $latest=0){
		if($latest) $latest  = '--depth=1';
		$command = "git clone ".$latest." --branch ".$this->branch." ".$this->source.' '.$this->sourceDir;
		$result = exec($command);
		
		echo "Result [$command]: ".$result.";\n<br/>";
		
		$result = self::execGit($this->sourceDir," submodule foreach git pull");
		$this->takeSourceOwnership();
		return $result;
	}


	function hardPull($branch){
		self::execGit($this->sourceDir,"fetch origin ".$this->branch);
		self::execGit($this->sourceDir,"reset --hard origin/".$this->branch);
	}
	
	
	static function execGit($sourceDir, $command){
		$command = "git --git-dir='".$sourceDir.".git' --work-tree='".$sourceDir."' ".$command;
		$result = exec($command);
		
		echo "Result [$command]: ".$result.";\n<br/>";
		if(strpos($result,"FETCH_HEAD: Permission denied")){
			$this->takeSourceOwnership();
			$result = exec("git --git-dir='".$sourceDir."' --work-tree='".$sourceDir."' ".$command);
		}
		
		return $result;
	}	
	
}
