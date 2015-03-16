<?php
require_once dirname(dirname(__FILE__)).'/autoloader/loader.class.php';
class ImageList implements Iterator
{
	private $position;
	private $img = [];
	public function __construct()
	{
		$this->position = 0;
		$this->img = [];
	}
	public function add(Image $img)
	{
		$this->img[] = $img;
	}
	public function contains(Image $img)
	{
		foreach($this->img as $image)
		{
			if($image->equals($img))
				return true;
		}
		return false;
	}
	public function size()
	{
		return count($this->img);
	}
	function rewind() 
	{
        $this->position = 0;
    }

    function current() 
	{
        return $this->img[$this->position];
    }

    function key() 
	{
        return $this->position;
    }

    function next() 
	{
        ++$this->position;
    }

    function valid() 
	{
        return isset($this->img[$this->position]);
    }
}
?>