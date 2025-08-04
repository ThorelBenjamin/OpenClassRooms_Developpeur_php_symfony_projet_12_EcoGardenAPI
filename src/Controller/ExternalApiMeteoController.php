<?php

namespace App\Controller;

use App\Service\MeteoApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ExternalApiMeteoController extends AbstractController
{
    #[Route('/api/meteo', name: 'app_external_api_meteo')]
    public function getMeteo(MeteoApi $api, TagAwareCacheInterface $cache): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $city = $user->getVille() ?? 'Paris';
        $idCache = "getMeteo-" . $city;

        $meteo = $cache->get($idCache, function (ItemInterface $item) use ($idCache, $city, $api) {
            $item->tag("meteoCache");
            $item->expiresAfter(3600);
            return $api->getMeteoCity($city);
        });

        return new JsonResponse($meteo, Response::HTTP_OK);
    }

    #[Route('/api/meteo/{city}', name: 'app_external_api_meteo_city')]
    public function getMeteoCity(MeteoApi $api, string $city, TagAwareCacheInterface $cache): JsonResponse
    {
        $idCache = "getMeteoCity-" . $city;

        $meteo = $cache->get($idCache, function (ItemInterface $item) use ($idCache, $city, $api) {

            $item->tag("meteoCityCache");
            $item->expiresAfter(3600);
            return $api->getMeteoCity($city);
        });

        return new JsonResponse($meteo, Response::HTTP_OK);
    }
}
