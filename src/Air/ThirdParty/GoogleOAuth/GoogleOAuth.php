<?php

declare(strict_types=1);

namespace Air\ThirdParty\GoogleOAuth;

use Air\Http\Request;
use Air\ThirdParty\GoogleOAuth\Exception\InvalidCode;
use Air\ThirdParty\GoogleOAuth\Exception\UnableToGetUserByAccessToken;
use Throwable;

class GoogleOAuth
{
  /**
   * @var string
   */
  private string $clientId;

  /**
   * @var string
   */
  private string $clientSecret;

  /**
   * @var string
   */
  private string $redirectUrl;

  /**
   * @param string $clientId
   * @param string $clientSecret
   * @param string $redirectUrl
   */
  public function __construct(string $clientId, string $clientSecret, string $redirectUrl)
  {
    $this->clientId = $clientId;
    $this->clientSecret = $clientSecret;
    $this->redirectUrl = $redirectUrl;
  }

  /**
   * @return string
   */
  public function authUrl(): string
  {
    $params = [
      'response_type' => 'code',
      'client_id' => $this->clientId,
      'redirect_uri' => $this->redirectUrl,
      'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
      'access_type' => 'offline',
      'prompt' => 'consent'
    ];
    return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
  }

  /**
   * @param string $code
   * @return Profile
   * @throws InvalidCode
   * @throws UnableToGetUserByAccessToken
   */
  public function auth(string $code): Profile
  {
    $response = Request::run('https://accounts.google.com/o/oauth2/token', [
      'method' => Request::POST,
      'body' => [
        'code' => $code,
        'client_id' => $this->clientId,
        'client_secret' => $this->clientSecret,
        'redirect_uri' => $this->redirectUrl,
        'grant_type' => 'authorization_code'
      ]
    ]);

    if (!$response->isOk()) {
      throw new InvalidCode($code);
    }

    $accessToken = $response->body['access_token'];

    $response = Request::run('https://www.googleapis.com/oauth2/v3/userinfo', [
      'method' => Request::POST,
      'bearer' => $accessToken
    ]);

    if (!$response->isOk()) {
      throw new UnableToGetUserByAccessToken($accessToken);
    }

    try {
      return new Profile([
        'email' => $response->body['email'],
        'firstName' => $response->body['given_name'],
        'secondName' => $response->body['family_name'],
        'image' => $response->body['picture']
      ]);

    } catch (Throwable) {
      throw new UnableToGetUserByAccessToken($accessToken);
    }
  }
}