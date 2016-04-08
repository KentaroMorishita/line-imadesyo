<?php

require_once '../vendor/autoload.php';
require_once '../lib/line_bot.php';

use Symfony\Component\HttpFoundation\Request;

try {
	$app = new Silex\Application();

	$app->post('/callback', function (Request $request) use ($app) {

		$api = new LINE_BOT($request);

		if ($api->matchText('/(いつ|何時|when)/')) {
			$content = $api->getContent();
			$content->text = '今でしょ？？？？？';
			$api->setContent($content)
					->setEventType(EventType::SENDING_MESSAGES)
					->sendingMessages();
		}

		return true;
	});

	$app->run();
} catch (Exception $e) {
	error_log($e->getMessage());
}