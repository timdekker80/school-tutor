<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\SickReport;
use App\Form\LessonTypeForm;
use App\Form\SickReportTypeForm;
use App\Repository\LessonRepository;
use App\Repository\SickReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TeacherController extends AbstractController
{
    #[Route('/teacher', name: 'app_teacher')]
    public function index(): Response
    {
        return $this->render('teacher/index.html.twig', [
            'controller_name' => 'TeacherController',
        ]);
    }

    #[Route('/lesson/new', name: 'teacher_lesson_new')]
    public function newLesson(Request $request, EntityManagerInterface $em): Response
    {
        $lesson = new Lesson();
        $lesson->setTeacher($this->getUser());

        $form = $this->createForm(LessonTypeForm::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lesson);
            $em->flush();

            $this->addFlash('success', 'Lesson created!');
            return $this->redirectToRoute('teacher_schedule');
        }

        return $this->render('teacher/lesson_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/lesson/{id}/edit', name: 'teacher_lesson_edit')]
    public function editLesson(Lesson $lesson, Request $request, EntityManagerInterface $em): Response
    {
        // Check that current user is the teacher of this lesson
        if ($lesson->getTeacher() !== $this->getUser()) {
            throw $this->createAccessDeniedException('This is not your lesson.');
        }

        $form = $this->createForm(LessonTypeForm::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Lesson updated!');
            return $this->redirectToRoute('teacher_schedule');
        }

        return $this->render('teacher/lesson_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/lesson/{id}/delete', name: 'teacher_lesson_delete', methods: ['POST'])]
    public function deleteLesson(Lesson $lesson, EntityManagerInterface $em, Request $request): Response
    {
        if ($lesson->getTeacher() !== $this->getUser()) {
            throw $this->createAccessDeniedException('This is not your lesson.');
        }

        if ($this->isCsrfTokenValid('delete' . $lesson->getId(), $request->request->get('_token'))) {
            $em->remove($lesson);
            $em->flush();
            $this->addFlash('success', 'Lesson deleted!');
        }

        return $this->redirectToRoute('teacher_schedule');
    }

    #[Route('/schedule', name: 'teacher_schedule')]
    public function schedule(LessonRepository $lessonRepo): Response
    {
        $user = $this->getUser();
        $lessons = $lessonRepo->findBy(['teacher' => $user], ['date' => 'ASC']);

        return $this->render('teacher/schedule.html.twig', [
            'lessons' => $lessons,
        ]);
    }

    #[Route('/teacher/sick-report', name: 'teacher_sick_report')]
    public function sickReport(Request $request, EntityManagerInterface $em): Response
    {
        $sickReport = new SickReport();
        $sickReport->setTeacher($this->getUser());

        $form = $this->createForm(SickReportTypeForm::class, $sickReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($sickReport);
            $em->flush();

            $this->addFlash('success', 'Sick report submitted successfully.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('teacher/sick_report.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/announcements', name: 'teacher_announcements')]
    public function announcements(SickReportRepository $sickReportRepo): Response
    {
        $announcements = [
            'Welcome to the teacher portal!',
            'Don’t forget to plan your lessons on time.',
            'Report your sickness using the form below.',
        ];

        $sickReports = $sickReportRepo->findBy([], ['date' => 'DESC']);

        return $this->render('teacher/announcements.html.twig', [
            'announcements' => $announcements,
            'sickReports' => $sickReports,
        ]);
    }
}
