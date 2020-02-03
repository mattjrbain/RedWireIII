<?php

namespace App\Controller;

use App\Entity\Annonce;
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
     * @Route("/", name="rubrique")
     */
    public function index(RubriqueRepository $repo)
    {
        //$repo = $this->getDoctrine()->getRepository(Rubrique::class);


        $rubrique = $repo->findAll();

        return $this->render('rubrique/index.html.twig', [
            'controller_name' => 'RubriqueController',
            'rubriques' => $rubrique
        ]);
    }

//    /**
//     * @Route("/rubrique/{id}", name="annonce_show")
//
//     */
//    public function show(Rubrique $rubrique){
//
//        return $this->render('rubrique/show.html.twig', [
//            'rubrique' => $rubrique
//
//        ]);
//    }

}
