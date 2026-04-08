<?php
// src/Controller/AuthController.php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
#[Route('/api/register', name: 'api_register', methods: ['POST'])]
public function register(
Request $request,
UserPasswordHasherInterface $passwordHasher,
EntityManagerInterface $em,
ValidatorInterface $validator
): JsonResponse {
$data = json_decode($request->getContent(), true);

$user = new User();
$user->setEmail($data['email'] ?? '');
$user->setName($data['name'] ?? '');
$user->setPassword(
$passwordHasher->hashPassword($user, $data['password'] ?? '')
);

$errors = $validator->validate($user);
if (count($errors) > 0) {
return $this->json(['error' => (string) $errors], 400);
}

$em->persist($user);
$em->flush();

return $this->json(['message' => 'User registered successfully'], 201);
}

#[Route('/api/login', name: 'api_login', methods: ['POST'])]
public function login(): void
{
// Handled by lexik_jwt_authentication
}
}
