<?php

class StatImport {
	private $statsTable = 'bevomedia_user_aff_network_stats';
	private $subIdStatsTable = 'bevomedia_user_aff_network_subid';
	
	private $tableToStatBinding = array(
		'offer__id'=>'offerId', 'subId'=>'subId', 'revenue'=>'revenue', 'clicks'=>'clicks', 'conversions'=>'conversions');
	
	private $networkId = 0;
	private $userId = 0;
	
	public function __construct($NetworkID, $UserID)
	{
		$this->networkId = $NetworkID;
		$this->userId = $UserID;
	}
	
	public function processStatEnvelope($StatEnvelope)
	{
	    $stats = array();
	    if(empty($StatEnvelope))
	        throw new Exception("Unable to parse stats envelope: " . print_r($StatEnvelope, true));
		$StatDate = date('Y-m-d', strtotime($StatEnvelope->Date()));
		$Sql = 'DELETE FROM ' . $this->statsTable
			 . ' WHERE user__id = ' . $this->userId . ' AND network__id = ' . $this->networkId . ' AND statDate = "' . $StatDate . '"';
		$Result = mysql_query($Sql);
		if(!$Result){
			die(mysql_error());
		}
		$Sql = 'DELETE FROM ' . $this->subIdStatsTable
			 . ' WHERE user__id = ' . $this->userId . ' AND network__id = ' . $this->networkId . ' AND statDate = "' . $StatDate . '"';
		$Result = mysql_query($Sql);
		if(!$Result){
			die(mysql_error());
		}
		foreach($StatEnvelope->Stats() as $Stat)
		{
			$ColumnNames = '';
			$ColumnValues = '';
			foreach($this->tableToStatBinding as $columnName=>$statParam)
			{
				if($Stat->{$statParam} != NULL)
				{
					$ColumnNames .= $columnName . ',';
					$ColumnValues .= "'" . mysql_escape_string($Stat->{$statParam}) . "',";
				}
			}
			
			$ColumnNames .= 'network__id,user__id,statDate';
			$ColumnValues .= $this->networkId . ',' . $this->userId . ',"' . $StatDate . '"';
			$Sql = "INSERT INTO " . $this->subIdStatsTable . " (" . $ColumnNames . ") VALUES (" . $ColumnValues . ")";
			$Result = mysql_query($Sql);
			if(!$Result){
				die(mysql_error());
			}
			$stats[] = $Stat;
		}
		
		$ColumnNames = 'network__id,user__id,statDate,clicks,conversions,revenue';
		$ColumnValues = $this->networkId . ',' . $this->userId . ',"' . $StatDate . '",'
						. $StatEnvelope->TotalStats()->clicks . ',' . $StatEnvelope->TotalStats()->conversions . ',' . $StatEnvelope->TotalStats()->revenue;
		
		$Sql = 'INSERT INTO ' . $this->statsTable . ' (' . $ColumnNames .') VALUES (' . $ColumnValues . ')';
		$Result = mysql_query($Sql);
		if(!$Result){
			die(mysql_error());
		}
		return $stats;
	}
}
?>