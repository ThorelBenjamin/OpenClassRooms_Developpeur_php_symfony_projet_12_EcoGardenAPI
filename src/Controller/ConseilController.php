<?php

namespace App\Controller;

use App\Entity\Conseil;
use App\Repository\ConseilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class ConseilController extends AbstractController
{
    #[Route('/api/conseils', name: 'conseils', methods: ['GET'])]
    public function getAllConseils(ConseilRepository $conseilRepository, SerializerInterface $serializer): JsonResponse
    {
        $conseilList = $conseilRepository->findAll();

        $jsonConseilList = $serializer->serialize($conseilList, 'json');
        return new JsonResponse($jsonConseilList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/conseils/{id}', name: 'conseil', methods: ['GET'])]
    public function getDetailConseil(Conseil $conseil, SerializerInterface $serializer): JsonResponse
    {
        $jsonConseilDetail = $serializer->serialize($conseil, 'json');
        return new JsonResponse($jsonConseilDetail, Response::HTTP_OK, [], true);
    }
}
