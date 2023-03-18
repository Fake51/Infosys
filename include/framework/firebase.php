<?php
class Firebase {

  protected $authConfig;
  protected $secret;
  protected $response;

  static private function base64UrlEncode($text) {
    return str_replace(
        ['+', '/', '='],
        ['-', '_', ''],
        base64_encode($text)
    );
  }

  function __construct(Config $config) {
    $credentials_file = $config->get('firebase.credentials');
    $file_content = file_get_contents(INCLUDE_PATH."/$credentials_file");

    if ($file_content === false) {
      throw new FrameworkException('Could not find firebase credentials file');
    }

    $this->authConfig = json_decode($file_content);
    $this->secret = openssl_get_privatekey($this->authConfig->private_key);
  }

  function refreshToken() {
    // Create the token header
    $header = json_encode([
      'typ' => 'JWT',
      'alg' => 'RS256'
    ]);

    // Get seconds since 1 January 1970
    $time = time();

    $payload = json_encode([
        "iss" => $this->authConfig->client_email,
        "scope" => "https://www.googleapis.com/auth/firebase.messaging",
        "aud" => "https://oauth2.googleapis.com/token",
        "exp" => $time + 3600,
        "iat" => $time
    ]);

    // Encode Header
    $base64UrlHeader = self::base64UrlEncode($header);

    // Encode Payload
    $base64UrlPayload = self::base64UrlEncode($payload);

    // Create Signature Hash
    $result = openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $this->secret, OPENSSL_ALGO_SHA256);

    // Encode Signature to Base64Url String
    $base64UrlSignature = self::base64UrlEncode($signature);

    // Create JWT
    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    //-----Request token------
    $options = [
      'http' => [
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => 'grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer&assertion='.$jwt,
      ]
    ];

    $context  = stream_context_create($options);
    $responseText = file_get_contents("https://oauth2.googleapis.com/token", false, $context);
    $response = json_decode($responseText);

    $this->access = [
      'token' => $response->access_token,
      'type' => $response->token_type,
      'expires' => $time + $response->expires_in,
    ];
  }

  function sendMessage($message, $id) {
    // Check that we have a valid access token
    if (!isset($this->access['expires']) || $this->access['expires'] < time() + 10) {
      $this->refreshToken();
    }

    $body = is_array($message) ? $message['body'] : $message;
    $title = is_array($message) ? $message['title'] : "Fastaval";

    $message = [
      "notification" => [
        "body" => $body,
        "title" => $title
      ],
    ];

    if (is_string($id)) {
      $message['token'] = $id;
    } else {
      $message['topic'] = "debug";
    }

    $content = [
      "message" => $message,
    ];

    $options = [
      'http' => [
        'method' => 'POST',
        'header' => [
          "Content-Type: application/json",
          "Authorization: ".$this->access['type']." ".$this->access['token'],
        ],
        'content' => json_encode($content),
      ],
    ];

    $context  = stream_context_create($options);
    $responseText = file_get_contents("https://fcm.googleapis.com/v1/projects/fastaval-it/messages:send", false, $context);
    $this->response = json_decode($responseText);

    return isset($this->response->name);
  }

  function getResponse() {
    return $this->response;
  }
}