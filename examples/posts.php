<?php
// Librejo uses sessions to access the credentials from app registration
session_name('Librejo');
session_start();
require_once __DIR__.'/../vendor/autoload.php';

use Librejo\App\App;
use Librejo\Client;
use Librejo\Entity\Entity;

$credentials = array(
	'entity' => $_SESSION['entity'], 
	'client_id' => $_SESSION['client_id'],
	'hawk_id' => $_SESSION['hawk_id'],
	'hawk_key' => $_SESSION['hawk_key']
);
if(isset($_POST['text'])) {
	$credentials = array('client_id' => $_SESSION['client_id'], 'access_token' => $_SESSION['access_token'], 'hawk_key' => $_SESSION['hawk_key'], 'hawk_id' => $_SESSION['hawk_id'], 'entity' => $_SESSION['entity']);
	$status = array(
		'type' => 'https://tent.io/types/status/v0#',
		'content' => array(
			'text' => $_POST['text'],
		),
		'permissions' => array('public' => false),
	);
	$app = new App;
	$post = $app->send_post($credentials, $status, $_SESSION['endpoints']['new_post']);
	unset($_POST['text']);
	var_export($post);
}
else { ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<label>Status: <textarea name="text"></textarea></label>
		<p><input type="submit" value="Create status" /></p>
	</form>
	<h2>Your statuses:</h2>
	<div id="status">
		<p>You statuses will show up here later</p>
	</div>
<?php }
?>