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
			$this->sourceDir = TMP_DIR."tik-".md5($source).'/';
		else
			$this->sourceDir = "/tmp/tik-".md5($source).'/';			
	}

	
	function pullCode(){
		if(file_exists($this->sourceDir))
			self::hardPull($this->branch);
		else
			self::cloneBranch($this->branch,1);

		
		return $this->sourceDir;
	}
	

	function takeSourceOwnership(){
		exec("chown -R `whoami`:`whoami` ".$this->sourceDir);
	}
	
	function cloneBranch($branch, $latest=0){
		if($latest) $latest  = '--depth=1';
		$result = exec("git clone ".$latest." --branch ".$this->branch." ".$this->source.' '.$this->sourceDir);
		//echo ("git clone ".$latest." --branch ".$this->branch." ".$this->source.' '.$this->sourceDir);
		$result = self::execGit($this->sourceDir," submodule foreach git pull");
		$this->takeSourceOwnership();
		return $result;
	}


	function hardPull($branch){
		self::execGit($this->sourceDir,"fetch origin ".$this->branch);
		self::execGit($this->sourceDir,"reset --hard origin/".$this->branch);
	}
	
	
	static function execGit($sourceDir, $command){
		//echo "git --git-dir='".$sourceDir."/.git' --work-tree='".$sourceDir."' ".$command;
		$result = exec("git --git-dir='".$sourceDir."/.git' --work-tree='".$sourceDir."' ".$command);
		
		if(strpos($result,"FETCH_HEAD: Permission denied")){
			$this->takeSourceOwnership();
			$result = exec("git --git-dir='".$sourceDir."' --work-tree='".$sourceDir."' ".$command);
		}
		
		return $result;
	}	
	
}
