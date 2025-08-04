<?php

namespace App\Controller;

use App\Entity\Conseil;
use App\Repository\ConseilRepository;
use App\Security\Voter\ConseilVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final class ConseilController extends AbstractController
{
    #[Route('api/conseils', name: 'conseil', methods: ['GET'])]
    public function getAllConseils(ConseilRepository $conseilRepository, SerializerInterface $serializer): JsonResponse
    {
        $conseilList = $conseilRepository->findByCurrentMonth();

        $jsonConseilList = $serializer->serialize($conseilList, 'json');
        return new JsonResponse($jsonConseilList, Response::HTTP_OK, [], true);
    }

    #[Route('api/conseils/{id}', name: 'conseilDetail', methods: ['GET'])]
    public function getDetailConseil(int $id, ConseilRepository $conseilRepository, SerializerInterface $serializer): JsonResponse
    {
        $conseilList = $conseilRepository->find($id);

        if (!$conseilList) {
            return new JsonResponse(['message' => 'Conseil non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $jsonConseilList = $serializer->serialize($conseilList, 'json');
        return new JsonResponse($jsonConseilList, Response::HTTP_OK, [], true);
    }

    #[Route('api/conseil/{month}', name: 'conseilMonth', methods: ['GET'])]
    public function getMonthConseil(int $month, ConseilRepository $conseilRepository, SerializerInterface $serializer): JsonResponse
    {
        if ($month < 1 || $month > 12) {
            return new JsonResponse(['error' => 'Mois invalide'], Response::HTTP_BAD_REQUEST);
        }

        $conseilMonth = $conseilRepository->findByMonth($month);

        $jsonConseilDetail = $serializer->serialize($conseilMonth, 'json');
        return new JsonResponse($jsonConseilDetail, Response::HTTP_OK, [], true);
    }

    #[Route('api/conseil/{id}', name: 'conseilDelete', methods: ['DELETE'])]
    public function deleteConseil(Conseil $conseil, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted(ConseilVoter::DELETE, $conseil);
        $em->remove($conseil);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/conseils', name: 'conseilCreate', methods: ['POST'])]
    public function createConseil(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        try {
            $conseil = $serializer->deserialize($request->getContent(), Conseil::class, 'json');
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Requête mal formulée'], Response::HTTP_BAD_REQUEST);
        }

        $conseil->setCreated(new \DateTime('now'));
        $conseil->setUpdated(new \DateTime('now'));

        $this->denyAccessUnlessGranted(ConseilVoter::CREATE, $conseil);

        $em->persist($conseil);
        $em->flush();

        $jsonConseil = $serializer->serialize($conseil, 'json');

        $location = $urlGenerator->generate('conseilDetail', ['id' => $conseil->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonConseil, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('api/conseils/{id}', name: 'conseilUpdate', methods: ['PUT'])]
    public function updateConseil(
        int $id,
        Request $request,
        SerializerInterface $serializer,
        ConseilRepository $conseilRepository,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $conseil = $conseilRepository->find($id);

        if (!$conseil) {
            return new JsonResponse(['message' => 'Conseil non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $conseil->setUpdated(new \DateTime('now'));
        $this->denyAccessUnlessGranted(ConseilVoter::EDIT, $conseil);

        $serializer->deserialize($request->getContent(), Conseil::class, 'json', ['object_to_populate' => $conseil]);

        $em->persist($conseil);
        $em->flush();

        $jsonConseil = $serializer->serialize($conseil, 'json');

        $location = $urlGenerator->generate('conseilDetail', ['id' => $conseil->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonConseil, Response::HTTP_OK, ["Location" => $location], true);
    }

}
