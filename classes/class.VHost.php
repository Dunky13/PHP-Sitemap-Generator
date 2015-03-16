<?php
require_once dirname(dirname(__FILE__)).'/autoloader/loader.class.php';
class VHost
{
	public function __construct($str, $blah)
	{
		$this->full = $str;
		$this->ServerName = $this->getTag("ServerName");
		$this->ssl = $this->matchTag("SSLEngine", "on");
		$this->alias = $this->getAliases();

	}
	private function getTag($tag)
	{
		$res = preg_match("/\s+$tag\s+(.*?)\s+/", $this->full, $out);
		if($res)
			return $out[1];
		return false;
	}
	private function matchTag($tag, $value)
	{
		if(preg_match("/\s+$tag\s+$value\s+/", $this->full))
			return true;
		return false;
	}
	private function getTags($tag)
	{
		$res = preg_match_all("/\s+$tag\s+(.*?)\s+/", $this->full, $out);
		if($res)
			return $out[1];
		return false;
	}
	private function getAliases()
	{
		$ret = new AliasList();
		$res = preg_match("/\s+DocumentRoot\s+(.*?)\s+/", $this->full, $out);
		if($res)
		{
			$ret->add(new Alias("/", $out[1]));
		}
		$res = preg_match_all("/\s+Alias\s+(.*?)\s+(.*?)\s+/", $this->full, $out);
		if($res)
		{
			for($i = 0; $i < count($out[1]); $i++)
			{
				$alias = [$out[1][$i], $out[2][$i]]; //$out[1][$i] == Alias Path, $out[2][$i] == Absolute Path
				if(!$this->isErrorDocument($alias) && !$this->isPrivate($alias))
				{
					$als = new Alias($out[1][$i], $out[2][$i]);
					if(!$ret->contains($als))
						$ret->add($als);
				}
			}
		}
		//Sort alias of VHost by priority, then number of images and finally alphabetically
		usort($ret, function($a, $b)
		{
			if($a->priority == $b->priority)
			{
				if(count($a->images) == count($b->images))
					return strcmp($a->url, $b->url);
				else if(count($a->images) > count($b->images))
					return -1;
				else
					return 1;
			}
			else if($a->priority > $b->priority)
				return -1;
			else
				return 1;
		});
		return $ret;

	}
	private function isErrorDocument(array $posibilities)
	{
		foreach($posibilities as $pos)
		{
			if(preg_match("/(ErrorDocument\s+\d+\s+".preg_quote($pos, "/")."\s+)/",$this->full))
				return true;
		}
		return false;
	}

	private function isPrivate(array $posibilities)
	{
		foreach($posibilities as $pos)
		{
			$res = preg_match_all("/<(Location|Directory) ".preg_quote($pos, "/").">(.|\s)*?(?=Require)Require (.*)(.|\s)*?<\/(Location|Directory)>/",$this->full, $out);
			if($res && ($out[3][0] == "all denied" || $out[3][0] == "valid-user"))
			{
					return true;
			}
		}
		return false;
	}
}
?>