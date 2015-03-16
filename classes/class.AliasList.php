<?php
require_once dirname(dirname(__FILE__)).'/autoloader/loader.class.php';
class AliasList implements Iterator
{
	private $position;
	private $img = [];
	public function __construct()
	{
		$this->position = 0;
		$this->alias = [];
	}
	public function add(Alias $alias)
	{
		$this->alias[] = $alias;
	}
	public function contains(Alias $alias)
	{
		foreach($this->alias as $als)
		{
			if($als->equals($alias))
				return true;
		}
		return false;
	}
	public function size()
	{
		return count($this->alias);
	}
	function rewind() 
	{
        $this->position = 0;
    }

    function current() 
	{
        return $this->alias[$this->position];
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
        return isset($this->alias[$this->position]);
    }
}
?>