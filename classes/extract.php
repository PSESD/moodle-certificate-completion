<?php
namespace report_seriescompletion;

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/coursecatlib.php');

/*
	Author: Jacob Morrison (jmorrison@psesd.org)
*/
class extract extends object {
	public $type;
	public $error = false;
	protected $_file;
	protected $_filePath;

	public function __construct($type)
	{
		if (!in_array($type, ['course', 'series'])) {
			throw new \Exception("Invalid roll up type.");
		}
		$this->type = $type;
		register_shutdown_function([$this, 'cleanFile']);
	}

	protected function prepare()
	{
		$this->loadObjects();
	}

	protected function loadObjects()
	{
		// load categories [-> subcategories] -> courses -> certificate
		$rootCategory = objects\course_category::load();
		\d($rootCategory);
	}

	protected function roll()
	{

	}

	public function serve()
	{
		$file = $this->getFile();
		$this->prepare();
		$this->roll();

		die ("Made it this far! " . memory_get_peak_usage());
		exit;
		fclose($file);
		if ($this->error || !file_exists($this->getFilePath())) {
			throw new \Exception("Creation of report failed (".$this->error.").");
		}
		\send_file($this->getFilePath(), 'series_completion_'. $this->type .'_'.date("Y-m-d") .'.csv', 'default' , 0, false, true, 'text/csv; charset=UTF-8');
	}

	public function cleanFile()
	{
		@unlink($this->getFilePath());
	}

	public function getFile()
	{
		if (!isset($this->_file)) {
			$this->_file = fopen($this->getFilePath(), 'w');
			if (!$this->_file) {
				throw new \Exception("Unable to open temp file for writing. Please check settings.");
			}
		}
		return $this->_file;
	}

	public function getFilePath() {
	    global $CFG;
	    if (isset($this->_filePath)) {
	    	return $this->_filePath;
	    }
	    $path = [];
	    $path[] = $CFG->dataroot;
	    $path[] = $dataPath = 'admin_report_seriescompletiontemp';
	    $path[] = date("Y-m-d_H-i-s") .'-'. $this->type .'.csv';
	    \make_upload_directory($dataPath);
	    return $this->_filePath = implode(DIRECTORY_SEPARATOR, $path);
	}
}