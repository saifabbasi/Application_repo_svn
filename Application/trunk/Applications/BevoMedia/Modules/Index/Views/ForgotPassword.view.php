<div id="pageinfo" class="sub">
	<h2>Forgot Your Password?</h2>	
</div>

<div class="clear"></div>


<br/><br/>

<div class='content'>
An email will be sent to your provided email address with
instructions on reseting your password.

<?php if($this->Message == 'EMAIL_NOT_FOUND'):?>
<h3>
	Your email address was not found in our system.  Please try again.
</h3>
<?php elseif($this->Message == 'EMAIL_SENT'):?>
<h3>
	An email has been sent to <?php echo $_POST['Email']?> with instructions on how to reset your password. Be sure to check your spambox. If you haven't recieved a message in 15 minutes, please contact Bevo Support at <a href="mailto:help@bevomedia.com">help@bevomedia.com</a>.
</h3>
<?php endif?>

<form method="post" name="registerForm" class="registerForm">

<table>
<tbody>
	<tr><td class="td" colspan="2">&nbsp;</td></tr>
					<tr>
						<td class="td" width="100" align="right">Email:</td>
						<td class="td"><input name="Email" size="30" maxlength="255" tabindex="1" class="effect" alt="email" type="text" /></td>
					</tr>

					
					<tr>
						<td>&nbsp;</td>
						<td class="td" style="text-align: center;">
							<input name="forgotPasswordSubmit" class="formSubmit" type="submit" />
						
						</td>
					</tr>
					<tr><td class="td" colspan="2">&nbsp;</td></tr>
					<tr>
						<td class="td" colspan="2" align="right">Forgot Password? <a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/ForgotPassword.html" class="navigation">Click here</a></td>
					</tr>
				</tbody>
			</table>

</form>

</div>