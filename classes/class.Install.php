<?php
require_once dirname(dirname(__FILE__)).'/autoloader/loader.class.php';
class Install
{
	public function __construct($base, $host)
	{
		$this->base = $base;
		$this->host = $host;
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
		$dir = dirname(dirname(__FILE__));
		foreach($this->files as $file)
		{
			$content = file_get_contents($file);
			$lines = explode(PHP_EOL, $content);
			preg_match_all("/<VirtualHost .*?>(.*?)<\/VirtualHost>/s", $content, $split);
			$vHostLines = $this->getLineNumber("</VirtualHost", $lines);
			for($i = 0; $i < count($split[1]); $i++){
				$subsplit = $split[1][$i];
				if(preg_match("/(ServerName\s+$this->host\s+)/s",$subsplit,$out))
				{
					$res = preg_match("/Alias\s+(.*?)\s+".preg_quote($dir, "/")."/", $subsplit, $out);

					if($res){
						$res2 = preg_match("/RewriteRule\s+\^\/\?sitemap.xml\?\\\$\s+".preg_quote($out[1], "/")."\/index.php\s+\[NC,PT\]/", $subsplit, $out2);
						if($res2){
							echo "<em>$file</em> has the script already installed";
							return;
						}
					}
						$str = explode(PHP_EOL, "	<IfModule mod_alias.c>
		<IfModule mod_rewrite.c>
			Alias			/sitemap			$dir

			RewriteEngine 	on
			RewriteRule		^/?sitemap.xml?$		/sitemap/index.php [NC,PT]
		</IfModule>
	</IfModule>"); 
					array_splice($lines, $vHostLines[$i]-1,0, $str);
					$file_output = implode(PHP_EOL, $lines);
					if(is_writable($file)){
						file_put_contents($file, $file_output);
					}
					else{
						echo "I cannot write to <em>$file</em> please overwrite it with the following: <br>\n";
						echo "<xmp>
$file_output
</xmp>";
					}
				}
			}
		}
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
}

?>
