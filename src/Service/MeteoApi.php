<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MeteoApi
{
    public function __construct(
        private string $token,
        private HttpClientInterface $httpClient)
    {
    }

    // https://api.openweathermap.org/data/2.5/weather?q=Paris&appid=0ebec701c42956b86b6894566a702a97&units=metric&lang=fr
    public function getMeteoCity(string $city)
    {
        $url = 'https://api.openweathermap.org/data/2.5/weather?q=' . $city . '&appid=' . $this->token . '&units=metric&lang=fr';
        $response = $this->httpClient->request('GET', $url);
        return $response->toArray();
    }
}
