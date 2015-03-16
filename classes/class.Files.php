<?php
require_once dirname(dirname(__FILE__)).'/autoloader/loader.class.php';
class Files
{
	public function __construct($base, $host)
	{
		$this->base = $base;
		$this->host = $host;
		$this->vHosts = [];
	}
	public function findConfigurations()
	{
		$dirs = scandir($this->base);
		$files = [];
		foreach($dirs as $key)
		{
			if(is_file($this->base."/".$key))
			{
				if(exec("grep ServerName $this->base/$key | grep [^.]$this->host"))
				{
					$files[] = "$this->base/$key";
				}
			}
		}
		$this->files = $files;
		return $this->files;
	}
	public function installRewrite()
	{
		$dir = dirname(__FILE__);
		foreach($this->files as $file)
		{
			$content = file_get_contents($file);
			$lines = explode(PHP_EOL, $content);
			preg_match_all("/<VirtualHost .*?>(.*?)<\/VirtualHost>/s", $contents, $split);
			$vHostLines = $this->getLineNumber("<VirtualHost", $lines);
			for($i = 0; $i < count($split[1]); $i++){
				if(preg_match("/(ServerName\s+$this->host\s+)/s",$subsplit,$out))
				{
					$subsplit = $split[1][$i];
					$res = preg_match("/Alias\s+(.*?)\s+".preg_quote($dir, "/")."/", $subsplit, $out);
					$res2 = preg_match(preg_quote("RewriteRule ^/?sitemap.xml?$ $out[1]/index.php [NC,PT]","/"), $subsplit, $out2);
					if($res && $res2)
					{
						echo "Script is already installed";
					}
					else
					{
						if($res)
						{
							$isRWEngineOn = $this->getLineNumber("RewriteEngine", $lines, $vHostLines[$i]);
							if($isRWEngineOn)
							{
								$isRWEngineOn = $isRWEngineOn[0]+1;
								$RewriteRule = [];

								array_splice($lines, $isRWEngineOn, 0, $RewriteRule);
							}
							else
							{

							}
						}

					}
				}
			}
		}
	}
	public function parseFiles()
	{
		foreach($this->files as $file)
		{
			$this->parseFile($file);
		}
		//Sort VHost by SSL, HTTPS has priority over HTTP
		usort($this->vHosts, function($a, $b)
		{
			if($a->ssl == $b->ssl)
				return 0;
			else if($a->ssl)
				return -1;
			else
				return 1;
		});
	}
	private function getLineNumber($search, $lines, $offset = 0){
		$line_number = false;
		for($i = $offset; $i < count($lines); $i++)
		{
			$line = $lines[$i];
			if(strpos($line, $search) !== FALSE)
			{
				if(!$line_number) $line_number = [];
				$line_number[] = $i + 1;
			}
		}
		return $line_number;
	}
	private function parseFile($file_path)
	{
		$contents = file_get_contents($file_path);
		$lines = explode(PHP_EOL, $contents);
		preg_match_all("/<VirtualHost .*?>(.*?)<\/VirtualHost>/s", $contents, $split);

		foreach($split[1] as $subsplit)
		{
			if(preg_match("/(ServerName\s+$this->host\s+)/s",$subsplit,$out))
			{
				$this->vHosts[] = new VHost($subsplit, $this->getLineNumber($subsplit, $lines));
			}
		}
	}
}

?>
