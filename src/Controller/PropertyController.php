<?php

namespace App\Controller;

use App\Entity\Property;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{

    const HEAT = [
        0 => 'electric',
        1 => 'gaz'
    ];

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/biens', name: 'property_index')]
    public function index(): Response
    {

        // $repoProperty = $this->doctrine->getRepository(Property::class);


        return $this->render('property/index.html.twig', [
            'current_menu' => 'property'
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
