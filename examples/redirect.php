<?php
// Librejo uses sessions to access the credentials from app registration
session_name('Librejo');
session_start();
require_once __DIR__.'/../vendor/autoload.php';

use Librejo\App\App;
use Librejo\Client;
use Librejo\Entity\Entity;

if (isset($_GET['error'])) {
	echo "<p>An error occured, please try again</p>";
	echo "<p>Error: ".$_GET['error']."</p>";
}
else {
	echo "<p>Code: ".$_GET['code']."</p>";
	$app = new App;
	$oauth = $app->oauth($_GET['code'], $_SESSION['client_id'], $_SESSION['hawk_id'], $_SESSION['hawk_key'], $_SESSION['entity'], $_SESSION['endpoints']['oauth_token']);
	$_SESSION['hawk_key'] = $oauth['hawk_key'];
	$_SESSION['access_token'] = $oauth['access_token'];
	$_SESSION['hawk_id'] = $oauth['access_token'];
	echo "<p>App ID: ".$_SESSION['client_id']."</p>";
	echo "<p>Hawk ID: ".$_SESSION['hawk_id']."</p>";
	echo "<p>Hawk Key: ".$_SESSION['hawk_key']."</p>";
	echo "<p>Access Token: ".$_SESSION['access_token']."</p>"; ?>
	<p><a href="posts.php">Read and create statuses</a></p>
<?php }
?>