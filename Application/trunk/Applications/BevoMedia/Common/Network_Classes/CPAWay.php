<?php 

include_once('ScrapeAbstract.php');
include_once('../StatImport.class.php');
include_once('../OfferImport.class.php');
include_once('../Response_Objects/StatEnvelope.php');
include_once('../Response_Objects/OfferEnvelope.php');

class CPAWay extends ScrapeAbstract
{
    protected $_login_endPoint = 'https://portal.cpaway.com/go/login';
    protected $_stats_endPoint = 'https://portal.cpaway.com/go/report_detail';
    protected $_offers_endPoint = 'https://portal.cpaway.com/go/campaigns';
    protected $_offerDetails_endPoint = 'https://portal.cpaway.com/go/campaign_view&id=';
    
    private $_username = 'ryan@bevomedia.com';
    private $_password = 'bevo1025';
    
    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }
    
    public function getUsername()
    {
        return $this->_username;
    }
    
    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }
    
    public function getPassword()
    {
        return $this->_password;
    }
    
    protected function _getLoginPostArray()
    {
        $loginPostArray = array();
        $loginPostArray['email'] = $this->getUsername();
        $loginPostArray['password'] = $this->getPassword();
        return $loginPostArray;
    }
    
    protected function _getStatsPostArray()
    {
        // TODO: Allow specification of date range
        $startDate = new DateTime();
        $endDate = new DateTime();
        
        $statsPostArray = array();
        $statsPostArray['submit'] = 'EXPORT TO CSV';
        $statsPostArray['camp'] = 'all';
        // Start date
        $statsPostArray['sm'] = $startDate->format('m');
        $statsPostArray['sd'] = $startDate->format('d');
        $statsPostArray['sy'] = $startDate->format('Y');
        // End date
        $statsPostArray['em'] = $endDate->format('m');
        $statsPostArray['ed'] = $endDate->format('d');
        $statsPostArray['ey'] = $endDate->format('Y');
        return $statsPostArray;
    }
    
    protected function _getOffersPostArray()
    {
        $offersPostArray = array();
        return $offersPostArray;
    }
    
    /**
     * Method to get the stats for this affiliate network
     * @return string
     */
    public function getStats()
    {
        $body = $this->getStatsBody();
        $bodyRows = explode("\n", $body);
        
        $stats = array();
        
        $headerRow = false;
        foreach($bodyRows as $row) {
            if(trim($row) == '') {
                continue;
            }
            
            $columns = explode(",", $row);
            if($columns[0] == 'Campaign ID') {
                $headerRow = $columns;
                continue;
            }
            
            $campaignId = $columns[0];
            $payout = $columns[2];
            $subId = trim($columns[3], '"');
            
            if(isset($stat[$campaignId])) {
                $clicks = $sta[$campaignId]['Clicks'] + 1;
            }else{
                $clicks = 1;
            }
            
            $stat = array();
            $stat['CampaignID'] = $campaignId;
            $stat['SubID'] = $subId;
            $stat['Clicks'] = $clicks;
            $stat['Conversions'] = $clicks;
            $stat['Payout'] = $payout;
            
            $stats[$campaignId] = $stat;
        }
        
        $statEnvelope = new StatEnvelope();
        foreach ($stats as $stat) {
			$TempStat = new Stat($stat['Clicks'], $stat['Conversions'], $stat['Payout'], $stat['SubID'], $stat['CampaignID']);
			$statEnvelope->addStatObject($TempStat);
        }
        
        return $statEnvelope;
    }
    
    public function getOffers()
    {
        $body = $this->getOffersBody();
        $rows = explode("\n", $body);
        $offers = array();
        foreach ($rows as $row) {
            if (trim($row) == '') {
                continue;
            }
            
            if(preg_match('/\/go\/campaign_view&id=(.*?)">View/', $row, $matches)) {
                if(!in_array($matches[1], $offers)) {
                    $offers[] = $matches[1];
                }
            }
            
        }
        
		$offerEnvelope = new OfferEnvelope();
        foreach($offers as $offer) {
            $body = $this->getOfferDetailBody($offer);
            
            $title = $this->getNodeValue('/html/body/div[3]/div[2]/div[2]/div[2]', $body);
            $description = $this->getNodeValue('/html/body/div[3]/div[2]/div[6]/div[2]', $body);
            $payout = $this->getNodeValue('/html/body/div[3]/div[2]/div[3]/div[2]', $body);
            $countries = $this->getNodeValue('/html/body/div[3]/div[2]/div[4]/div[2]', $body);
            $previewUrl = $this->getNodeValue('/html/body/div[3]/div[2]/div[8]/div[2]', $body);
            
            if ($title == '') {
                continue;
            }
            
			$offerObj = new Offer();
			$offerObj->offerId = $offer;
			$offerObj->name = trim($title);
			$offerObj->description = trim($description);
			$offerObj->payout = str_replace('$', '', $payout);
			$offerObj->countries = explode(",", trim($countries));
			$offerObj->previewUrl = trim($previewUrl);
			
			$offerEnvelope->addOfferObject($offerObj);
        }
        
        return $offerEnvelope;
    }
}