<?php
/**
 * Class which processes the submitted data from CreatePPC.html
 */

/**
 * Class which processes the submitted data from CreatePPC.html
 *
 * Class which processes the submitted data from CreatePPC.html
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */
require_once(PATH . 'JSON.php');
require_once(PATH . 'CreatePPCQueue.class.php');

Class CreatePPC {
	
	/**
	 * @var Mixed $jsonString
	 */
	var $jsonString = false;
	
	/**
	 * @var Mixed $jsonObj
	 */
	var $jsonObj = false;
	
	/**
	 * @var Array $ADWORDS
	 */
	var $ADWORDS = array();
	
	/**
	 * @var Array $YAHOO
	 */
	var $YAHOO = array();
	
	/**
	 * @var Array $MSN
	 */
	var $MSN = array();
	
	/**
	 * @var Integer $QueueInsertCount
	 */
	var $QueueInsertCount = 0;
	
	/**
	 * @var Integer $QueueInsertCount
	 */
	var $QueueItemCount = 0;
	
	/**
	 * @var Integer $DeleteCount
	 */
	var $DeleteCount = 0;
	
	/**
	 * @var Integer $NotEnoughCreditCount
	 */
	var $NotEnoughCreditCount = 0;
	
	Private $_db;
	
	/**
	 * Constructor
	 *
	 * @param String $jsonString
	 */
	public function __construct($jsonString)
	{
		$this->jsonString = $jsonString;
		$this->jsonObj = json_decode($jsonString);
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		/*//
		print '<pre>';
		print_r($this->jsonObj);
		die;
		//*/
	}
	public function summaryInfo()
	{
		$Output = '';
		foreach($this->jsonObj as $lb => $jso)
		{
			if(!is_array($jso))
				continue;
			if(sizeof($jso))
			{
				$lb = str_replace('new', 'New ', $lb);
				$Output .= sizeof($jso) . ' ' . $lb . ' <br/> ';
			}
		}
		return $Output;
	}
	
	public function summaryInfoExtended()
	{
		$Output = '';
		foreach($this->jsonObj as $lb => $jso)
		{
			if(!is_array($jso))
				continue;
				
			$lb = str_replace('new', 'New ', substr($lb, 0, -1));
			foreach($jso as $jsoi)
			{
				$Output .= $lb . ': ';
				if(isset($jsoi->name))
					$Output .= $jsoi->name;
				if(isset($jsoi->keyword))
					$Output .= $jsoi->keyword;
				if(isset($jsoi->title))
					$Output .= $jsoi->title;

				$Output .= ' &nbsp; ' . "\n";
			}
		}
		return $Output;
	}
	
	public function insertQueueEnvelope($JobID, $Input, $UserID)
	{
		$this->QueueInsertCount++;
		$PATH = PATH;
        require_once("{$PATH}AbsoluteIncludeHelper.include.php");
		$Queue = new QueueComponent();
		
		$envelope = "<?php\n\t
	require_once('{$PATH}AbsoluteIncludeHelper.include.php');\n
	{$Input}
\n\t?>";
		$Queue->SendEnvelope($JobID, $envelope);
		$query = "UPDATE bevomedia_queue SET user__id = {$UserID} WHERE jobId = '{$JobID}'";
		$this->_db->exec($query);
		return $JobID;
	}
	
	public function processDelete()
	{
		$Types = array('1'=>'ADWORDS', '2'=>'YAHOO', '3'=>'MSN');
		
		foreach($this->jsonObj->deleteAdVariations as $delAdVar)
		{
			$AdGroup = $this->getAdGroup($delAdVar->curAdGroupID);
			$Campaign = $this->getCampaign($AdGroup->CampaignID);
		
			/* @var $account Accounts_PPC_Abstract */
			$account = $this->getAccount($Types[$Campaign->ProviderType], $Campaign->AccountID);
			$CampaignAPI = $account->GetCampaignIDAPI($Campaign->Name);
			$AdGroupAPI = $account->GetAdGroupIDAPI($AdGroup->Name, $CampaignAPI);
			$account->GetAPI()->deleteAds($AdGroupAPI, $delAdVar->apiAdVariationID);
			$account->RemoveAdVariation($delAdVar->curAdVariationID);
			$this->DeleteCount++;
		}
		
		foreach($this->jsonObj->deleteKeywords as $delKeyword)
		{
			$AdGroup = $this->getAdGroup($delKeyword->curAdGroupID);
			$Campaign = $this->getCampaign($AdGroup->CampaignID);
			
			/* @var $account Accounts_PPC_Abstract */
			$account = $this->getAccount($Types[$Campaign->ProviderType], $Campaign->AccountID);
			$CampaignAPI = $account->GetCampaignIDAPI($Campaign->Name);
			$AdGroupAPI = $account->GetAdGroupIDAPI($AdGroup->Name, $CampaignAPI);
			$account->GetAPI()->deleteKeywords($AdGroupAPI, $delKeyword->apiKeywordID);
			$account->RemoveKeyword($delKeyword->curKeywordID);
			$this->DeleteCount++;
		}
	}
	
	public function getAdGroup($id)
	{
		$sql = 'SELECT CampaignID, Name FROM bevomedia_ppc_adgroups WHERE id = ' . $id;
		return $this->_db->fetchRow($sql);
	}
	
	public function getCampaign($id)
	{
		$sql = 'SELECT ProviderType, AccountID, Name FROM bevomedia_ppc_campaigns WHERE id = ' . $id;
		return $this->_db->fetchRow($sql);
	}
	
	public function queueInsert($UserID = false)
	{
		$CPQ = new CreatePPCQueue();
		//print '<pre>';
		//print_r($this->jsonObj);
		
		$jsonObj = $this->jsonObj;
		$output = array();

		$skipCampaigns = array();
		$skipAdGroups = array();
		$skipAdVars = array();
		$skipKeywords = array();
        if($UserID === false)
		    $UserID = $_SESSION['User']['ID'];
		$CPQ->UserID = $UserID;
		
		$User = new User($UserID);
		
		$APIUse = 0;
		$PATH = PATH;
        require_once("{$PATH}AbsoluteIncludeHelper.include.php");
		$Queue = new QueueComponent();
		$JobID =  $Queue->CreateJobID('PPC Editor', $UserID);

		foreach($jsonObj->newCampaigns as $key=>$newCampaign)
		{
			if(!$newCampaign)
				continue;
			if($newCampaign->checked)
			{
				
				$APIUse += 5;
				/* @var $account Accounts_PPC_Abstract */
				$account = $this->getAccount($newCampaign->curAccountType, $newCampaign->curAccountID);
				$Env = "";
				$Env .= $CPQ->EchoIdentificationComment($UserID);
				$Env .= $CPQ->EchoAccountConstruct($account);
				$Env .= $CPQ->EchoAccountFunction('AddCampaignAPI', $newCampaign->name, $newCampaign->budget, $newCampaign->name, $newCampaign->geotargets, $newCampaign->negativekeywords, $newCampaign->searchtarget);
				$Env .= $CPQ->EchoAccountFunction('addCampaign', $newCampaign->name, "\$Tempoutput", $newCampaign->budget, $newCampaign->searchtarget);
				$Env .= $CPQ->EchoAccountFunction('replaceCampaignNegativeKeywords', "\$Tempoutput", $newCampaign->negativekeywords);
				$Env .= $CPQ->EchoAccountFunction('replaceCampaignGeotargetCountries', "\$Tempoutput", $newCampaign->geotargets->countries);
				$Env .= $CPQ->EchoDescriptionString();
				$CPQ->AddToEnvelope($JobID, $Env, $newCampaign->curAccountType, 'Create campaign "'.$newCampaign->name.'" on account '.$account->username);
				$this->QueueItemCount++;
			}else{
				$skipCampaigns[] = $jsonObj->newCampaigns[$key]->curCampaignID;
				$jsonObj->newCampaigns[$key]->skipped = true;
			}
		}
		foreach($jsonObj->newAdGroups as $key=>$newAdGroup)
		{
			if(!$newAdGroup)
				continue;
				
			if($newAdGroup->checked && !in_array($newAdGroup->curCampaignID, $skipCampaigns))
			{
				
				$APIUse += 5;
				
				/* @var $account Accounts_PPC_Abstract */
				$account = $this->getAccount($newAdGroup->curAccountType, $newAdGroup->curAccountID);
				$CampaignName = $this->getCampaignName($jsonObj, $account, $newAdGroup->curCampaignID);
				
				$Env = "";
				$Env .= $CPQ->EchoIdentificationComment($UserID);
				$Env .= $CPQ->EchoAccountConstruct($account);
				$Env .= $CPQ->EchoAccountFunction('GetCampaignIDAPI', $CampaignName);
				$Env .= $CPQ->EchoAccountFunction('AddAdGroupAPI', $newAdGroup->name, "\$Tempoutput", $newAdGroup->bid, $newAdGroup->addistribution, $newAdGroup->negativekeywords, $newAdGroup->contentbid);
				$Env .= $CPQ->EchoCopyVar('Tempoutput', 'APIAdGroupID');
				$Env .= $CPQ->EchoAccountFunction('GetCampaignID', $CampaignName);
				$Env .= $CPQ->EchoAccountFunction('addAdGroup', $newAdGroup->name, "\$Tempoutput", "\$APIAdGroupID", $newAdGroup->bid, $newAdGroup->contentbid);
				$Env .= $CPQ->EchoCopyVar('Tempoutput', 'AdGroupId');
				$Env .= $CPQ->EchoAccountFunction('replaceAdGroupNegativeKeywords', "\$AdGroupId", $newAdGroup->negativekeywords);
				$Env .= $CPQ->EchoDescriptionString();
				$CPQ->AddToEnvelope($JobID, $Env, $newAdGroup->curAccountType, 'Add Adgroup "'.$newAdGroup->name.'" to campaign "'.$CampaignName.'" on account '.$account->username);
				$this->QueueItemCount++;
			}else{
				$skipAdGroups[] = $jsonObj->newAdGroups[$key]->curAdGroupID;
				$jsonObj->newAdGroups[$key]->skipped = true;
			}
		}

		foreach($jsonObj->newAdVariations as $key=>$newAdVar)
		{
			if(!$newAdVar)
				continue;
				
			if($newAdVar->checked && !in_array($newAdVar->curAdGroupID, $skipAdGroups))
			{
				$APIUse += 50;
				
				/* @var $account Accounts_PPC_Abstract */
				$account = $this->getAccount($newAdVar->curAccountType, $newAdVar->curAccountID);
			
				$CampaignName = $this->getCampaignName($jsonObj, $account, $newAdVar->curCampaignID);
				$AdGroupName = $this->getAdGroupName($jsonObj, $account, $newAdVar->curAdGroupID, $CampaignName);
				if(!$AdGroupName) { continue; }
				$Env = "";
				$Env .= $CPQ->EchoIdentificationComment($UserID);
				$Env .= $CPQ->EchoAccountConstruct($account);
				$Env .= $CPQ->EchoAccountFunction('GetCampaignIDAPI', $CampaignName);
				$Env .= $CPQ->EchoAccountFunction('GetAdGroupIDAPI', $AdGroupName, "\$Tempoutput");
				$Env .= $CPQ->EchoAccountFunction('AddAdVariationAPI', $newAdVar->title, $newAdVar->destinationurl, $newAdVar->displayurl, $newAdVar->description, "\$Tempoutput");
				$Env .= $CPQ->EchoAccountFunction('addAdVariationTo', $CampaignName, $AdGroupName, $newAdVar->title, $newAdVar->destinationurl, $newAdVar->displayurl, $newAdVar->description, "\$Tempoutput");
				$Env .= $CPQ->EchoDescriptionString();
				$CPQ->AddToEnvelope($JobID, $Env,$newAdVar->curAccountType, 'Add Ad Variation "'.$newAdVar->title.'" to Ad Group "'.$AdGroupName.'" under campaign "'.$CampaignName.'" on account '.$account->username);
				$this->QueueItemCount++;
			}else{
				$jsonObj->newAdVariations[$key]->skipped = true;
			}
		}
		
		foreach($jsonObj->newKeywords as $key=>$newKeyword)
		{
			if(!$newKeyword)
				continue;

			if(!isset($newKeyword->bid))
				$newKeyword->bid = 0;
			if(!isset($newKeyword->destinationurl) || $newKeyword->destinationurl == 'http://')
				$newKeyword->destinationurl = '';
				
			if($newKeyword->checked && !in_array($newKeyword->curAdGroupID, $skipAdGroups))
			{
				$APIUse += 20;
				
				/* @var $account Accounts_PPC_Abstract */
				$account = $this->getAccount($newKeyword->curAccountType, $newKeyword->curAccountID);
				$CampaignName = $this->getCampaignName($jsonObj, $account, $newKeyword->curCampaignID);
				$AdGroupName = $this->getAdGroupName($jsonObj, $account, $newKeyword->curAdGroupID, $CampaignName);
			
				$Env = "";
				$Env .= $CPQ->EchoIdentificationComment($UserID);
				$Env .= $CPQ->EchoAccountConstruct($account);
				$Env .= $CPQ->EchoAccountFunction('GetCampaignIDAPI', $CampaignName);
				$Env .= $CPQ->EchoAccountFunction('GetAdGroupIDAPI', $AdGroupName, "\$Tempoutput");
				$Env .= $CPQ->EchoAccountFunction('AddKeywordAPI', $newKeyword->keyword, $newKeyword->bid, $newKeyword->destinationurl, "\$Tempoutput", $newKeyword->advMatch);
				$Env .= $CPQ->EchoAccountFunction('addKeywordTo', $CampaignName, $AdGroupName, $newKeyword->keyword, $newKeyword->bid, $newKeyword->destinationurl, "\$Tempoutput");
				$Env .= $CPQ->EchoDescriptionString();
				$CPQ->AddToEnvelope($JobID, $Env, $newKeyword->curAccountType, 'Add keyword "'.$newKeyword->keyword.'" to Ad Group "'.$AdGroupName.'" under campaign "'.$CampaignName.'" on account '.$account->username);
				$this->QueueItemCount++;
			}else{
				$jsonObj->newKeywords[$key]->skipped = true;
			}
		}
		
		if ( (!$User->IsSubscribed(User::PRODUCT_FREE_PPC)) || 
		 	 (($User->vaultID!=0) && !$User->IsSubscribed(User::PRODUCT_PPC_YEARLY_CHARGE))
	   		)
		{
			echo "Account not verified...";
			return;
		}
				
		if ($User->apiCalls<$APIUse)
		{
			$User->AddUserAPICallsCharge();
		}
		$User->subtractApiCalls($APIUse);
		foreach($CPQ->envelopes as $jid => $Env)
		{
			$this->insertQueueEnvelope($jid, $Env, $UserID);
		}
		
		return $this;
	}
	
	/**
	 * Saves this $jsonString to the database for the specified User matching $User_ID and returning the table insert ID.
	 *
	 * @param Integer $User_ID
	 * @return Integer
	 */
	public function save($User_ID, $Label = '', $Overwrite = false)
	{
		$_db = Zend_Registry::get('Instance/DatabaseObj');
		
		$Data = $_db->quote($this->jsonString);
		$Label = $_db->quote($Label);
		$Sql = "INSERT INTO bevomedia_createppc_session (user__id, json, label) VALUES ($User_ID, $Data, {$Label})";
		$_db->exec($Sql);

		return $_db->lastInsertId();
	}
	
	public function delete($ID)
	{
		$_db = Zend_Registry::get('Instance/DatabaseObj');
		
		$Sql = "DELETE FROM bevomedia_createppc_session WHERE id = $ID AND user__id = {$_SESSION['User']['ID']} LIMIT 1";
		$_db->exec($Sql);
	}
	
	/**
	 * Processes this $jsonObj submitted by CreatePPC.html
	 *
	 * @return Mixed
	 */
	public function process()
	{
		$jsonObj = $this->jsonObj;
		
		$skipCampaigns = array();
		$skipAdGroups = array();
		$skipAdVars = array();
		$skipKeywords = array();
		
		$output = array();
		
		
		foreach($jsonObj->newCampaigns as $key=>$newCampaign)
		{
			if(!$newCampaign)
				continue;
				
			if($newCampaign->checked)
			{
				$account = $this->getAccount($newCampaign->curAccountType, $newCampaign->curAccountID);
				
				if($account->GetErrorAPI() !== false)
				{
					$jsonObj->newCampaigns[$key]->success = false;
					$jsonObj->newCampaigns[$key]->error = $account->GetErrorAPI();
				}else{
					if($newCampaign->curCampaignID < 0)
					{
						$out = $account->AddCampaignAPI($newCampaign->name, $newCampaign->budget, $newCampaign->name, $newCampaign->geotargets, $newCampaign->negativekeywords, $newCampaign->searchtarget);
						if(is_float($out))
						{
							$jsonObj->newCampaigns[$key]->success = true;
							$jsonObj->newCampaigns[$key]->apiCampaignID = (string)$out;
							$jsonObj->newCampaigns[$key]->dbCampaignID = $account->addCampaign($newCampaign->name, (int)$out, $newCampaign->budget, $newCampaign->searchtarget);
						}else{
							$jsonObj->newCampaigns[$key]->error = $out;
						}
					}
				}
			}else{
				$skipCampaigns[] = $jsonObj->newCampaigns[$key]->curCampaignID;
				$jsonObj->newCampaigns[$key]->skipped = true;
			}
			
			$output[] = $jsonObj->newCampaigns[$key];
		}
		foreach($jsonObj->newAdGroups as $key=>$newAdGroup)
		{
			if(!$newAdGroup)
				continue;
				
			if($newAdGroup->checked && !in_array($newAdGroup->curCampaignID, $skipCampaigns))
			{
				$account = $this->getAccount($newAdGroup->curAccountType, $newAdGroup->curAccountID);

				if($account->GetErrorAPI() !== false)
				{
					$jsonObj->newAdGroups[$key]->success = false;
					$jsonObj->newAdGroups[$key]->error = $account->GetErrorAPI();
				}else{
					
					if($newAdGroup->curAdGroupID < 0)
					{
						$apiCampaignID = $this->getCampaignIDAPI($jsonObj, $account, $newAdGroup->curCampaignID);
						$out = $account->AddAdGroupAPI($newAdGroup->name, $apiCampaignID, $newAdGroup->bid, $newAdGroup->addistribution, $newAdGroup->negativekeywords, $newAdGroup->contentbid);
						if(is_float($out))
						{
							$jsonObj->newAdGroups[$key]->success = true;
							$jsonObj->newAdGroups[$key]->apiAdGroupID = (string)$out;
							$jsonObj->newAdGroups[$key]->dbAdGroupID = $account->AddAdGroup($newAdGroup->name, $this->getCampaignID($jsonObj, $account, $newAdGroup->curCampaignID));
						}else{
							$jsonObj->newAdGroups[$key]->error = $out;
						}
					}
				}
			}else{
				$skipAdGroups[] = $jsonObj->newAdGroups[$key]->curAdGroupID;
				$jsonObj->newAdGroups[$key]->skipped = true;
			}
			
			$output[] = $jsonObj->newAdGroups[$key];
		}
		foreach($jsonObj->newAdVariations as $key=>$newAdVar)
		{
			if(!$newAdVar)
				continue;
				
			if($newAdVar->checked && !in_array($newAdVar->curAdGroupID, $skipAdGroups))
			{
				$account = $this->getAccount($newAdVar->curAccountType, $newAdVar->curAccountID);
			
				if($account->GetErrorAPI() !== false)
				{
					$jsonObj->newAdVariations[$key]->success = false;
					$jsonObj->newAdVariations[$key]->error = $account->GetErrorAPI();
				}else{
					$apiAdGroupID = $this->getAdGroupIDAPI($jsonObj, $account, $newAdVar->curAdGroupID);
					$out = $account->AddAdVariationAPI($newAdVar->title, $newAdVar->destinationurl, $newAdVar->displayurl, $newAdVar->description, $apiAdGroupID);
					if(is_float($out))
					{
						$jsonObj->newAdVariations[$key]->success = true;
						$jsonObj->newAdVariations[$key]->apiAdVariationID = (string)$out;
						$jsonObj->newAdVariations[$key]->dbAdVariationID = $account->AddAdVariation($this->getAdGroupID($jsonObj, $account, $newAdVar->curAdGroupID), $newAdVar->title, $newAdVar->destinationurl, $newAdVar->displayurl, $newAdVar->description);
					}else{
						$jsonObj->newAdVariations[$key]->error = $out;
					}
				}
			}else{
				$jsonObj->newAdVariations[$key]->skipped = true;
			}
			
			$output[] = $jsonObj->newAdVariations[$key];
		}
		foreach($jsonObj->newKeywords as $key=>$newKeyword)
		{
			if(!$newKeyword)
				continue;

			if(!isset($newKeyword->bid))
				$newKeyword->bid = 0;
			if(!isset($newKeyword->destinationurl))
				$newKeyword->destinationurl = '';
				
			if($newKeyword->checked && !in_array($newKeyword->curAdGroupID, $skipAdGroups))
			{
				$account = $this->getAccount($newKeyword->curAccountType, $newKeyword->curAccountID);
			
				if($account->GetErrorAPI() !== false)
				{
					$jsonObj->newKeywords[$key]->success = false;
					$jsonObj->newKeywords[$key]->error = $account->GetErrorAPI();
				}else{
					
					$apiAdGroupID = $this->getAdGroupIDAPI($jsonObj, $account, $newKeyword->curAdGroupID);
					$out = $account->AddKeywordAPI($newKeyword->keyword, $newKeyword->bid, $newKeyword->destinationurl, $apiAdGroupID, $newKeyword->advMatch);
					if(is_float($out))
					{
						$jsonObj->newKeywords[$key]->success = true;
						$jsonObj->newKeywords[$key]->apiKeywordID = (string)$out;
						$jsonObj->newKeywords[$key]->dbKeywordID = $account->AddKeyword($this->getAdGroupID($jsonObj, $account, $newKeyword->curAdGroupID), $newKeyword->keyword, $newKeyword->bid, $newKeyword->destinationurl);
					}else{
						$jsonObj->newKeywords[$key]->error = $out;
					}
				}
			}else{
				$jsonObj->newKeywords[$key]->skipped = true;
			}
			
			$output[] = $jsonObj->newKeywords[$key];
		}
		
		usort($output, array($this, 'outSort'));
		
		$jsonObj->output = $output;
		return $jsonObj;
	}
	
	private function genNum($o)
	{
		$out = 0;
		if(isset($o->curCampaignID))
			$out += $o->curCampaignID * 100 * 100 * 100;
		if(isset($o->curAdGroupID))
			$out += $o->curAdGroupID * 100 * 100;
		if(isset($o->curAdVariationID))
			$out += $o->curAdVariationID * 100;
		if(isset($o->curKeywordID))
			$out += $o->curKeywordID * 1;
		return $out;
	}
	
	private function outSort($a, $b)
	{
		$a = $this->genNum($a);
		$b = $this->genNum($b);

		if ($a == $b) {
			return 0;
		}
		return ($a > $b) ? -1 : 1;
	}
	
	private function getAccount($type, $id)
	{
		if(isset($this->{$type}[$id]))
		{
			$account = $this->{$type}[$id];
		}else{
			if($type == 'MSN')
			{
				$account = new Accounts_MSNAdCenter();
				$account->GetInfo($id);
			}
			
			if($type == 'YAHOO')
			{
				$account = new Accounts_Yahoo();
				$account->GetInfo($id);
			}
			
			if($type == 'ADWORDS')
			{
				$account = new Accounts_Adwords();
				$account->GetInfo($id);
			}
			
			$this->{$type}[$id] = $account;
		}
		return $account;
	}
	
	private function getAdGroupName($jsonObj, $account, $id, $campaign)
	{
		if($id > 0)
		{
			return $account->GetAdGroupName($id, $account->GetCampaignID($campaign));
		}else{
			$id = ($id * -1) - 1;
			return @$jsonObj->newAdGroups[$id]->name;
		}
	}
	
	private function getCampaignName($jsonObj, $account, $id)
	{
		if($id > 0)
		{
			return $account->GetCampaignName($id);
		}else{
			$id = ($id * -1) - 1;
			return $jsonObj->newCampaigns[$id]->name;
		}
	}
	
	private function getCampaignID(&$jsonObj, $account, $id)
	{
		if($id > 0)
			return $id;

		$id = ($id * -1) - 1;
		if(!isset($jsonObj->newCampaigns[$id]->dbCampaignID))
			$jsonObj->newCampaigns[$id]->dbCampaignID = $account->GetCampaignID($jsonObj->newCampaigns[$id]->name);

		return $jsonObj->newCampaigns[$id]->dbCampaignID;
	}
	
	private function getAdGroupID(&$jsonObj, $account, $id)
	{
		if($id > 0)
			return $id;

		$id = ($id * -1) - 1;
		if(!isset($jsonObj->newAdGroups[$id]->dbAdGroupID))
			$jsonObj->newAdGroups[$id]->dbAdGroupID = $account->GetAdGroupID($jsonObj->newAdGroups[$id]->name, $this->getCampaignID($jsonObj, $account, $jsonObj->newAdGroups[$id]->curCampaignID));

		return $jsonObj->newAdGroups[$id]->dbAdGroupID;
	}
	
	private function getCampaignIDAPI(&$jsonObj, $account, $id)
	{
		if($id > 0)
		{
			$jsonObj->newCampaigns[$id]->apiCampaignID = $account->GetCampaignIDAPI($account->GetCampaignName($id));
		}else{
			$id = ($id * -1) - 1;
			if(!isset($jsonObj->newCampaigns[$id]->apiCampaignID))
				$jsonObj->newCampaigns[$id]->apiCampaignID = $account->GetCampaignIDAPI($jsonObj->newCampaigns[$id]->name);
		}
					
		return $jsonObj->newCampaigns[$id]->apiCampaignID;
	}
	
	private function getAdGroupIDAPI(&$jsonObj, $account, $id)
	{
		if($id > 0)
		{
			if(!isset($jsonObj->newAdGroups[$id]))
			{
				$adgroup = $account->getAdGroupByID($id);
				$temp = new stdClass();
				$temp->curCampaignID = $adgroup->CampaignID;
				$jsonObj->newAdGroups[$id] = $temp;
			}
			$jsonObj->newAdGroups[$id]->apiAdGroupID = $account->GetAdGroupIDAPI($account->GetAdGroupName($id, $this->getCampaignID($jsonObj, $account, $jsonObj->newAdGroups[$id]->curCampaignID)), $this->getCampaignIDAPI($jsonObj, $account, $jsonObj->newAdGroups[$id]->curCampaignID));
		}else{
			$id = ($id * -1) - 1;
			if(!isset($jsonObj->newAdGroups[$id]->apiAdGroupID))
				$jsonObj->newAdGroups[$id]->apiAdGroupID = $account->GetAdGroupIDAPI($jsonObj->newAdGroups[$id]->name, $this->getCampaignIDAPI($jsonObj, $account, $jsonObj->newAdGroups[$id]->curCampaignID));
		}
		return $jsonObj->newAdGroups[$id]->apiAdGroupID;
	}
	
}



?>