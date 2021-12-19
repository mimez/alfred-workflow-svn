<?php
namespace Mimez\AlfredWorkflow\SvnWorkflow;

use Mimez\AlfredApp\Results;
use Redmine;

class SvnWorkflow
{

	/**
	 * @var Redmine\Client
	 */
	protected $connectionData = [];

    /**
     * @var string
     */
    protected $svnBinaryPath;

	public function setSvnConnection($url, $user, $pass)
	{
		$this->connectionData = [
		    'url' => $url,
            'user' => $user,
            'pass' => $pass
		];
	}

    public function __construct($url, $user, $pass, $svnBinaryPath)
    {
        $this->setSvnConnection($url, $user, $pass);
        $this->svnBinaryPath = $svnBinaryPath;
    }



	public function __invoke($keyword = '') {
		$resultsCollection = new Results\ResultsCollection();


        if (in_array($keyword, array('latest', 'last', ''))) {
            $revisionId = $this->getLatestRevisionId();

        } else {
            $revisionId = $keyword;
        }

        $revision = $this->getRevision($revisionId);

        $result = new Results\Result();
        $result
            ->setTitle(sprintf("%s: %s", $revision['revisionId'], $revision['msg']))
            ->setSubtitle('Copy to clipboard')
            ->setArg(sprintf("Interne Notiz: \n%s\nsiehe r%s", $this->deleteFirstLine($revision['msg']), $revision['revisionId']))
            ->setIcon('icons/svn.png')
            ->setValid(true);
        $resultsCollection->add($result);

		echo $resultsCollection->getResultsAsXml();
	}

    protected function deleteFirstLine($string)
    {
        return preg_replace('/^.+\n/', '', $string);
    }

    protected function getRevision($revisionId)
    {
        $xmlString = $this->runSvnCmd(sprintf('log -c %s', $revisionId));
        $xml = simplexml_load_string($xmlString);

        return [
            'revisionId' => $revisionId,
            'msg' => (string)$xml->logentry->msg,
        ];
    }

    protected function getLatestRevisionId()
    {
        $xmlString = $this->runSvnCmd('log -l 1');
        $xml = simplexml_load_string($xmlString);

        return (string)$xml->logentry->attributes()->revision;
    }

    protected function runSvnCmd($cmd)
    {
        return shell_exec(sprintf('%s --xml --username %s --password %s %s %s', $this->svnBinaryPath, $this->connectionData['user'], $this->connectionData['pass'], $cmd, $this->connectionData['url']));
    }
}
