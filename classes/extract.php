<?php
namespace report_seriescompletion;

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . "/user/lib.php");

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
		if (!in_array($type, ['course', 'category'])) {
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
		$start = microtime(true);
		objects\user::load();

		// load categories [-> subcategories] -> courses -> certificate
		$rootCategory = objects\course_category::load();

	}

	protected function roll()
	{
		$columns = [];
		$columns[] = ['label' => 'First Name', 'value' => function($user) { return $user->getMetaField('firstname'); }];
		$columns[] = ['label' => 'Last Name', 'value' => function($user) { return $user->getMetaField('lastname'); }];
		$columns[] = ['label' => 'Username', 'value' => function($user) { return $user->getMetaField('username'); }];
		$columns[] = ['label' => 'Email', 'value' => function($user) { return $user->getMetaField('email'); }];
		$columns[] = ['label' => 'Site Name', 'value' => function($user) { return $user->getMetaField('sitename'); }];
		$columns[] = ['label' => 'Program', 'value' => function($user) { return $user->getMetaField('program'); }];
		$columns[] = ['label' => 'Stars ID', 'value' => function($user) { return $user->getMetaField('starsid'); }];

		if ($this->type === 'category') {
			$coursecats = objects\course_category::getAll();
			foreach ($coursecats as $coursecat) {
				$columns[] = [
					'label' => $coursecat->getMetaField('name'),
					'value' => function($user) use ($coursecat) { return $coursecat->getCompletion($user); }
				];
			}
		} else {
			$courses = objects\course::getAll();
			foreach ($courses as $course) {
				$columns[] = [
					'label' => $course->getMetaField('shortname'),
					'value' => function($user) use ($course) { return $course->getCompletion($user); }
				];
			}
		}

		$columnLabels = [];
		foreach ($columns as $column) {
			$columnLabels[] = $this->cleanValue($column['label']);
		}
		fputcsv($this->file, $columnLabels);

		$allUsers = objects\user::getAll();
		foreach ($allUsers as $user) {
			$row = [];
			foreach ($columns as $column) {
				$row[] = $this->cleanValue($column['value']($user));
			}
			fputcsv($this->file, $row);
		}
	}

	public function cleanValue($value)
	{
		$value = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $value);
		$value = htmlspecialchars_decode($value);
		return $value;
	}

	public function serve()
	{
		$file = $this->getFile();
		$this->prepare();
		$this->roll();

		fclose($file);
		if ($this->error || !file_exists($this->getFilePath())) {
			throw new \Exception("Creation of report failed (".$this->error.").");
		}
		\send_file($this->getFilePath(), 'series_completion_'. $this->type .'_'.date("Y-m-d") .'.csv', 'default' , 0, false, true, 'application/csv');
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

	public function getCacheFilePath() {
	    global $CFG;
	    if (isset($this->_cacheFilePath)) {
	    	return $this->_cacheFilePath;
	    }
	    $path = [];
	    $path[] = $CFG->dataroot;
	    $path[] = $dataPath = 'admin_report_seriescompletiontemp';
	    $path[] = 'data_cache.csv';
	    \make_upload_directory($dataPath);
	    return $this->_cacheFilePath = implode(DIRECTORY_SEPARATOR, $path);
	}
}