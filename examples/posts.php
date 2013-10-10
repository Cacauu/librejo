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
if(isset($_POST['text'])) {
	$status = array(
		'type' => 'https://tent.io/types/status/v0#',
		'content' => array(
			'text' => $_POST['text'],
		),
		'permissions' => array('public' => true),
	);
	$post = $app->send_post($status);
	unset($_POST['text']); 
	if (!isset($post['error'])) { ?>
		<p>Createad Status: <?php echo $post['post']['content']['text']; ?> | <a href="delete.php?id=<?php echo $post['post']['id']; ?>">Delete post</a> <a href="posts.php?update=<?php echo $post['post']['id']; ?>">Update post</a></p> 
	<?php }
	else { ?>
		<p>Error: <?php echo $post['error']; ?></p>
	<?php }
} 
elseif (isset($_GET['update'])) {
		$status = $app->get_single_post($_GET['update'], $_SESSION['entity']); ?>
		<h2>Update Post:</h2>
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?send_update=<?php echo $status['post']['id']; ?>&version=<?php echo $status['post']['version']['id']; ?>">
			<textarea name="update"><?php echo $status['post']['content']['text']; ?></textarea>
			<input type="submit" value="Update post">
		</form>
<?php }
elseif (isset($_GET['send_update'])) {
	$new_post = array(
		'type' => 'https://tent.io/types/status/v0#',
		'content' => array(
			'text' => $_POST['update'],
		),
		'version' => array(
			'parents' => array(
				array('version' => $_GET['version']),
			),
		),
		'permissions' => array('public' => true),
	);
	$update = $app->update_post($_GET['send_update'], $_SESSION['entity'], $new_post);
	if (!isset($update['error'])) {
		echo "<p>Updated ".$_GET['send_update']."!</p>";
	}
}
?>

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
			$statuses = $app->get_posts('https://tent.io/types/status/v0#');
			foreach ($statuses['posts'] as $status) { ?>
				<p><?php echo $status['content']['text']; ?> - <?php echo $status['entity']; ?></p>
			<?php } ?>

	<h2>Single Post:</h2>
		<?php
		$status = $app->get_single_post('9KV3fqegxNP65oFgrE3U4A', 'https://cacauu.cupcake.is'); 
		if (!isset($status['error'])) { ?>
			<p><?php echo $status['post']['content']['text']; ?></p>
		<?php }
		?>
		
	</div>