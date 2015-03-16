<?php
require_once dirname(dirname(__FILE__)).'/autoloader/loader.class.php';
class Alias
{
	public function __construct($url, $path)
	{
		$this->url = $url;
		$this->path = $path;
		$this->lastMod = filemtime($path."/.");
		$this->priority = number_format(1.0/count(explode("/", rtrim($url,"/"))), 2);

		$this->checkForIndex();
	}
	public function equals(Alias &$alias)
	{
		return $this->url === $alias->url;
	}
	private function checkForIndex()
	{
		$fileName = null;
		if(file_exists($this->path."/index.html"))
		{
			$fileName = $this->path."/index.html";
		}
		else if(file_exists($this->path."/index.php"))
		{
			$fileName = $this->path."/index.php";
		}
		if($fileName)
		{
			$file = file_get_contents($fileName);

			$this->images = $this->getImages($file);
		}
	}
	private function getImages($file)
	{
		$res = preg_match_all("/<img(.*?)>/", $file, $out);
		$img = new ImageList();
		if($res)
		{
			foreach($out[1] as $o)
			{
				$image = new Image($o);
				if($image->isValid($this->path) && !$img->contains($image))
					$img->add($image);
			}
			return $img->size() > 0 ? $img: false;
		}

		return false;
	}
}
?>