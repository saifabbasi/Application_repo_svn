<div id="pageinfo" class="sub">
	<h2>Reset Your Password</h2>	
</div>

<div class="clear"></div>


<br/><br/>

<div class='content'>
Please enter the code that you received in your email as well as your email address to reset your password.

<?php if($this->Message == 'EMAIL_NOT_FOUND'):?>
<h3>
	Your email address was not found in our system.
	Please try again.
</h3>
<?php elseif($this->Message == 'BAD_CODE'):?>
<h3>
	This verification code does not match the one we have stored in our system.
	Please try again.
	</h3>
<?php elseif($this->Message == 'PASSWORD_CHANGED'):?>
<h3>
	Your password has been successfully changed.<br/>
	<a href='<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/Login.html'>Login</a>
</h3>
<?php endif?>

<form method="post" 
	name="registerForm" 
	class="registerForm">

<table>
<tbody>
	<tr><td class="td" colspan="2">&nbsp;</td></tr>
					<tr>
						<td class="td" width="100" align="right">Email:</td>
						<td class="td"><input name="Email" size="30" maxlength="255" tabindex="1" class="effect" alt="email" value="<?php echo isset($_GET['Email'])?$_GET['Email']:''?>" type="text" /></td>
					</tr>

					<tr>
						<td class="td" width="100" align="right">Email Code:</td>
						<td class="td"><input name="EmailCode" size="30" maxlength="255" tabindex="1" class="effect" alt="email" value='<?php echo isset($_GET['EmailCode'])?$_GET['EmailCode']:''?>' type="text" /></td>
					</tr>
					
					<tr>
						<td class="td" width="100" align="right">New Password:</td>
						<td class="td"><input name="Password" size="30" maxlength="255" tabindex="1" class="effect" alt="email" type="password" /></td>
					</tr>
					
					<tr>
						<td class="td" colspan="2" style="text-align: center;">
							<input name="resetPasswordSubmit" class="baseeffectEx" type="submit" />
						
						</td>
					</tr>
					<tr><td class="td" colspan="2">&nbsp;</td></tr>
				</tbody>
			</table>

</form>

</div>