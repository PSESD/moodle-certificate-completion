<?php
namespace report_seriescompletion\objects;

class certificate extends base {
	protected $_certificate_issues = [];
	protected $_loaded = false;

	public static function load($certificateRaw, $course) {
		return static::loadObject(['id' => $certificateRaw->id, 'name' => $certificateRaw->name, 'course' => $course]);
	}
	
	public function postLoad()
	{
		if (!$this->_loaded) {
			if ($issues = $this->db->get_records('certificate_issues', array('certificateid' => $this->id))) {
				foreach ($issues as $issueRaw) {
					certificate_issue::load($issueRaw);
				}
			}
			$this->_loaded = true;
		}
		return parent::postLoad();
	}

	public function addCertificateIssue($certificateIssue)
	{
		$this->_certificate_issues[$certificateIssue->id] = $certificateIssue;
	}

	public function getIssues()
	{
		return $this->_certificate_issues;
	}
}
?>