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

$app = new App($_SESSION['entity']);
if(isset($_POST['text'])) {
	$status = array(
		'type' => 'https://tent.io/types/status/v0#',
		'content' => array(
			'text' => $_POST['text'],
		),
		'permissions' => array('public' => false),
	);
	$post = $app->send_post($credentials, $status);
	unset($_POST['text']); 
	if (!isset($post['error'])) { ?>
		<p>Createad Status: <?php echo $post['post']['content']['text']; ?> | <a href="delete.php?id=<?php echo $post['post']['id']; ?>">Delete post</a></p> 
	<?php }
	else { ?>
		<p>Error: <?php echo $post['error']; ?></p>
	<?php }
} ?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<label>Status: <textarea name="text"></textarea></label>
		<p><input type="submit" value="Create status" /></p>
	</form>

	<h2>Profile:</h2>
	<?php
		$profile = $app->get_profile($_SESSION['entity']); ?>
		<img src="<?php echo $profile['avatar']; ?>" style="border-radius:10px; float:right; width:200px; height:200px;">
		<p>Name: <?php echo $profile['name']; ?></p>
		<p>Location: <?php echo $profile['location']; ?></p>
		<p>Bio: <?php echo $profile['bio']; ?></p>
		<p>Website: <?php echo $profile['website']; ?></p>
		
	<h2>Your statuses:</h2>
	<div id="status">
		<?php
			$statuses = $app->get_posts($credentials, 'https://tent.io/types/status/v0#');
			foreach ($statuses['posts'] as $status) { ?>
				<p><?php echo $status['content']['text']; ?> - <?php echo $status['entity']; ?></p>
			<?php } ?>

	<h2>Single Post:</h2>
		<?php
		$status = $app->get_single_post($credentials, '9KV3fqegxNP65oFgrE3U4A', 'https://cacauu.cupcake.is'); 
		if (!isset($status['error'])) { ?>
			<p><?php echo $status['post']['content']['text']; ?></p>
		<?php }
		?>
		
	</div>