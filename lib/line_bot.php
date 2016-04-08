<?php

require_once '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class LINE_BOT
{

	const END_POINT_URL = 'https://trialbot-api.line.me/v1/events';
	const TO_CHANNEL = 1383378250;

	private $eventType;
	private $httpClient;
	private $requestContent;
	private $requestText;
	private $requestFrom;

	public function __construct(Request $request)
	{
		$this->httpClient = new Client();
		$json = json_decode($request->getContent());
		$this->requestContent = $json->result{0}->content;
		$this->requestText = $this->requestContent->text;
		$this->requestFrom = $this->requestContent->from;
		error_log(print_r($this->requestContent, true));
		error_log(print_r($this->requestText, true));
		error_log(print_r($this->requestFrom, true));
	}

	/**
	 * 送られたテキストのマッチング
	 * @param string $pattern
	 * @return boolean
	 */
	public function matchText($pattern)
	{
		return preg_match($pattern, $this->requestText);
	}

	/**
	 * リクエストコンテンツ取得
	 * @return stdClass
	 */
	public function getContent()
	{
		return $this->requestContent;
	}

	/**
	 * リクエストコンテンツセット
	 * @param stdClass $content
	 * @return \LINE_BOT
	 */
	public function setContent($content)
	{
		$this->requestContent = $content;
		return $this;
	}

	/**
	 * イベントタイプセット
	 * @param string $eventType
	 * @return \LINE_BOT
	 */
	public function setEventType($eventType)
	{
		$this->eventType = $eventType;
		return $this;
	}

	/**
	 * メッセージ送信
	 * @return \LINE_BOT
	 */
	public function sendingMessages()
	{
		$this->httpClient->request('post', self::END_POINT_URL, $this->createRequestOptions());
		return $this;
	}

	/**
	 * リクエストのbodyの作成
	 * @return string
	 */
	private function createRequestBody()
	{
		return json_encode([
			'to' => [$this->requestFrom],
			'toChannel' => self::TO_CHANNEL,
			'eventType' => $this->eventType,
			'content' => $this->requestContent,
		]);
	}

	/**
	 * リクエストのオプション作成
	 * @return array
	 */
	private function createRequestOptions()
	{
		return [
			'body' => $this->createRequestBody(),
			'headers' => [
				'Content-Type' => 'application/json; charset=UTF-8',
				'X-Line-ChannelID' => getenv('LINE_CHANNEL_ID'),
				'X-Line-ChannelSecret' => getenv('LINE_CHANNEL_SECRET'),
				'X-Line-Trusted-User-With-ACL' => getenv('LINE_CHANNEL_MID'),
			],
			'proxy' => [
				'https' => getenv('FIXIE_URL'),
			]
		];
	}

}

class ContentType
{

	const TEXT = 1;
	const IMAGE = 2;
	const VIDEO = 3;
	const AUDIO = 4;
	const LOCATION = 7;
	const STICKER = 8;
	const CONTACT = 10;

}

class EventType
{

	const SENDING_MESSAGES = '138311608800106203';
	const SENDING_MULTIPLE_MESSAGES = '140177271400161403';

}
