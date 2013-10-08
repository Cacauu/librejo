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
$app = new App($_SESSION['entity'], $credentials);
$delete = $app->delete_post($_GET['id']);
var_export($delete);
if (!isset($delete['error'])) { ?>
	<p>Successfully deleted!</p>
<?php }
?>
<a href="posts.php">Go back</a>