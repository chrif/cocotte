<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController {

	public function index() {
		return new Response(
			'<html><body>It works</body></html>'
		);
	}
}