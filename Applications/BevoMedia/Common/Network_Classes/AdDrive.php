<?php 

include_once('ScrapeAbstract.php');

class AdDrive extends ScrapeAbstract
{
    protected $_login_endPoint = 'http://www.addrive.com/login';
    protected $_stats_endPoint = 'http://www.addrive.com/reports/exportSubIdReportCsv/1/YES/'; //2011-01-25/2011-01-25
    protected $_offers_endPoint = 'http://www.addrive.com/offers/index/All';
    protected $_offerDetails_endPoint = 'http://www.addrive.com/offers/offerDetails/'; //41823
    
    private $_username = 'ryan@bevomedia.com';
    private $_password = 'bevo1025';
    
    private $_statDate;
    
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
        $loginPostArray['data[AddriveUserDetail][Username]'] = $this->getUsername();
        $loginPostArray['data[AddriveUserDetail][PASSWORD]'] = $this->getPassword();
        return $loginPostArray;
    }
    
    protected function _getStatsPostArray()
    {
        $startDate = $this->_statDate;
        $endDate = $this->_statDate;
        
        $this->_stats_endPoint .= $startDate->format('Y-m-d') . '/' . $startDate->format('Y-m-d');
        /*
        $statsPostArray = array();
        $statsPostArray['submit'] = 'EXPORT TO CSV';
        $statsPostArray['showSub'] = 'YES';
        // Start date
        $statsPostArray['sm'] = $startDate->format('m');
        $statsPostArray['sd'] = $startDate->format('d');
        $statsPostArray['sy'] = $startDate->format('Y');
        // End date
        $statsPostArray['em'] = $endDate->format('m');
        $statsPostArray['ed'] = $endDate->format('d');
        $statsPostArray['ey'] = $endDate->format('Y');
        return $statsPostArray;
        */
        return array();
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
    public function getStats($date = '')
    {
        if ($date == '') {
            $this->_statDate = new DateTime();
        }else{
            $this->_statDate = new DateTime($date);
        }
        
        $body = $this->getStatsBody();
        $bodyRows = explode("\n", $body);
        
        $stats = array();
        
        $headerRow = false;
        foreach($bodyRows as $row) {
            if(trim($row) == '') {
                continue;
            }
            
            if(strpos($row, 'SubId data from') !== false ) {
                continue;
            }
            
            $columns = explode(",", $row);
            if($columns[0] == 'OfferId') {
                $headerRow = $columns;
                continue;
            }
            
            $campaignId = $columns[0];
            $subId = trim($columns[2], '"');
            $payout = $columns[5];
            
            
            $clicks = $columns[3];
            $converts = $columns[4];
        
            if(isset($stat[$campaignId])) {
                $clicks = $sta[$campaignId]['Clicks'] + $clicks;
            }
            if(isset($stat[$campaignId])) {
                $converts = $sta[$campaignId]['Conversions'] + $converts;
            }
                        
            $stat = array();
            $stat['CampaignID'] = $campaignId;
            $stat['SubID'] = $subId;
            $stat['Clicks'] = $clicks;
            $stat['Conversions'] = $converts;
            $stat['Payout'] = $payout;
            
            $stats[$campaignId] = $stat;
        }
        
        $statEnvelope = new StatEnvelope();
        $statEnvelope->setDate($this->_statDate->format('Y-m-d'));
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

            if(preg_match('/\/offers\/offerDetails\/(.*?)">/', $row, $matches)) {
                if(!in_array($matches[1], $offers)) {
                    $offers[] = $matches[1];
                }
            }
            
        }
        
		$offerEnvelope = new OfferEnvelope();
        foreach($offers as $offer) {
            $body = $this->getOfferDetailBody($offer);
            
            $title = $this->getNodeValue('/html/body/div/div[2]/div/div[2]/strong', $body);
            $description = $this->getNodeValue('/html/body/div/div[2]/div/table[3]/tr/td', $body);
            $payout = $this->getNodeValue('/html/body/div/div[2]/div/table/tr[2]/td', $body);
            $expirationDate = $this->getNodeValue('/html/body/div/div[2]/div/table/tr[2]/td[2]', $body);
            $previewUrl = $this->getNodeAttribute('[@id="ViewLP-Rollover"]', 'href', $body);

            if ($title == '') {
                continue;
            }
            
			$offerObj = new Offer();
			$offerObj->offerId = $offer;
			$offerObj->name = trim($title);
			$offerObj->description = trim($description);
			$offerObj->payout = str_replace('$', '', $payout);
			$offerObj->previewUrl = trim($previewUrl);
			$offerObj->expireDate = $expirationDate;

			$offerEnvelope->addOfferObject($offerObj);
        }
        
        return $offerEnvelope;
    }
}