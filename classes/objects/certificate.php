<?php
namespace report_seriescompletion\objects;

class certificate extends base {
	protected $_certificate_issues = [];
	
	public static function load($certificateRaw) {
		return static::loadObject(['id' => $certificateRaw->id, 'name' => $certificateRaw->name]);
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