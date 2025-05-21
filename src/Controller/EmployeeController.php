<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductTypeForm;
use App\Form\StudentProfileTypeForm;
use App\Form\TeacherRegistrationTypeForm;
use App\Form\UserTypeForm;
use App\Repository\AnnouncementRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'app_employee')]
    public function index(): Response
    {
        return $this->render('employee/index.html.twig', [
            'controller_name' => 'EmployeeController',
        ]);
    }

    #[Route('/employeeProducts', name: 'employeeProducts')]
    public function employeeProducts(EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Product::class)->findAll();

        return $this->render('employee/employeeProducts.html.twig', compact('products'));
    }

    #[Route('/newProducts', name: 'employee_product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductTypeForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product created!');
            return $this->redirectToRoute('employeeProducts');
        }

        return $this->render('employee/productAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/employee/product/{id}/edit', name: 'employee_product_edit')]
    public function editProduct(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProductTypeForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Product bewerkt!');
            return $this->redirectToRoute('employeeProducts');
        }

        return $this->render('employee/productEdit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/employee/product/{id}/delete', name: 'employee_product_delete', methods: ['POST'])]
    public function deleteProduct(Product $product, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();

            $this->addFlash('success', 'Product verwijderd!');
        }

        return $this->redirectToRoute('employeeProducts');
    }


    #[Route('/employee/teacher/new', name: 'employee_teacher_new')]
    public function newTeacher(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $teacher = new User();

        $form = $this->createForm(TeacherRegistrationTypeForm::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Wachtwoord hashen
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($teacher, $plainPassword);
            $teacher->setPassword($hashedPassword);

            // Rol zetten
            $teacher->setRoles(['ROLE_TEACHER']);

            // Opslaan
            $em->persist($teacher);
            $em->flush();

            $this->addFlash('success', 'Docentaccount is succesvol aangemaakt.');

            return $this->redirectToRoute('employee_teacher_list'); // Verander deze route naar je lijst met docenten
        }

        return $this->render('employee/teacherNew.html.twig', [
            'teacherForm' => $form->createView(),
        ]);
    }

    #[Route('/employee/teacher/list', name: 'employee_teacher_list')]
    public function teacherList(EntityManagerInterface $em): Response
    {
        $teachers = $em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_TEACHER"%')
            ->getQuery()
            ->getResult();

        return $this->render('employee/teacherList.html.twig', [
            'teachers' => $teachers,
        ]);
    }

    #[Route('/employee/teacher/{id}/lessons', name: 'employee_teacher_lessons')]
    public function teacherLessons(User $teacher, LessonRepository $lessonRepo): Response
    {
        // Optioneel: check of $teacher wel echt docent is
        if (!in_array('ROLE_TEACHER', $teacher->getRoles())) {
            throw $this->createNotFoundException('Geen docent gevonden');
        }

        $lessons = $lessonRepo->findBy(['teacher' => $teacher]);

        return $this->render('employee/teacherLessons.html.twig', [
            'teacher' => $teacher,
            'lessons' => $lessons,
        ]);
    }
    #[Route('/employee/user/list', name: 'employee_user_list')]
    public function userList(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();


        return $this->render('employee/userList.html.twig', [
            'users' => $users,        ]);
    }
    #[Route('/employee/user/list/{id}', name: 'employee_user_list_edit')]
    public function userListEdit(User $users, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserTypeForm::class, $users);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();

            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($users, $newPassword);
                $users->setPassword($hashedPassword);
            }

            $em->flush();

            $this->addFlash('success', 'Gebruiker succesvol bijgewerkt.');
            return $this->redirectToRoute('employee_user_list');
        }

        return $this->render('employee/userListEdit.html.twig', [
            'users' => $users,
            'UserTypeForm' => $form->createView(),
        ]);
    }

    #[Route('/employee/user/delete/{id}', name: 'employee_user_list_delete')]
    public function userListDelete(EntityManagerInterface $entityManager, User $user): Response
    {
        $entityManager->remove($user);

        //We voeren de statements uit (het wordt nog gedelete)
        $entityManager->flush();

        //Uiteraard zetten we een flash-message
        $this->addFlash('success', 'Boek is gewist');

        return $this->redirectToRoute('employee_user_list');
    }

    #[Route('/employee/announcement', name: 'employee_user_announcement')]
    public function announcementList(AnnouncementRepository $announcementRepo): Response
    {
        $announcements = $announcementRepo->findBy([
            'targetRole' => 'ROLE_STUDENT'
        ]);

        return $this->render('employee/announcement.html.twig', [
            'announcements' => $announcements,
        ]);
    }
}
