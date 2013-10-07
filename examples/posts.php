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
$status = array(
		'type' => 'https://tent.io/types/status/v0#',
		'content' => array(
			'text' => $_POST['text'],
		),
		'permissions' => array('public' => false),
	);
	$app = new App;
if(isset($_POST['text'])) {
	$post = $app->send_post($credentials, $status, $_SESSION['endpoints']['new_post']);
	unset($_POST['text']); 
	if (!isset($post['error'])) { ?>
		<p>Createad Status: <?php echo $post['post']['content']['text']; ?></p> 
	<?php }
	else { ?>
		<p>Error: <?php echo $post['error']; ?></p>
	<?php }
} ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<label>Status: <textarea name="text"></textarea></label>
		<p><input type="submit" value="Create status" /></p>
	</form>
	<?php
		$statuses = $app->get_posts($credentials, 'https://tent.io/types/status/v0#', $_SESSION['endpoints']['posts_feed']);
	?>
	<h2>Your statuses:</h2>
	<div id="status">
		<?php
			foreach ($statuses['posts'] as $status) { ?>
				<p><?php echo $status['content']['text']; ?> - <?php echo $status['entity']; ?></p>
			<?php }
		?>
	</div>