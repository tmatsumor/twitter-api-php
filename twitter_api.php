<?php
require_once('http-requests-php/http_requests.php');
namespace tmatsumor\twitter_api_php;

class TwitterAPI extends \tmatsumor\http_requests_php\HttpRequests
{
    const TWITTER_URL = 'https://api.twitter.com/2/';
    const TWITTER_TOKEN = __DIR__.'/twitter_token.json';
    private $token;

    public function __construct($userpwd) {
        $this->token = json_decode(file_get_contents(self::TWITTER_TOKEN), true);
        if(time() % 2 === 0){                  // refresh current token if timestamp is even number
            $p = 'grant_type=refresh_token'
               . '&refresh_token='.$this->token['refresh_token'];
            $this->token = json_decode($this->post(self::TWITTER_URL.'oauth2/token',
                $p,  ['Content-type: application/x-www-form-urlencoded'], $userpwd)[0], true);
            file_put_contents(self::TWITTER_TOKEN, stripslashes(trim(json_encode($this->token), '"')));
        }
    }

    public function tweet($text) {
        return $this->post(self::TWITTER_URL.'tweets',
            json_encode( array( 'text'=> $text  )),
            ['Authorization: Bearer '.$this->token['access_token'],
             'Content-type: application/json']);
    }
}
