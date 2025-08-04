<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'users', methods: ['GET'])]
    public function getAllUsers(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $userList = $userRepository->findAll();

        $jsonUserList = $serializer->serialize($userList, 'json');
        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    #[Route('api/user/{id}', name: 'userDetail', methods: ['GET'])]
    public function getDetailUser(int $id, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $userList = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $jsonUserList = $serializer->serialize($userList, 'json');
        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    #[Route('api/users', name: 'userCreate', methods: ['POST'])]
    public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Requête invalide'], Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

        $em->persist($user);
        $em->flush();

        $jsonConseil = $serializer->serialize($user, 'json');

        $location = $urlGenerator->generate('userDetail', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonConseil, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('api/users/{id}', name: 'userUpdate', methods: ['PUT'])]
    public function updateUser(
        int $id,
        Request $request,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $user = $userRepository->find($id);

        if(!$user){
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        try {
            $serializer->deserialize($request->getContent(), User::class, 'json', ['object_to_populate' => $user]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        if($user->getPassword()){
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
        }

        $em->persist($user);
        $em->flush();

        $jsonConseil = $serializer->serialize($user, 'json');

        $location = $urlGenerator->generate('userDetail', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonConseil, Response::HTTP_OK, ["Location" => $location], true);
    }

    #[Route('api/user/{id}', name: 'userDelete', methods: ['DELETE'])]
    public function deleteConseil(User $user, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted(UserVoter::DELETE, $user);

        $em->remove($user);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
