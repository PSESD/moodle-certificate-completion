<?php
namespace report_seriescompletion\objects;

class course_category extends base {
	protected $_courses = [];
	protected $_children = [];

	public static function load($id = false) {
		if (is_object($id)) {
			$categoryRaw = $id;
		} else {
			$categoryRaw = \coursecat::get($id);
		}
		$courseCategory = static::loadObject(['id' => $categoryRaw->id, 'name' => $categoryRaw->name, 'visible' => $categoryRaw->visible]);
		foreach ($categoryRaw->get_children(['sort' => ['name' => 1]]) as $subCategoryRaw) {
			$subcat = static::load($subCategoryRaw);
			if ($subcat === false) { continue; }
			$courseCategory->addChild($subcat);
		}
		$courses = $categoryRaw->get_courses([
			'sort' => ['sortorder' => 1, 'fullname' => 1]
		]);
		foreach ($courses as $courseRaw) {
			$courseObject = course::load($courseRaw);
			if (!$courseObject) { continue; }
			$courseCategory->addCourse($courseObject);
		}
		return $courseCategory;
	}

	public function getCompletion($user)
	{
		$results = [];
		$expected = count($this->children) + count($this->courses);
		foreach ($this->children as $childcat) {
			$result = $childcat->getCompletion($user);
			if (!is_null($result)) {
				$results[] = strtotime($result .' 00:00:01');
			}
		}
		foreach ($this->courses as $course) {
			$result = $course->getCompletion($user);
			if (!is_null($result)) {
				$results[] = strtotime($result.' 00:00:01');
			}
		}
		if (!empty($results) && count($results) === $expected) {
			$max = max($results);
			return date("Y-m-d", $max);
		}
		return null;
	}

	public function addChild($child)
	{
		$this->_children[$child->id] = $child;
	}

	public function getChildren()
	{
		return $this->_children;
	}

	public function getCourses()
	{
		return $this->_courses;
	}

	public function addCourse($course)
	{
		$this->_courses[$course->id] = $course;
	}
}
?>