<?php

namespace Librejo\Client;

class Client {

	protected $meta;
	protected $new_post_endpoint;
	protected $post_feed_endpoint;
	protected $oauth_endpoint;
	protected $profile;

	//Function returning the meta post of an entity
	public function discover($entityUri) {
		$header = get_headers($entityUri);
		if (!$header[0] == 'HTTP/1.1 200 OK') {
			return 'The request failed, please check the entered entity';
		}
		else {
			$link = $header[2];
			$link = str_replace('Link: <', '', $link);
			$link = str_replace('>; rel="https://tent.io/rels/meta-post"', "", $link);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $entityUri.$link);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$meta = json_decode(curl_exec($ch));
			curl_close($ch);
			$this->meta = $meta;
			$this->new_post_endpoint = 'New Post';
			$this->post_feed_endpoint = 'Post Feed';
			$this->profile = $meta->post->content->profile;
			return $meta;
		}
	}

	public function meta() {
		return $this->meta;
	}

	public function profile() {
		return $this->profile;
	}

	public function post_feed_endpoint() {
		return $this->post_feed_endpoint;
	}

	public function new_post_endpoint() {
		return $this->new_post_endpoint;
	}
}

?>