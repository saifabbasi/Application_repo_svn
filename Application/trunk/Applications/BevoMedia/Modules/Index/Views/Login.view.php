<div id="pageinfo" class="sub">
	<h2>Please Login to Continue.</h2>	
</div>

<div class="clear"></div>


<br/><br/>

<div class='content'>
<?php if($this->ErrorDISABLED):?>
<h3>Your account is currently disabled.</h3>
<p>
	If you have just registered then this means your account is still pending review.
</p>
<?php elseif($this->Email):?>
<h3>
	Password successfully changed.
</h3>
<?php else:?>
<h3>Please try again.</h3>
<?php endif?>

<form method="post" action="<?=$this->{'System/BaseURL'}?><?=$this->{'Application/Theme'};?>/User/ProcessLogin.html"
	name="registerForm" 
	class="registerForm">

<table>
<tbody><tr>
						<td class="th" colspan="2">Member's Login</td>
					</tr>
					<tr><td class="td" colspan="2">&nbsp;</td></tr>
					<tr>
						<td class="td" width="100" align="right">Email:</td>
						<td class="td"><input name="Email" size="30" maxlength="255" tabindex="1" class="effect" alt="email" type="text"></td>
					</tr>

					<tr>
						<td class="td" align="right">Password:</td>
						<td class="td"><input name="Password" size="30" tabindex="2" maxlength="20" class="effect" alt="blank" type="password"></td>
					</tr>
					
					<tr>
						<td class="td" align="right">Remember me:</td>
						<td class="td"><input type="checkbox" name="Remember" alt="blank" /></td>
					</tr>
					
					<tr>
						<td class="td" colspan="2" style="text-align: center;">
							<input name="loginFormSubmit" value=" Login " class="baseeffectEx" type="submit">
							<input name="Register" value=" Register " class="baseeffectEx" onclick="location.href='<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/User/Register.html'" type="button">

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