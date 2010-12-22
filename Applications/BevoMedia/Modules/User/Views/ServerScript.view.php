<?php
function genRandomString($length = 10) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}
?>
<script>
function updatescript()
{
	$('span#myrp').html($('input#myrp').val());
	$('span#myu').html($('input#myu').val());
	$('span#myp').html($('input#myp').val());
	$('span#mydb').html($('input#mydb').val());
}
$(function() {
	updatescript();
});

</script>

<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/User/RackspaceWizard.html">&laquo; Back to the RackSpace Wizard<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<div class="pagecontent">
	<h3>Launch a new VPS (Rackspace Specific)</h3>
	<ol>
		<li>Sign up for a Rackspace Cloud account</li>
		<li>Pick a size for your server
			<ul>
				<li>256mb RAM - $10/mo</li>
				<li>512mb RAM - $20/mo</li>
				<li>1Gb RAM - $40/mo</li>
				<li>2Gb RAM - $80/mo</li>
		  </ul>
		</li>
		<li>Once your account is created and confirmed, Log into your RackSpace account at <a href="https://manage.rackspacecloud.com/" target="_blank">https://manage.rackspacecloud.com/</a>.</li>
		<li>Select the "Hosting" tab on the left navigation panel. Then Select "Cloud Servers".</li>
		<li>Click "Add Server". Use Ubuntu 10.04 LTS as your operating system.
			<br />
			<a rel="shadowbox" href="/Themes/BevoMedia/img/Step5Ubuntu 10.04.JPG" class="rack-shadowbox">
				<center><img alt="RackSpace API Access Screenshot" src="/Themes/BevoMedia/img/rack_api_thumb.jpg"></center>
			</a>
		</li>
		<li>
			 When prompted, choose your server size and a server name.
			<br />
			 <a rel="shadowbox" href="/Themes/BevoMedia/img/SeverConfig.JPG" class="rack-shadowbox">
				<center><img alt="RackSpace API Access Screenshot" src="/Themes/BevoMedia/img/rack_api_thumb.jpg"></center>
			</a>
		</li>
		<li>When your node is started up and ready, check your email to get the password. Make a note of your IP.</li>
		<li>
			Continue setting up your server below.
		</li>
	</ol>

	
	<h3>To setup any Ubuntu 10.04 or Debian 5.0 server</h3>
	<ol>
		<li><a href="http://the.earth.li/~sgtatham/putty/latest/x86/putty.exe">Download putty</a> and save it to your desktop.
			Putty is a server administration tool commonly used to issue commands to a remote server.</li>
		<li>Open Putty, and in the Server Address box, enter your server's IP and click "Open".</li>
		<li>Once you're prompted for your username, login as ‘root’. Hit enter and copy and paste your server password and hit enter. You are now logged into an SSH shell on your server.
			<br />
			 <a rel="shadowbox" href="/Themes/BevoMedia/img/rootputty.JPG" class="rack-shadowbox">
				<center><img alt="RackSpace API Access Screenshot" src="/Themes/BevoMedia/img/rack_api_thumb.jpg"></center>
			</a>
		</li>
		<li>Optional: Choose the following values, or just leave the defaults.
			<ul>
				<li>MySQL Root Password: <input class="formtxt wide_number" id='myrp' type="text" value="<?= genRandomString(12) ?>" onKeypress='updatescript()' /></li>
				<li>MySQL User Account: <input class="formtxt wide_number" id='myu' type="text" value="bevo_<?= genRandomString(3) ?>" onKeypress='updatescript()' /></li>
				<li>MySQL User Password: <input class="formtxt wide_number" id='myp' type="text" value="bevo_<?= genRandomString(6) ?>" onKeypress='updatescript()' /></li>
				<li>MySQL Database name: <input class="formtxt wide_number" id='mydb' type="text" value="bevodb" onKeypress='updatescript()' /></li>
			</ul>
		</li>
		<li>Copy and paste the following to install BevoMedia and its dependencies:
			<blockquote class="code">MYSQL_ROOT_PASSWORD=<span id='myrp'></span> &&  
	MYSQL_USER=<span id='myu'></span> && 
	MYSQL_PASS=<span id='myp'></span> && 
	MYSQL_DB=<span id='mydb'></span> && 
	IP=$(ifconfig | grep "inet addr" | egrep -v "addr:(10|127)\." | cut -d: -f2 | cut -d\&nbsp;&nbsp;-f1) &&  
	export DEBIAN_FRONTEND=noninteractive && 
	sudo apt-get update -q && sudo apt-get install -qy debconf && 
	echo mysql-server-5.0 mysql-server/root_password select $MYSQL_ROOT_PASSWORD | debconf-set-selections && 
	echo mysql-server-5.0 mysql-server/root_password_again select $MYSQL_ROOT_PASSWORD | debconf-set-selections && 
	sudo apt-get install -qy apache2 php5 php5-curl php5-mysql mysql-server libapache2-mod-php5 &&  
	sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/sites-enabled/000-default && 
	a2enmod rewrite && 
	rm -rf /var/www/index.html && 
	wget http://beta.bevomedia.com/install.php.txt -O /var/www/index.php && 
	chown -R www-data:www-data /var/www && 
	mysql -uroot -p$MYSQL_ROOT_PASSWORD -e "create database $MYSQL_DB; grant all on $MYSQL_DB.* to $MYSQL_USER@'localhost' identified by '$MYSQL_PASS'" && 
	/etc/init.d/apache2 restart && 
	echo "Bevo dependencies installed OK! Your database information: " && echo "  MySQL Database: $MYSQL_DB" && echo "  MySQL Username: $MYSQL_USER" && echo "  MySQL Password: $MYSQL_PASS" && 
	echo "Just click here to install -- " && echo "  http://$IP/?Step=2&dbuser=$MYSQL_USER&dbpass=$MYSQL_PASS&dbname=$MYSQL_DB"
	<br /></blockquote>
		</li>
		<li>If everything worked correctly, the last thing on the window in front of you should read "Just click here to install." Click this link, or copy-paste it into your browser, and you're almost done!
			<br />
			 <a rel="shadowbox" href="/Themes/BevoMedia/img/Clickheretoinstall.JPG" class="rack-shadowbox">
				<center><img alt="RackSpace API Access Screenshot" src="/Themes/BevoMedia/img/rack_api_thumb.jpg"></center>
			</a>
		</li>
		<li>At the install page, choose your desired username and password for logging into your selfhosted account. Optionally, enter your BevoMedia.com credentials to use our Bevo Live services.
			<br />
			 <a rel="shadowbox" href="/Themes/BevoMedia/img/FInal Step.JPG" class="rack-shadowbox">
				<center><img alt="RackSpace API Access Screenshot" src="/Themes/BevoMedia/img/rack_api_thumb.jpg"></center>
			</a>
		</li>
		<li>Click done.</li>
	</ol>
</div><!--close pagecontent-->