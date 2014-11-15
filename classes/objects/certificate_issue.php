<?php
namespace report_seriescompletion\objects;

class certificate_issue extends base {
	public static function load($certificateIssueRaw) {
		$issueParams = [];
		$issueParams['id'] = $certificateIssueRaw->id;
		$issueParams['user'] = user::getById($certificateIssueRaw->userid, true);
		$issueParams['certificate'] = certificate::getById($certificateIssueRaw->certificateid, true);

		$object = static::loadObject($issueParams);

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