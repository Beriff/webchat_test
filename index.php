<?php
	session_start();
	if(!isset($_SESSION['localName'])) {
		$_SESSION['localName'] = NULL;
	}
	if(!isset($_SESSION['separator'])) {
		$_SESSION['separator'] = " > ";
	}
	$local_name = $_SESSION['localName'];
	$filename = "names.txt";
	$filename = fopen($filename, "a+") or die();

	$names = fread($filename, filesize("names.txt"));
	$namesArray = explode("!", $names);
	fclose($filename);

	$filename = "passwords.txt";
	$filename = fopen($filename, "a+") or die();

	$passwords = fread($filename, filesize("passwords.txt"));
	$passArray = explode("!", $passwords);
	fclose($filename);

	$accounts = array_combine($namesArray, $passArray);

	$filename = "statuses.txt";
	$filename = fopen($filename, "a+") or die();

	$statuses = fread($filename, filesize("statuses.txt"));
	$statArray = explode("!", $statuses);
	$accountStatuses = array_combine($namesArray, $statArray);

	$filename = "blacklist.txt";
	$filename = fopen($filename, "a+") or die();

	$deny = fread($filename, filesize("blacklist.txt"));
	$deny = explode("!", $deny);

	if (in_array ($_SERVER['REMOTE_ADDR'], $deny)) {
		header("location: /blocked");
		exit();
	}

	echo 'Register or Login:
	<form action="index.php" method="post"> 
		<label for="name">Name:</label>
		<input type="text" name="name" id="name"/><br/>
		<label for="pass">Password:</label>
		<input type="text" name="pass" id="pass"/><br/>
		<input class="button" type="submit" name="sub" id="sub">
		<hr>
	</form>';
	if(isset($_POST['sub'])) {
		$local_name = $_POST['name'];
		$local_pass = $_POST['pass'];
		if(strpos($local_pass, "!") !== false) {
			echo "<span style='color: red;'>Invalid Password Type! No '!' allowed!</span><br/>";
			$local_pass = "";
		}
		if(array_key_exists($local_name, $accounts)) {
			if($accounts[$local_name] == $local_pass) {
				echo "<span style='color: green;'>Successfully logged in.</span><br/>";
				$_SESSION['localName'] = $local_name;
				if(array_key_exists($local_name, $accountStatuses)) {
					if ($accountStatuses[$local_name] == "admin") {
						$_SESSION['admin_NAN'] = $_SESSION['localName'];
						$_SESSION['localName'] = "<span style='color: purple;'>".$_SESSION['localName']."</span>";
						$local_name = $_SESSION['localName'];
					}
				}
				
			}
			else {
				echo "<span style='color: red;'>Invalid Password!</span><br/>";
			}
		}
		else {
			echo "<span style='color: green;'>No accounts has been found. Successfully made a new one!</span><br/>";
			$filename = "names.txt";
			$filename = fopen($filename, "a+") or die();
			$local_name = $local_name."!";
			fwrite($filename, $local_name);
			fclose($filename);

			$filename = "passwords.txt";
			$filename = fopen($filename, "a+") or die();
			$local_pass = $local_pass."!";
			fwrite($filename, $local_pass);
			fclose($filename);

			$filename = "statuses.txt";
			$filename = fopen($filename, "a+") or die();
			fwrite($filename, "usr!");
			fclose($filename);
		}

	}
?>
<?php
	echo 'logged as '.$_SESSION['localName'];
	$filename = "log.txt";
	$filename = fopen($filename, "a+") or die(); #opening log.txt to show it on page laterly
	$local_name = NULL;
	$separator = " > ";
	echo '<br/>(write /logout in message to log out)';
	echo '
	<form action="index.php" method="post"> 
		<label for="msg_hold">Message:</label>
		<input type="text" name="msg_hold" id="msg_hold"/><br/>
		<input class="button" type="submit" name="enter" id="enter">
	</form>
	'; #form to enter your name, message content and submit

	
?>
<!-- chat application here-->
<!DOCTYPE html>
<html>
	<head>
		<title>OpenChat</title>
		<script>
			var toggleFlag = true
			function blackTheme() {
				document.body.style.backgroundColor = "black";
				document.body.style.color = "white";
				document.getElementsByClass("button").style.backgroundColor = "black"
			}
			function whiteTheme() {
				document.body.style.backgroundColor = "white";
				document.body.style.color = "black";
				document.getElementsByClass("button").style.backgroundColor = "black"
			}
			function settToggle() {
				if (toggleFlag) {
					toggleFlag = false
					document.getElementById("settings").style.display = "inline-block";
				}
				else {
					toggleFlag = true
					document.getElementById("settings").style.display = "none";
				}
			}
			function chatErase() {
				chat = document.getElementById("updateChat");
				chat.remove();
			}
		</script>
		<style>
			.spoiler {
				background-color: black;
				color: black;
			}
			.spoiler:hover {
				background-color: grey;
			}

			#settings {
				display: none;
			}
		</style>
	</head>
	<body>
		
		<?php
			$ACSI = NULL;

			if(isset($_POST['enter'])) { #if user pressed "enter" button
				if(isset($_SESSION['localName'])) { 
					$local_msg = stripslashes(htmlspecialchars($_POST['msg_hold'])); #getting message content
					$local_name = $_SESSION['localName'];

					###MESSAGE PARSER###

					if(strpos($local_msg, "/i") !== false) {
						$local_msg = str_replace("/i", "<i> ", $local_msg);
						$ASCI = substr_count($local_msg, "<i>");
						for($i = 0; $i < $ASCI; $i++) {
							$local_msg = $local_msg."</i>";
						}
					};

					if(strpos($local_msg, "/b") !== false) {
						$local_msg = str_replace("/b", "<b> ", $local_msg);
						$ASCI = substr_count($local_msg, "<b>");
						for($i = 0; $i < $ASCI; $i++) {
							$local_msg = $local_msg."</b>";
						}
					};

					if(strpos($local_msg, "/code") !== false) {
						$local_msg = str_replace("/code", "<div style='background-color: grey; color: white; border: 2px solid black'> ", $local_msg);
						$ASCI = substr_count($local_msg, "<div style='background-color: grey; color: white; border: 2px solid black'>");
						for($i = 0; $i < $ASCI; $i++) {
							$local_msg = $local_msg."</>";
						}
					};

					if(strpos($local_msg, "/scode") !== false) {
						if (strpos($local_msg, "/code") !== false) {
							$local_msg = "[REMOVED DUE INNAPROPRIATE CONTENT OR CHAT-BREAKING SCRIPT]";
						}
						else {
							$local_msg = str_replace("/scode", "<span style='background-color: grey; color: white; border: 2px solid black'> ", $local_msg);
							$ASCI = substr_count($local_msg, "<span style='background-color: grey; color: white; border: 2px solid black'>");
							for($i = 0; $i < $ASCI; $i++) {
								$local_msg = $local_msg."</span>";
							}
						}
					};

					if(strpos($local_msg, "https://") !== false or strpos($local_msg, "http://") !== false or strpos($local_msg, "/am") !== false) {
						$local_msg = "[MESSAGE WAS AUTO MODERATED]";
					};

					if(strpos($local_msg, "/heart") !== false) {
						$local_msg = str_replace("/heart", "♥", $local_msg);
					};

					if(strpos($local_msg, "!NAME") !== false) {
						$local_msg = str_replace("!NAME", $local_name, $local_msg);
					};

					if(strpos($local_msg, "/lenny") !== false) {
						$local_msg = str_replace("/lenny", "( ͡° ͜ʖ ͡°)", $local_msg);
					};

					if(strpos($local_msg, "/wink") !== false) {
						$local_msg = str_replace("/wink", "( ͡~ ͜ʖ ͡°)", $local_msg);
					};

					if(strpos($local_msg, "/hooo") !== false) {
						$local_msg = str_replace("/hooo", "( ͡o ͜ʖ ͡o)", $local_msg);
					};

					if(strpos($local_msg, "/shrug") !== false) {
						$local_msg = str_replace("/shrug", "¯\_( ͡° ͜ʖ ͡°)_/¯", $local_msg);
					};

					if(strpos($local_msg, "!MY_IP") !== false) {
						$local_msg = str_replace("!MY_IP", $_SERVER['REMOTE_ADDR'], $local_msg);
					};

					if(strpos($local_msg, "!DATE") !== false) {
						$local_msg = str_replace("!DATE", date("D, d M Y H:i:s"), $local_msg);
					};

					if(strpos($local_msg, "!STATUS") !== false) {
						$local_msg = str_replace("!STATUS", $accountStatuses[$local_name], $local_msg);
					};

					if(strpos($local_msg, "/new") !== false) {
						if(substr_count("/new", $local_msg) > 5) {
							$local_msg = "[MESSAGE WAS AUTO MODERATED]";
						};
					};

					if(strpos($local_msg, "/new") !== false) {
						$local_msg = str_replace("/new","<br/> ", $local_msg);
					};

					if(strpos($local_msg, "/chto") !== false) {
						$local_msg = str_replace("/chto","", $local_msg);
						if(strlen($local_msg) > 3) {
							echo "<script type='text/javascript'>alert('separator changed.');</script> ";
							$_SESSION['separator'] = $local_msg;
						}
						else {
							echo "<script type='text/javascript'>alert('separator is too long.');</script> ";
						}
					};

					if($local_msg == "/logout") {
						$local_msg == "[USER LOGGED OUT]";
						session_destroy();
						header("refresh:0");
					};
					
					if(strpos($local_msg, "/sp") !== false) {
						$local_msg = str_replace("/sp", "<span class='spoiler'> ", $local_msg);
						$ASCI = substr_count($local_msg, "<span class='spoiler'>");
						for($i = 0; $i < $ASCI; $i++) {
							$local_msg = $local_msg."</span>";
							};
						};

						if(strpos($local_msg, "/g") !== false) {
							$local_msg = str_replace("/g", "<span style='color: yellow;'> ", $local_msg);
							$ASCI = substr_count($local_msg, "<span style='color: yellow;'>");
							for($i = 0; $i < $ASCI; $i++) {
								$local_msg = $local_msg."</span>";
							}
						};

						if(strpos($local_msg, "/clean") !== false) {
							if( $accountStatuses[$_SESSION['admin_NAN']] == "admin" ) {
								$filet = "log.txt";
								$filet = fopen("log.txt", "w") or die();
								fwrite($filet, "[WEBSERVER] > Chat Cleared by an admin.<br/>");
								fclose($filet);
								header("refresh:0"); 
							}
						}

					$local_msg = $local_name.$_SESSION['separator'].$local_msg."<br/>"; #transforming message into NAME > MESSAGE format
					fwrite($filename, $local_msg); #writing that message to "log.txt" 
					header("Refresh:0");

				}
				else {
					echo '<script type="text/javascript">alert("You are not logged in.")</script>';
				}
			}
			$filename = fopen('log.txt', "a+");
			$messages = fread($filename, filesize("log.txt"));
			echo '<div id="updateChat">'.$messages.'</div>';
			fclose($filename);
		?>

		<hr><hr><input type="button" value="toggle settings" onclick="settToggle()"></input>
		<hr>
		
		<div id="settings">
		<input type="button" value="black theme" onclick="blackTheme()"></input>
		<input type="button" value="white theme" onclick="whiteTheme()"></input>
		<?php
			echo '
			<br/>
			<form action="index.php" method="post"> 
				<label for="rate">Page Update (20ups):</label>
				<input class="button" type="submit" name="refsub" id="refsub">
			</form>
			(0 - single refresh)</div>
			';
			if(isset($_POST['refsub'])) {
				while(true) {
					$filename = fopen('log.txt', "a+");
					$messages = fread($filename, filesize("log.txt"));
					echo '<div id="updateChat">'.$messages.'</div>';
					fclose($filename); 
					echo '<script type="text/javascript">chatErase();</script>';
				}
			}
		?>
	</body>
</html>
