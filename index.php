<?php
	session_start();

	$servername = "localhost";
	$username = "db_admin";
	$password = "qwertorros7089";

	$conn = new mysqli($servername, $username, $password);

	if($conn -> connect_error) {
		die("you failed: " . $conn -> connect_error);
	}
	echo "database connected<br/>";
?>

<script>
	function myFunction() {
  			
  	var copyText = document.querySelector("#toCopy");

  			
  	copyText.select();
  	copyText.setSelectionRange(0, 99999); 

  	document.execCommand("copy"); 
	}
</script>

<?php
	echo 'previous name:<input id="toCopy" value='.$_SESSION['localName']."'></span>";
	echo "<button onclick='myFunction()'>Copy name</button>";
	$filename = "log.txt";
	$filename = fopen($filename, "a+") or die(); #opening log.txt to show it on page laterly
	$local_name = NULL;
	$separator = " > ";
	echo '
	<form action="index.php" method="post"> 
		<label for="name">Name:</label>
		<input type="text" name="name" id="name"/><br/>
		<label for="msg_hold">Message:</label>
		<input type="text" name="msg_hold" id="msg_hold"/><br/>
		<input type="submit" name="enter" id="enter">
	</form>
	'; #form to enter your name, message content and submit

	
?>
<!-- chat application here-->
<!DOCTYPE html>
<html>
	<head>
		<title>OpenChat</title>
	</head>
	<body>
		
		<?php
			if(isset($_POST['enter'])) { #if user pressed "enter" button
				if($_POST['name'] != "" and $_POST['name'] != "admin") { #if name isn't empty
					$local_name = stripslashes(htmlspecialchars($_POST['name'])); #getting name from <input> tag
					$local_msg = stripslashes(htmlspecialchars($_POST['msg_hold'])); #getting message content
					$_SESSION['localName'] = $local_name;

					###MESSAGE PARSER###

					if(strpos($local_msg, "/i") !== false) {
						$local_msg = str_replace("/i", "<i>", $local_msg);
						$local_msg = $local_msg."</i>";
					};

					if(strpos($local_msg, "/b") !== false) {
						$local_msg = str_replace("/b", "<b>", $local_msg);
						$local_msg = $local_msg."</b>";
					};

					if(strpos($local_msg, "/code") !== false) {
						$local_msg = str_replace("/code", "<div style='background-color: grey; color: white; border: 2px solid black'>", $local_msg);
						$local_msg = $local_msg."</div>";
					};

					if(strpos($local_msg, "/scode") !== false) {
						if (strpos($local_msg, "/code") !== false) {
							$local_msg = "[REMOVED DUE INNAPROPRIATE CONTENT OR CHAT-BREAKING SCRIPT]";
						}
						else {
							$local_msg = str_replace("/scode", "<span style='background-color: grey; color: white; border: 2px solid black'>", $local_msg);
							$local_msg = $local_msg."</span>";
						}
					
					};

					if(strpos($local_msg, "https://") !== false or strpos($local_msg, "http://") !== false or strpos($local_msg, "/am") !== false) {
						$local_msg = "[MESSAGE WAS AUTO MODERATED]";
					};

					if(strpos($local_msg, "/h") !== false) {
						$local_msg = str_replace("/h", "♥", $local_msg);
					};

					if(strpos($local_msg, "!NAME") !== false) {
						$local_msg = str_replace("!NAME", $local_name, $local_msg);
					};

					if(strpos($local_msg, "!DATE") !== false) {
						$local_msg = str_replace("!DATE", date("D, d M Y H:i:s"), $local_msg);
					};

					if(strpos($local_msg, "/new") !== false) {
						if(substr_count("/new", $local_msg) > 5) {
							$local_msg = "[MESSAGE WAS AUTO MODERATED]";
						};
					};

					if(strpos($local_msg, "/new") !== false) {
						$local_msg = str_replace("/new","<br/>", $local_msg);
					};

					if(strpos($local_msg, "/chto") !== false) {
						$local_msg = str_replace("/chto","", $local_msg);
						if(strlen($local_msg) > 3) {
							echo "<script type='text/javascript'>alert('separator changed.');</script>";
							$_SESSION['separator'] = $local_msg;
						}
						else {
							echo "<script type='text/javascript'>alert('separator is too long.');</script>";
						}
					};
					

					$local_msg = $local_name.$_SESSION['separator'].$local_msg."<br/>"; #transforming message into NAME > MESSAGE format
					fwrite($filename, $local_msg); #writing that message to "log.txt" 
					header("Refresh:0");

				}
				else {
					echo "<span style='color: red;'>Invalid Name, yo!</span><br/>"; #Showing if name format is invalid
				}
			}
			$messages = fread($filename, filesize("log.txt"));
			echo $messages;
			fclose($filename);
		?>
	</body>
</html>