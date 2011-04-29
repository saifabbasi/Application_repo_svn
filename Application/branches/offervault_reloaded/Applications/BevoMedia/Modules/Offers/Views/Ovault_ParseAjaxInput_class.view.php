<?php /* it's soapdesigned.com */

/** Writes stuff to the database, like saving an offer to a list or creating new offer lists.
  */

class ParseAjaxInput {
	
	/*output*/
	private function out($out=false) {		
		if(!$out)
			$out['error'] = 'An error occured, please try again.';
		return $out;
	}
	
	/*public*/
	public function createnewlist($query=false) {
	
		$out = array();
		$newname = $query['params']['newlistname']; //this has already been sanitized in AjaxPutContent
		$userid = $_SESSION['User']['ID'];		
				
		$sql = "INSERT INTO 
				bevomedia_user_offer_savelists(
					user__id,
					name
				)
			VALUES (
				'$userid',
				'$newname'
			)
			";
		
		$result = mysql_query($sql);

		if($result) {
			$out['message'] = 'New Offer List created!';
			$out['listid'] = mysql_insert_id();
			
		} else	$out['error'] = 'List could not be created, please try again and contact us if the error persists.';
		
		return $out;
		
	}//createnewlist()
}
