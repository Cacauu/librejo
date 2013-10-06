<?php
namespace Librejo\App\Post;

use Librejo\Client\GuzzleClient;

class Post {

	protected $credentials;
	protected $post;
	protected $type;
	protected $endpoint;

	public function __construct($credentials, $post, $new_post_endpoint) {
		$this->credentials = $credentials;
		$this->post = $post;
		$this->type = $post['type'];
		$this->endpoint = $new_post_endpoint;
	}

	public function send() {
		$Guzzle = new GuzzleClient\Guzzle($this->credentials['entity']);
		
	}
}
?>