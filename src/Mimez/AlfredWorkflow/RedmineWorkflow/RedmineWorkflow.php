<?php
namespace Mimez\AlfredWorkflow\RedmineWorkflow;

use Mimez\AlfredApp\Results;
use Redmine;

class RedmineWorkflow
{
	
	/**
	 * @var Redmine\Client
	 */
	protected $redmine;
	
	public function setRedmine(Redmine\Client $redmine)
	{
		$this->redmine = $redmine;
	}
	
	public function getRedmine()
	{
		return $this->redmine;
	}
	
	public function __construct(Redmine\Client $redmine) 
	{
		$this->setRedmine($redmine);
	}
	
	
	public function __invoke($keyword) {
		$resultsCollection = new Results\ResultsCollection();
        
		$redmineResult = $this->getRedmine()->issue->show(str_replace('#', '', $keyword));
    
        if (!$redmineResult) {
            return;
        }

        $result = new Results\Result();
        $result
            ->setTitle(sprintf('%s #%s - %s', $redmineResult['issue']['tracker']['name'], $redmineResult['issue']['id'], $redmineResult['issue']['subject']))
            ->setSubtitle('Copy to clipboard')
            ->setArg(sprintf('%s #%s - %s', $redmineResult['issue']['tracker']['name'], $redmineResult['issue']['id'], $redmineResult['issue']['subject']))
            ->setIcon('icons/redmine.png')
            ->setValid(true);
        $resultsCollection->add($result);
        
		echo $resultsCollection->getResultsAsXml();
	}
}