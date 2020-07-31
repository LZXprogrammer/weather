<?php

namespace Lzxprogrammer\Weather;

use GuzzleHttp\Client;
use Lzxprogrammer\Weather\Exceptions\HttpException;
use Lzxprogrammer\Weather\Exceptions\InvalidArgumentException;

class Weather
{
    protected $key;
    protected $guzzleOptions = [];

    /**
     * 初始化天气接口 key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * 实例化 HTTP 类
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * 设置http类初始化参数
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * 获取天气结果
     * 
     * @param String $city 城市编码
     * @param String $type 气象类型  base:返回实况天气；all:返回预报天气；
     * @param String $format 返回格式 json/xml，默认是 json
     * @param String $city 城市编码
     */
    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        if (!in_array(strtolower($format), ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }

        if (!in_array(strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): '.$type);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => strtolower($format),
            'extensions' => strtolower($type),
        ]);

        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            return 'json' === $format ? \json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}