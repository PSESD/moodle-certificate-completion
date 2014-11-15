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


	public function addChild($child)
	{
		$this->_children[$child->id] = $child;
	}

	public function addCourse($course)
	{
		$this->_courses[$course->id] = $course;
	}
}
?>