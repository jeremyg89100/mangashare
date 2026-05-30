<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class UserPageController extends AbstractController
{
    #[Route('/user/page', name: 'app_user_page')]
    #[IsGranted('ROLE_USER')]
    public function index(EntityManagerInterface $em, SluggerInterface $slugger, Request $request, UserPasswordHasherInterface $passwordHasher, #[Autowire('%kernel.project_dir%/public/uploads/avatar')] string $avatarDirectory): Response
    {
        $user = $this->getUser();
        \assert($user instanceof User);

        if ($request->isMethod('POST')) {
            $userBirthdate = $request->request->getString('birthdate');
            $username = $request->request->getString('pseudo');
            $mail = $request->request->getString('email');
            $userDescription = $request->request->getString('description');
            $changePassword = $request->request->getString('changePassword');
            $confirmPassword = $request->request->getString('passwordConfirm');
            $avatarFile = $request->files->get('avatar');

            if ($changePassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe sont différents !');

                return $this->redirectToRoute('app_userpage');
            }
            if ('' !== $userBirthdate) {
                $user->setBirthDate(new \DateTimeImmutable($userBirthdate));
            }
            if ('' !== $username) {
                $user->setPseudo($username);
            }
            if ('' !== $mail) {
                $user->setEmail($mail);
            }
            if ('' !== $userDescription) {
                $user->setDescription($userDescription);
            }
            if ('' !== $changePassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $changePassword);
                $user->setPassword($hashedPassword);
            }

            if ($avatarFile instanceof UploadedFile) {
                $originalFileName = pathinfo($avatarFile->getClientOriginalName(), \PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$avatarFile->guessExtension();

                $avatarFile->move($avatarDirectory, $newFileName);

                $user->setAvatar($newFileName);
            }

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_user_page');
        }

        return $this->render('user_page/index.html.twig', [
            'user' => $user,
        ]);
    }
}
