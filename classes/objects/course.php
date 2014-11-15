<?php
namespace report_seriescompletion\objects;

class course extends base {
	protected $_certificates = [];
	protected $_users = [];
	public static function load($courseRaw) {
		$courseRaw = get_course($courseRaw->id);
		$course = static::loadObject(['id' => $courseRaw->id, 'shortname' => $courseRaw->shortname, 'fullname' => $courseRaw->fullname]);

		// load certificates
		$sql = "SELECT c.id, c.name
                  FROM {certificate} c
                 WHERE c.course = :course";
        $params = ['course' => $course->id];
        $certificatesRaw = $course->db->get_records_sql($sql, $params);
        foreach ($certificatesRaw as $certificateRaw) {
        	$certificate = certificate::load($certificateRaw);
        	if ($certificate) {
        		$course->addCertificate($certificate);
        	}
        }
        if (empty($course->certificates)) {
        	return false;
        }

        $usersRaw = \core_enrol_external::get_enrolled_users($course->id, [['name' => 'onlyactive', 'value' => 1]]);
        foreach ($usersRaw as $userRaw) {
        	$user = user::load($userRaw);
        	if ($user) {
        		$course->enrollUser($user);
        	}
        }

		return $course;
	}

	public function getCertificates()
	{
		return $this->_certificates;
	}

	public function addCertificate($cert)
	{
		$this->_certificates[$cert->id] = $cert;
	}

	public function getUsers()
	{
		return $this->_users;
	}

	public function enrollUser($user)
	{
		$user->enrollIn($this);
		$this->_users[$user->id] = $user;
	}
	
}
?>