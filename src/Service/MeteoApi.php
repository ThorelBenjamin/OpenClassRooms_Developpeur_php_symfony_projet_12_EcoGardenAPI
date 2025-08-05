<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MeteoApi
{
    public function __construct(
        private string $token,
        private HttpClientInterface $httpClient,
        private CacheInterface $cache)
    {
    }

    public function getMeteoCity(string $city)
    {
        $idCache = "getMeteo-" . $city;

        return $this->cache->get($idCache, function (ItemInterface $item) use ($city) {
            // Durée de vie du cache (en secondes)
            $item->expiresAfter(3600);

            $url = 'https://api.openweathermap.org/data/2.5/weather?q=' . $city . '&appid=' . $this->token . '&units=metric&lang=fr';

            try {
                $response = $this->httpClient->request('GET', $url);
                return $response->toArray();
            } catch (ClientExceptionInterface $e) {
                throw new \RuntimeException("Ville introuvable : " . $city);
            } catch (TransportExceptionInterface $e) {
                throw new \RuntimeException("Erreur réseau lors de la récupération météo.");
            } catch (\Throwable $e) {
                throw new \RuntimeException("Erreur inconnue : " . $e->getMessage());
            }
        });
    }
}
