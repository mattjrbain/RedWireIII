<?php

namespace App\Controller;

use App\Entity\Rubrique;
use App\Form\RubriqueType;
use App\Repository\AnnonceRepository;
use App\Repository\RubriqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RubriqueController extends AbstractController
{
    /**
     * @Route("/rubrique", name="rubrique")
     */
    public function index(AnnonceRepository $repo)
    {
        //$repo = $this->getDoctrine()->getRepository(Rubrique::class);


        $annonce = $repo->findAll();

        return $this->render('rubrique/index.html.twig', [
            'controller_name' => 'RubriqueController',
            'annonces' => $annonce
        ]);
    }

    
    
}
