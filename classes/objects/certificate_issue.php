<?php
namespace report_seriescompletion\objects;

class certificate_issue extends base {
	public static function load($certificateIssueRaw) {
		$issueParams = [];
		$issueParams['id'] = $certificateIssueRaw->id;
		$issueParams['user'] = user::getById($certificateIssueRaw->userid);
		$issueParams['certificate'] = certificate::getById($certificateIssueRaw->certificateid);
		if (!empty($issueParams['user'])) {
			\d($issueParams);exit;
		}
		$object = static::loadObject($issueParams);

		if (empty($issueParams['user']) || empty($issueParams['certificate'])) {
			return false;
		//	throw new \Exception("missing user or certificate from issue");
		}
		if (!empty($issueParams['user'])) {
			$issueParams['user']->addCertificateIssue($object);
		}
		if (!empty($issueParams['certificate'])) {
			$issueParams['certificate']->addCertificateIssue($object);
		}
		return $object;
	}
	
}
?>