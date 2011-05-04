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
	public function save2list($query=false) {
		
		$listid = $query['params']['list'];
		$oid = $query['params']['oid'];
		$out = array();
		
		//check if offer already exists in this list
		$sql = "SELECT COUNT(*)
			FROM bevomedia_user_offerlists_offers
			WHERE 	(list__id = {$listid}) AND
				(offer__id = {$oid})		
		";
		$result = mysql_query($sql);
		$num = mysql_fetch_row($result);
		
		if($num[0] != 0)
			$out['error'] = 'Offer is already in this list!';
		
		else {
			$sql = "INSERT INTO 
					bevomedia_user_offerlists_offers(
						offer__id,
						list__id
					)
				VALUES (
					'$oid',
					'$listid'
				)
				";
			
			$result = mysql_query($sql);
	
			if($result)
				$out['message'] = 'Offer added.';	
			else	$out['error'] = 'Offer could not be added. Please try again and let us know if you keep getting this error.';
		}
		
		return $out;
		
	}//save2list()
	
	public function createnewlist($query=false) {
	
		$newname = $query['params']['newlistname']; //this has already been sanitized in AjaxPutContent
		$userid = $_SESSION['User']['ID'];
		
		/* dont need to check for verified - all get 10 lists!
		$user = new User();
		$user->getInfo($_SESSION['User']['ID']);
		$this->{'User'} = $user;
		
		$maxlists = $this->User->vaultID == 0 ? 1 : 10;
		*/
		
		$maxlists = 10;
		
		$out = array();
		$listnum = '';

		//get no. of offers in list
		$sql = "SELECT 
				COUNT(*)
			FROM 
				bevomedia_user_offerlists 
			WHERE 
				(bevomedia_user_offerlists.user__id = {$userid})
			";					
		$result = mysql_query($sql);
		$listnum = mysql_fetch_row($result);
		$listnum = $listnum[0];
				
		if($listnum < $maxlists) {
			$sql = "INSERT INTO 
					bevomedia_user_offerlists(
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
		
		} else { //if no more lists allowed
			$out['error'] = 'Sorry, but you can\'t have more than '.$maxlists.' Offer List(s) at the same time. You can delete old lists to make room for new ones.';
		}
		
		return $out;
		
	}//createnewlist()
	
	public function deletelist($query=false) {
	
		$listid = $query['params']['listid'];
		$userid = $_SESSION['User']['ID'];
		
		$sql = "DELETE	lists, offers
			
			FROM	bevomedia_user_offerlists AS lists
			
			LEFT JOIN
				bevomedia_user_offerlists_offers AS offers
				ON offers.list__id = lists.id
				
			WHERE	(lists.id = {$listid}) AND
				(lists.user__id = {$userid})
			";
		$result = mysql_query($sql);
	
		if($result) {
			$rows = mysql_affected_rows() - 1; //we dont count the list row
			
			$out['message'] = 'List deleted! It contained '.$rows.' offers. Off to new lands.';		
			
		} else	$out['error'] = 'List could not be deleted, please try again and contact us if the error persists.';
		
		return $out;
		
	}//deletelist()
	
	public function deletealllists($query=false) {
	
		$userid = $_SESSION['User']['ID'];
		
		$sql = "DELETE	lists, offers
			
			FROM	bevomedia_user_offerlists AS lists
			
			LEFT JOIN
				bevomedia_user_offerlists_offers AS offers
				ON offers.list__id = lists.id
				
			WHERE	(lists.user__id = {$userid})
			";
		$result = mysql_query($sql);
	
		if($result) {
			//$rows = mysql_affected_rows() - 1; //later... see if we can get affected rows by table without running 2 queries
			
			$out['message'] = 'All offer lists deleted! Off to a brand new start.';		
			
		} else	$out['error'] = 'It seems like you don\'t have any lists to delete! Please contact us if you are getting this message in error.';
		
		return $out;
		
	}//deletelist()
	
	public function deletesavelistoffer($query=false) {
	
		$oid = $query['params']['oid'];
		$listid = $query['params']['listid'];
		$userid = $_SESSION['User']['ID'];
		
		$sql = "DELETE	offers
			
			FROM	bevomedia_user_offerlists_offers AS offers
			
			LEFT JOIN
				bevomedia_user_offerlists AS lists
				ON offers.list__id = lists.id
				
			WHERE	lists.id = {$listid} AND
				lists.user__id = {$userid} AND
				offers.offer__id = {$oid}
		";
			
		$result = mysql_query($sql);
	
		if(mysql_affected_rows() == 1)
			$out['message'] = 'Offer removed from list.';
		else	$out['error'] = 'The offer could not be deleted, please try again and contact us if the error persists.';
		
		return $out;
		
	}//deletesavelistoffer()
	
	public function renamelist($query=false) {
	
		$listid = $query['params']['listid'];
		$newlistname = $query['params']['newlistname'];
		$userid = $_SESSION['User']['ID'];
		
		$sql = "UPDATE	bevomedia_user_offerlists
			
			SET	name = '$newlistname'
			
			WHERE	id = $listid AND
				user__id = $userid
				
			LIMIT 1
		";
			
			//die($sql);
			
		$result = mysql_query($sql);
	
		if(mysql_affected_rows() == 1) {
			$out['message'] = 'List renamed to <em>'.$newlistname.'</em>';
			$out['newlistname'] = $newlistname;
			
		} else	$out['error'] = 'The list could not be renamed, please refresh the page and try again!';
		
		return $out;
		
	}//deletesavelistoffer()
}
