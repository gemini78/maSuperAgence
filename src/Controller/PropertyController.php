<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PropertyController extends AbstractController
{

    const HEAT = [
        0 => 'electric',
        1 => 'gaz'
    ];
    private $propertyRepository;

    public function __construct(PropertyRepository $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
    }

    #[Route('/biens', name: 'property_index')]
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
        $search = new PropertySearch;
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);
        $properties = $paginator->paginate(
            $this->propertyRepository->findAllVisibleQuery($search),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('property/index.html.twig', [
            'current_menu'  => 'property',
            'properties'    => $properties,
            'form' => $form->createView()
        ]);
    }


    #[Route('/biens/{slug}-{id}', name: 'property_show', requirements: ['slug' => '[a-z0-9\-]*'])]
    public function show(Property $property, string $slug): Response
    {
        $slugProperty = $property->getSlug();
        if ($slugProperty !== $slug) {
            return $this->redirectToRoute('property_show', [
                'id' => $property->getId(),
                'slug' => $slugProperty
            ], 301);
        }

        return $this->render('property/show.html.twig', [
            'current_menu' => 'property',
            'property' => $property
        ]);
    }
}
