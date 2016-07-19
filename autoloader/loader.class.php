<?php
//URL: http://www.sparky-san.com/efficient-convenient-autoloading-php/
class CustomAutoload
{
	function class_autoloader($class) {
		$class = strtolower($class);

		$className = ltrim($class, '\\');
		$fileName  = '';
		if ($lastNsPos = strrpos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}

		$class_filename = $fileName.'class.'.str_replace('_', DIRECTORY_SEPARATOR, strtolower($className)).'.php';
		$document_root = dirname(dirname(__FILE__));
		$class_root = "{$document_root}/classes/";
		$cache_file = "{$document_root}/cache/class.paths.cache";

		$path_cache = (file_exists($cache_file)) ? unserialize(file_get_contents($cache_file)) : array();
		if (!is_array($path_cache)) 
			$path_cache = array();

		if (array_key_exists($class, $path_cache) && file_exists($path_cache[$class]))
			require_once $path_cache[$class]; 
		else {
			/* Determine the location of the file within the $class_root and, if found, load and cache it */
			$directories = new RecursiveDirectoryIterator($class_root);
			foreach(new RecursiveIteratorIterator($directories) as $file) {
				if (strtolower($file->getFilename()) == $class_filename) {
					$full_path = $file->getRealPath();
					$path_cache[$class] = $full_path;
					require_once $full_path;
					$serialized_paths = serialize($path_cache);
					if ($serialized_paths != $path_cache)
						file_put_contents($cache_file, $serialized_paths);
					break;
				}
			}

		}
	}
}
spl_autoload_register(function ($class) {
	$customLoader = new CustomAutoload();
	$customLoader->class_autoloader($class);
});

?>
