<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController
{
  private $repository;
  private $doctrine;

  public function __construct(PropertyRepository $repository, ManagerRegistry $doctrine)
  {
    $this->repository = $repository;
    $this->doctrine = $doctrine;
  }

  #[Route('/admin', name: 'admin_property_index')]
  public function index()
  {
    $properties = $this->repository->findAll();
    return $this->render('admin/property/index.html.twig', [
      'properties' => $properties,
    ]);
  }

  #[Route('/admin/property/create', name: 'admin_property_new')]
  public function new(Request $request)
  {
    $property = new Property;

    $form = $this->createForm(PropertyType::class, $property);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->doctrine->getManager();
      $em->persist($property);
      $em->flush();
      $this->addFlash('success', 'Création effectuée avec succèss');
      return $this->redirectToRoute('admin_property_index');
    }

    return $this->render('admin/property/new.html.twig', [
      'property' => $property,
      'form' => $form->createView()
    ]);
  }

  #[Route('/admin/property/delete/{id}', name: 'admin_property_delete', methods: ['POST'])]
  public function delete(Property $property, Request $request)
  {
    if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->request->get('_token'))) {
      // dd($request->request);
      // dump($property->getId());
      // dd($request->request->get('_token'));
      $em = $this->doctrine->getManager();
      $em->remove($property);
      $em->flush();
      $this->addFlash('success', 'Suppression effectuée avec succèss');
    }
    return $this->redirectToRoute('admin_property_index');
  }

  #[Route('/admin/property/{id}', name: 'admin_property_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, Property $property)
  {

    $form = $this->createForm(PropertyType::class, $property);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->doctrine->getManager();
      $em->flush();
      $this->addFlash('success', 'Modification effectuée avec succèss');
      return $this->redirectToRoute('admin_property_index');
    }

    return $this->render('admin/property/edit.html.twig', [
      'property' => $property,
      'form' => $form->createView()
    ]);
  }
}
