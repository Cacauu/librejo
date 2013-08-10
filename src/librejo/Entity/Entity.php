<?php
	namespace Librejo\Entity;

	use Librejo\Client\GuzzleClient;

	function discover($entity) {
		$header = get_headers($entity);
		return $header;
	}
?>