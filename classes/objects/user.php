<?php
namespace report_seriescompletion\objects;

class user extends base {
	protected $_courses;
	protected $_certificates = [];

	public static function load($userRaw)
	{
		$userParams = [];
		$userParams['id'] = $userRaw['id'];
		$userParams['username'] = $userRaw['username'];
		$userParams['firstname'] = $userRaw['firstname'];
		$userParams['lastname'] = $userRaw['lastname'];
		$userParams['fullname'] = $userRaw['fullname'];
		$userParams['email'] = $userRaw['email'];

		$customParams = ['SiteName' => 'sitename', 'Program' => 'program', 'starsid' => 'starsid'];
		foreach ($customParams as $param => $id) {
			$userParams[$id] = null;
		}
		if (!empty($userRaw['customfields'])) {
			foreach ($userRaw['customfields'] as $field) {
				if (isset($field['shortname']) && isset($customParams[$field['shortname']])) {
					$customParam = $customParams[$field['shortname']];
					$userParams[$customParam] = $field['value'];
				}
				//$userParams['fullname'] = $userRaw->fullname;
			}
		}
		$user = static::loadObject($userParams);
		if ($issues = $user->db->get_records('certificate_issues', array('userid' => $user->id))) {
			foreach ($issues as $issueRaw) {
				$issue = certificate_issue::load($issueRaw);
				if ($issue) {
					$this->addCertificateIssue($issue);
				}
			}
		}
		return $user;
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

	public function getCertificates()
	{
		return $this->_certificates;
	}
}
?>