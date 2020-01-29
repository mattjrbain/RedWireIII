<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Rubrique;
use App\Form\RubriqueType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonces", name="annonceByRubrique")
     */
    public function index(AnnonceRepository $repo, Request $request)
    {
        // $annonces = $repo->findByRubrique($id);
        $annonces = $repo->findAll();

        // $rubrique = new Rubrique();
        // $rubrique->setLibelle('new');

    $form = $this->createForm(RubriqueType::class, /*$rubrique*/ NULL);
        $form->handleRequest($request);
        
        // if ($form->isSubmitted() && $form->isValid()) {

        //     $annonces = $repo->findByRubrique($request->get('rubrique')['id']);
    
        //     return $this->redirectToRoute('annonceByRubrique', [
        //         'annonces' => $annonces, 
        //         'form' => $form->createView()
        //     ]);
        // }

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
            'form' => $form->createView()
        ]);
    }



    // private function rubTab(){
    //     $barbapapa = $this->getDoctrine()->getRepository(Rubrique::class)
    //     ->findALl();
    //     $tabval = [];
    //     foreach ( $barbapapa as $item){
    //         $tabval[$item->getLibelle()]=$item->getId();
    //     }
    //     return $tabval;
    // }
}
