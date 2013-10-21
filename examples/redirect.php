<?php
// Librejo uses sessions to access the credentials from app registration
session_name('Librejo');
session_start();
require_once __DIR__.'/../vendor/autoload.php';

use Librejo\App\App;
use Librejo\Client;

if (isset($_GET['error']) OR $_GET['state'] != $_SESSION['state']) {
	echo "<p>An error occured, please try again</p>";
	echo "<p>Error: ".$_GET['error']."</p>";
}
elseif ($_SESSION['state'] == $_GET['state']){
	$credentials = array(
		'entity' => $_SESSION['entity'], 
		'client_id' => $_SESSION['client_id'],
		'hawk_id' => $_SESSION['hawk_id'],
		'hawk_key' => $_SESSION['hawk_key']
	);
	echo "<p>State: Correct</p>";
	unset($_SESSION['state']);
	echo "<p>Code: ".$_GET['code']."</p>";
	$app = new App($_SESSION['entity'], $credentials);
	$oauth = $app->oauth($_GET['code']);
	$_SESSION['hawk_key'] = $oauth['hawk_key'];
	$_SESSION['access_token'] = $oauth['access_token'];
	$_SESSION['hawk_id'] = $oauth['access_token'];
	echo "<p>App ID: ".$_SESSION['client_id']."</p>";
	echo "<p>Hawk ID: ".$_SESSION['hawk_id']."</p>";
	echo "<p>Hawk Key: ".$_SESSION['hawk_key']."</p>"; ?>
	<p><a href="posts.php">Read and create statuses</a></p>
<?php }
else {
	echo "<p>Something is wrong here...</p>";
}
?>