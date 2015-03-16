<?php
require_once dirname(dirname(__FILE__)).'/autoloader/loader.class.php';
class Image
{

	public function __construct($tags)
	{
		$this->src = $this->attr($tags, "src");
		$this->alt = $this->attr($tags, "alt");
	}
	private function attr($str, $attr)
	{
		$res = preg_match("/$attr=\"(.*?)\"/", $str, $out);
		return $res ? $out[1] : false;
	}
	public function equals(Image &$img)
	{
		return $this->src === $img->src;
	}
	public function isValid($root)
	{
		return trim($this->src) !== "" && trim($this->alt) !== "" && $this->isInternal() && $this->isVisible($root) && !$this->toExclude();
	}

	private function isInternal()
	{
		if(Glob::startsWith($this->src, "//") || Glob::startsWith($this->src, "http://") || Glob::startsWith($this->src, "https://"))
			return false;
		return true;
	}
	private function isVisible($root)
	{
		$_min_width = $GLOBALS["ini"]["Image settings"]["minimum_width"];
		$_min_height = $GLOBALS["ini"]["Image settings"]["minimum_height"];

		list($width, $height) = getimagesize($root."/".$this->src);
		return $width >= $_min_width && $height >= $_min_height;
	}
	private function toExclude()
	{
		$_exclude = $GLOBALS["ini"]["Image settings"]["exclude_images"];
		$ext = $this->extension();
		foreach($_exclude as $excl)
		{
			if($excl === $ext)
				return true;
		}
		return false;
	}
	private function extension()
	{
		return substr($this->src, strpos($this->src, ".")+1);
	}
}

?>