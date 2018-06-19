<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 18-6-13
 * Time: 下午6:06
 */

namespace phpspider\core;


use JonnyW\PhantomJs\Client;


class phantomjsRequest extends requests
{

    static $instance;

    public static function init()
    {
        self::$instance = Client::getInstance();
        //self::$phantomjsPath = '/home/bruce/phantomjs-2.1.1/bin/phantomjs';
        self::$instance->getEngine()->setPath(self::$phantomjsPath);
    }

    public static function get($url, $fields = array(), $allow_redirects = true, $cert = null)
    {
        self::init();
        $request = self::$instance->getMessageFactory()->createRequest($url);
        if (!empty(self::$useragents)) {
            $key = rand(0, count(self::$useragents) - 1);
            $request->addHeader('User-Agent', self::$useragents[$key]);
        }

        if (!empty(self::$client_ips)) {
            $key = rand(0, count(self::$client_ips) - 1);
            $request->addHeader('CLIENT-IP', self::$client_ips[$key]);
        }

        if (!empty(self::$proxies)) {
            $key = rand(0, count(self::$proxies) - 1);
            $request->addHeader('Proxy', self::$proxies[$key]);
        }

        if (!empty($fields)) {
            $url = $url . (strpos($url, "?") === false ? "?" : "&") . http_build_query($fields);
        }

        $response = self::$instance->getMessageFactory()->createResponse();

        // Send the request
        self::$instance->send($request, $response);

        self::$headers = $response->getHeaders();
        self::$status_code = $response->getStatus();

        if ($response->getStatus() == 200) {
            self::$content = $response->getContent();
        }

        return self::$content;
    }

    protected static function _is_url($url)
    {
        //$pattern = '/^http(s)?:\\/\\/.+/';
        $pattern = "/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/";
        if (preg_match($pattern, $url)) {
            return true;
        }
        return false;
    }

}