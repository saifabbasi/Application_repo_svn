<?php
Class DirectLink
{
	Public Function GeneratePublicID()
	{
		$Charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$Length = 32;
		
		$Output = '';
		while(strlen($Output)<$Length)
			$Output .= $Charset[rand(0,strlen($Charset)-1)];
			
		return $Output;
	}
	
	Public Function IsPublicIDUnique($publicId)
	{
		$Sql = "SELECT COUNT(id) AS C FROM bevomedia_directlink WHERE publicId = '{$publicId}' ";
		$Query = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Query);
		return( $Row['C'] == 0);
	}
	
	Public Function GetdestinationFromPublicID($publicId)
	{
		$Sql = "SELECT destination FROM bevomedia_directlink WHERE publicId = '{$publicId}';";
		$Query = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Query);
		if(!isset($Row['destination']))
			return false;
		else
			return $Row['destination'];
	}
	
	Public Function Insert($userId, $destination, $unique = false)
	{
		$publicId = $this->GenerateUniquePublicID();
		$Sql = "INSERT INTO bevomedia_directlink (`user__id`, `publicId`, `destination`, `uniqueVisitors`) VALUES ({$userId}, '{$publicId}', '{$destination}', '{$unique}')";
		mysql_query($Sql);
		return $publicId;
	}
	
	Public Function UpdateAdVarID($dlID, $avID)
	{
		$Sql = "UPDATE bevomedia_directlink SET adVariationID = '{$avID}' WHERE publicId = '{$dlID}' LIMIT 1";
		mysql_query($Sql);
	}
	
	Public Function GenerateUniquePublicID()
	{
		while($this->IsPublicIDUnique($ID = $this->GeneratePublicID()) == false);
		return $ID;
	}
	
}

?>