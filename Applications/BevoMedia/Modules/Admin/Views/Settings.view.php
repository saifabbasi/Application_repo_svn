
<form method="post">
	
	<input type="hidden" name="Auto_Accept_New_Applicants_HiddenField" value="" />
	
	<table width="100%">
	<tr>
		<td colspan="2">
			<label>
				<input type="checkbox" name="Auto_Accept_New_Applicants" value="1" <?=($this->Data['Auto_Accept_New_Applicants']=='1')?'checked':''?> /> Auto Accept New Applicants
			</label>
		</td>
	</tr>
	<tr>
		<td width="300">
			<label>
				MSN Dev Token:
			</label>
		</td>
		<td>
			<input type="text" name="MSN_Dev_Token" value="<?=($this->Data['MSN_Dev_Token'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Google Adwords Developer Token:
			</label>
		</td>
		<td>
			<input type="text" name="Google_Adwords_Developer_Token" value="<?=($this->Data['Google_Adwords_Developer_Token'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Google Adwords Application Token:
			</label>
		</td>
		<td>
			<input type="text" name="Google_Adwords_Application_Token" value="<?=($this->Data['Google_Adwords_Application_Token'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Yahoo PPC Username:
			</label>
		</td>
		<td>
			<input type="text" name="Yahoo_PPC_Username" value="<?=($this->Data['Yahoo_PPC_Username'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Yahoo PPC Password:
			</label>
		</td>
		<td>
			<input type="text" name="Yahoo_PPC_Password" value="<?=($this->Data['Yahoo_PPC_Password'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Yahoo PPC Master Account ID:
			</label>
		</td>
		<td>
			<input type="text" name="Yahoo_PPC_Master_Account_ID" value="<?=($this->Data['Yahoo_PPC_Master_Account_ID'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Yahoo PPC Account ID:
			</label>
		</td>
		<td>
			<input type="text" name="Yahoo_PPC_Account_ID" value="<?=($this->Data['Yahoo_PPC_Account_ID'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Yahoo PPC License:
			</label>
		</td>
		<td>
			<input type="text" name="Yahoo_PPC_License" value="<?=($this->Data['Yahoo_PPC_License'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Yahoo PPC Sandbox Username:
			</label>
		</td>
		<td>
			<input type="text" name="Yahoo_PPC_Sandbox_Username" value="<?=($this->Data['Yahoo_PPC_Sandbox_Username'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Yahoo PPC Sandbox Password:
			</label>
		</td>
		<td>
			<input type="text" name="Yahoo_PPC_Sandbox_Password" value="<?=($this->Data['Yahoo_PPC_Sandbox_Password'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Yahoo PPC Sandbox Master Account ID:
			</label>
		</td>
		<td>
			<input type="text" name="Yahoo_PPC_Sandbox_Master_Account_ID" value="<?=($this->Data['Yahoo_PPC_Sandbox_Master_Account_ID'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Yahoo PPC Sandbox Account ID:
			</label>
		</td>
		<td>
			<input type="text" name="Yahoo_PPC_Sandbox_Account_ID" value="<?=($this->Data['Yahoo_PPC_Sandbox_Account_ID'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Yahoo PPC Sandbox License:
			</label>
		</td>
		<td>
			<input type="text" name="Yahoo_PPC_Sandbox_License" value="<?=($this->Data['Yahoo_PPC_Sandbox_License'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Google Adwords Client Email:
			</label>
		</td>
		<td>
			<input type="text" name="Google_Adwords_Client_Email" value="<?=($this->Data['Google_Adwords_Client_Email'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Google Adwords Client Password:
			</label>
		</td>
		<td>
			<input type="text" name="Google_Adwords_Client_Password" value="<?=($this->Data['Google_Adwords_Client_Password'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Premium Cost:
			</label>
		</td>
		<td>
			<input type="text" name="Premium_Cost" value="<?=(@$this->Data['Premium_Cost'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Premium API Units:
			</label>
		</td>
		<td>
			<input type="text" name="Premium_API_Units" value="<?=(@$this->Data['Premium_API_Units'])?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label>
				Basic API Units:
			</label>
		</td>
		<td>
			<input type="text" name="Basic_API_Units" value="<?=(@$this->Data['Basic_API_Units'])?>" />
		</td>
	</tr>
	
	
	<tr>
		<td colspan="2">
			<input type="submit" name="Submit" value="Save" />
		</td>
	</tr>
	</table>
</form>
