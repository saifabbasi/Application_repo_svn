<div id="pageinfo" class="sub">
	<h2>Please Login to Continue.</h2>	
</div>

<div class='content'>
<?php if($this->ErrorBADPASS):?>
<h3>Please try again.</h3>
<?php else:?>
<h3>&nbsp;</h3>
<?php endif?>

<form method="post" action="/BevoMedia/Admin/ProcessLogin.html"
	name="registerForm" 
	class="registerForm">

<table>
<tbody>
	<tr>
	<td class="td" width="100" align="right">Username:</td>
	<td class="td"><input name="Username" size="30" maxlength="255" tabindex="1" class="effect" alt="email" type="text"></td>
</tr>

<tr>
	<td class="td" align="right">Password:</td>
	<td class="td"><input name="Password" size="30" tabindex="2" class="effect" alt="blank" type="password"></td>
</tr>
<tr>
	<td class="td" colspan="2" style="text-align: center;">
		<input name="loginFormSubmit" value=" Login " class="baseeffectEx" type="submit">

			</td>
		</tr>
		<tr><td class="td" colspan="2">&nbsp;</td></tr>
		<tr>
			<td class="td" colspan="2" align="right">Forgot Password? <a href="ForgotPassword.html" class="navigation">Click here</a></td>
		</tr>
	</tbody>
</table>

</form>

</div>