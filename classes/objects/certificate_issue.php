<?php
namespace report_certificatecompletion\objects;

class certificate_issue extends base {
	public static function load($certificateIssueRaw) {
		$issueParams = [];
		$issueParams['id'] = $certificateIssueRaw->id;
		$issueParams['user'] = user::getById($certificateIssueRaw->userid, true);
		$issueParams['certificate'] = certificate::getById($certificateIssueRaw->certificateid, true);
		$issueParams['issue_date'] = $certificateIssueRaw->timecreated;
		$object = static::loadObject($issueParams);

		return $object;
	}

	public function getCertificate()
	{
		return $this->getMetaField('certificate');
	}

	public function getCourse()
	{
		$certificate = $this->getMetaField('certificate');
		if (!empty($certificate)) {
			return $certificate->getMetaField('course');
		}
		return null;
	}

	public function getUser()
	{
		return $this->getMetaField('user');
	}

	public function getIssueDate()
	{
		return $this->getMetaField('issue_date');
	}

	public function postLoad()
	{
		if (!empty($this->meta['user'])) {
			$this->meta['user']->addCertificateIssue($this);
		}
		if (!empty($this->meta['certificate'])) {
			$this->meta['certificate']->addCertificateIssue($this);
		}
	}
	
}
?>