<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\StudentProfileTypeForm;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STUDENT')]
class StudentController extends AbstractController
{
    #[Route('/student/profile', name: 'student_profile')]
    public function viewProfile(): Response
    {
        $user = $this->getUser();

        return $this->render('student/profile.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/student/profile-edit', name: 'student_profile_edit')]
    public function profile(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(StudentProfileTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();

            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('student_profile');
        }

        return $this->render('student/profile_edit.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }

    #[Route('/lessonStudent', name: 'student_lessons')]
    public function index(LessonRepository $lessonRepository): Response
    {
        $student = $this->getUser();

        $lessons = $lessonRepository->findBy(['student' => $student]);

        return $this->render('student/lesson.html.twig', [
            'lessons' => $lessons,
        ]);
    }

    #[Route('/lessonShow/{id}', name: 'student_lesson_show')]
    public function show(Lesson $lesson): Response
    {
        $this->denyAccessUnlessGranted('ROLE_STUDENT');

        // Ensure the logged-in student owns the lesson
        if ($lesson->getStudent() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('student/lessonShow.html.twig', [
            'lesson' => $lesson,
        ]);
    }


}
