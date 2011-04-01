<?php

set_time_limit(0);

class MailChecker {

	var $strServer;
	var $intPort;
	var $strUser;
	var $strPass;
	var $objConn;
	var $objHeaders;
	var $intMessageCount;
	var $intCurrMessage;
	
	function MailChecker($strInServer = 'localhost', $intInPort = 110, $strInUser = '', $strInPass = '') {
		$this->strServer = $strInServer;
		$this->intPort = $intInPort;
		$this->strUser = $strInUser;
		$this->strPass = $strInPass;
		
		$this->intMessageCount = 0;
		$this->intCurrMessage = 0;
	}
	
	function Open() {
		$this->objConn = imap_open('{' . $this->strServer . ':' . $this->intPort . '/pop3/novalidate-cert}INBOX', $this->strUser, $this->strPass) or die (imap_last_error());
		
		if (!$this->objConn) {
			return false;
		}
		
		$this->objHeaders = imap_headers($this->objConn);
		
		if (!$this->objHeaders) {
			return false;
		}
		
		$this->intMessageCount = sizeof($this->objHeaders);
	}
	
	function GetMessage() {
		if ($this->intMessageCount == 0) {
			return false;
		}
		
		if ($this->intCurrMessage >= $this->intMessageCount) {
			return false;
		}
		
		$this->intCurrMessage++;
		$objThisHeader = imap_headerinfo($this->objConn, $this->intCurrMessage);
		
		if (!$objThisHeader) {
			return false;
		}
		
		$arrThisFrom = $objThisHeader->from;
		if (!empty($arrThisFrom)) {
			if (sizeof($arrThisFrom) > 0) {
				$strThisFrom = $arrThisFrom[0]->mailbox . '@' . $arrThisFrom[0]->host;
			}
			else {
				$strThisFrom = $objThisHeader->fromaddress;
			}
		}
		else {
			$strThisFrom = $objThisHeader->fromaddress;
		}
		
		$strThisSubject = $objThisHeader->subject;
		$strThisDate = $objThisHeader->date;
		
		$objStructure = imap_fetchstructure($this->objConn, $this->intCurrMessage);
		if (!empty($objStructure->parts)) {
			$intPartCount = count($objStructure->parts);
			
			$arrAttachments = array();
			for ($intX = 0; $intX < $intPartCount; $intX++) {
				$objThisPart = $objStructure->parts[$intX];
				
				// Fetch Email Plaintext
				if ($objThisPart->subtype == 'PLAIN') {
					$strThisBody = imap_fetchbody($this->objConn, $this->intCurrMessage, $intX+1);
					continue;
				}
				
				// Fetch Attachment
				$objThisAttach = array();
				
				// Get Filename
				if ($objThisPart->ifdparameters) {
					foreach ($objThisPart->dparameters as $objParam) {
						if (strtolower($objParam->attribute) == 'filename') {
							$objThisAttach['filename'] = $objParam->value;
						}
					}
				}
				
				// Get Name
				if ($objThisPart->ifparameters) {
					foreach ($objThisPart->parameters as $objParam) {
						if (strtolower($objParam->attribute) == 'name') {
							$objThisAttach['name'] = $objParam->value;
						}
					}
				}
				
				// Get Attachment
				if (strlen($objThisAttach['name']) > 0 || strlen($objThisAttach['filename']) > 0) {
					$objThisAttach['attachment'] = imap_fetchbody($this->objConn, $this->intCurrMessage, $intX+1);
				
					if ($objThisPart->encoding == 3) {
						$objThisAttach['attachment'] = base64_decode($objThisAttach['attachment']);
					}
					if ($objThisPart->encoding == 4) {
						$objThisAttach['attachment'] = quoted_printable_decode($objThisAttach['attachment']);
					}
					
					$arrAttachments[] = $objThisAttach;
				}
			}
		}
		else {
			$strThisBody = imap_body($this->objConn, $this->intCurrMessage);
		}
		
		$arrThisMessage = array('From' => $strThisFrom, 'Subject' => $strThisSubject, 'Date' => $strThisDate, 'Body' => $strThisBody, 'Attachments' => $arrAttachments);
		
		return $arrThisMessage;
	}
	
	function Delete() {
		if (!$this->objConn) {
			return false;
		}
		
		if ($this->intCurrMessage > $this->intMessageCount) {
			return false;
		}
		
		imap_delete($this->objConn, $this->intCurrMessage);
	}
	
	function DeleteAll() {
		if (!$this->objConn) {
			return false;
		}

		for ($intX = 1; $intX < $this->intMessageCount+1; $intX++) {
			imap_delete($this->objConn, $intX);
		}
	}
	
	function Expunge() {
		if (!$this->objConn) {
			return false;
		}
		
		imap_expunge($this->objConn);
	}
	
	function Close() {
		if (!$this->objConn) {
			return false;
		}
		
		imap_close($this->objConn);
	}
}

?>