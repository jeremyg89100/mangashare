<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserPageController extends AbstractController
{
    #[Route('/user/page', name: 'app_user_page')]
    #[IsGranted('ROLE_USER')]
    public function index(EntityManagerInterface $em, SluggerInterface $slugger, Request $request, UserPasswordHasherInterface $passwordHasher, #[AutoWire('%kernel.project_dir%/public/uploads/avatar')] string $avatarDirectory,): Response
    {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }
        if ($request->isMethod('POST')) {
            $userBirthdate = $request->request->get('birthdate');
            $username = $request->request->get('pseudo');
            $mail = $request->request->get('email');
            $userDescription = $request->request->get('description');
            $changePassword = $request->request->get('changePassword');
            $confirmPassword = $request->request->get('passwordConfirm');
            $avatarFile = $request->files->get('avatar');

            if ($changePassword != $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe sont différents !');
                return $this->redirectToRoute('app_userpage');
            } else {
                if ($userBirthdate) $user->setBirthDate(new \DateTime($userBirthdate));
                if ($username) $user->setPseudo($username);
                if ($mail) $user->setEmail($mail);
                if ($userDescription) $user->setDescription($userDescription);
                if ($changePassword) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $changePassword);
                    $user->setPassword($hashedPassword);
                }

                if ($avatarFile) {
                    $originalFileName = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFileName = $slugger->slug($originalFileName);
                    $newFileName = $safeFileName . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                    $avatarFile->move($avatarDirectory, $newFileName);

                    $user->setAvatar($newFileName);
                }

                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('app_user_page');
            }
        }
        return $this->render('user_page/index.html.twig', [
            'user' => $user,
        ]);
    }
}
