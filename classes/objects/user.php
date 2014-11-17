<?php
namespace report_certificatecompletion\objects;

class user extends base {
	protected $_courses;
	protected $_certificate_issues = [];

	public static function load()
	{
		global $CFG;
		$user = new static;
		$usersRaw = $user->db->get_records('user');
		foreach ($usersRaw as $userRaw) {
			$fields = $user->db->get_recordset_sql("SELECT f.shortname, d.data
		                                        FROM {user_info_field} f
		                                        JOIN {user_info_data} d ON (f.id=d.fieldid)
		                                    WHERE d.userid=". $userRaw->id);
		    $userRaw->customfields = [];
		    foreach ($fields as $field) {
		    	 $userRaw->customfields[$field->shortname] = $field->data;
		    }
		    static::loadOne($userRaw);
		    $fields->close();
		}
	}

	public static function loadOne($userRaw)
	{
		$userParams = [];
		$userParams['id'] = $userRaw->id;
		$userParams['username'] = $userRaw->username;
		$userParams['firstname'] = $userRaw->firstname;
		$userParams['lastname'] = $userRaw->lastname;
		$userParams['email'] = $userRaw->email;
		$userParams['deleted'] = $userRaw->deleted;

		$customParams = ['SiteName' => 'sitename', 'Program' => 'program', 'starsid' => 'starsid'];
		foreach ($customParams as $param => $id) {
			$userParams[$id] = null;
		}
		foreach ($userRaw->customfields as $name => $value) {
			if (!isset($customParams[$name])) { continue; }
			$key = $customParams[$name];
			$userParams[$key] = $value;
		}
		$user = static::loadObject($userParams);
		return $user;
	}


	public function getIsValid()
	{
		if (!empty($this->meta['deleted'])) {
			return false;
		}
		return true;
	}

	public function getCourses()
	{
		return $this->_courses;
	}

	public function enrollIn($course) {
		$this->_courses[$course->id] = $course;
	}

	public function addCertificateIssue($certificateIssue)
	{
		$this->_certificate_issues[$certificateIssue->id] = $certificateIssue;
	}

	public function getCertificateIssues()
	{
		return $this->_certificate_issues;
	}
}
?>