<?php
Class CloakRedirect
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
		$Sql = "SELECT COUNT(id) AS C FROM bevomedia_cloak WHERE publicId = '{$publicId}' ";
		$Query = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Query);
		return( $Row['C'] == 0);
	}
	
	Public Function GetdestinationFromPublicID($publicId)
	{
		$Sql = "SELECT destination FROM bevomedia_cloak WHERE publicId = '{$publicId}';";
		$Query = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Query);
		if(!isset($Row['destination']))
			return false;
		else
			return $Row['destination'];
	}
	
	Public Function Insert($User_ID, $destination)
	{
		$publicId = $this->GenerateUniquePublicID();
		$Sql = "INSERT INTO bevomedia_cloak (`userId`, `publicId`, `destination`) VALUES ({$User_ID}, '{$publicId}', '{$destination}')";
		mysql_query($Sql);
		return $publicId;
	}
	
	Public Function GenerateUniquePublicID()
	{
		while($this->IsPublicIDUnique($ID = $this->GeneratePublicID()) == false);
		return $ID;
	}
	
}
?>