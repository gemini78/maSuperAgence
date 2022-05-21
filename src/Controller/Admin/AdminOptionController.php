<?php

namespace App\Controller\Admin;

use App\Entity\Option;
use App\Form\OptionType;
use App\Repository\OptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/option')]
class AdminOptionController extends AbstractController
{
    #[Route('/', name: 'admin_option_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $options = $entityManager
            ->getRepository(Option::class)
            ->findAll();

        return $this->render('admin/option/index.html.twig', [
            'options' => $options,
        ]);
    }

    #[Route('/new', name: 'admin_option_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $option = new Option();
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($option);
            $entityManager->flush();

            return $this->redirectToRoute('admin_option_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/option/new.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    /* #[Route('/{id}', name: 'admin_option_show', methods: ['GET'])]
    public function show(Option $option): Response
    {
        return $this->render('admin/option/show.html.twig', [
            'option' => $option,
        ]);
    } */

    #[Route('/{id}/edit', name: 'admin_option_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Option $option, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_option_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/option/edit.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_option_delete', methods: ['POST'])]
    public function delete(Request $request, Option $option, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$option->getId(), $request->request->get('_token'))) {
            $entityManager->remove($option);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_option_index', [], Response::HTTP_SEE_OTHER);
    }
}
