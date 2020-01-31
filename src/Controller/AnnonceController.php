<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use DateInterval;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/annonce")
 */
class AnnonceController extends AbstractController
{
    /**
     * @Route("/", name="annonce_index", methods={"GET"})
     * @param AnnonceRepository $annonceRepository
     * @return Response
     */
    public function index(AnnonceRepository $annonceRepository): Response
    {
        return $this->render(
            'annonce/index.html.twig', [
            'annonces' => $annonceRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new", name="annonce_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $annonce = new Annonce();
        $form    = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setCreatedAt(new DateTime());
            $annonce->setExpiredAt((new DateTime())->add(new DateInterval('P' . $this->getParameter('validityDays') . 'D')));

            $annonce->setUser($this->getUser());

            $images = $annonce->getImages();
            foreach ($images as $key => $image) {
                $image->setAnnonce($annonce);
                $images->set($key, $image);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('annonce_index');
        }

        return $this->render(
            'annonce/new.html.twig', [
            'annonce' => $annonce,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annonce_show", methods={"GET"})
     * @param Annonce $annonce
     * @return Response
     */
    public function show(Annonce $annonce): Response
    {
        $annonce->setVisites($annonce->getVisites()+1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($annonce);
        $entityManager->flush();
        return $this->render(
            'annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="annonce_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Annonce $annonce
     * @return Response
     */
    public function edit(Request $request, Annonce $annonce): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            //dd($request);
            //die();
            $images = $annonce->getImages();
            dump($images);
            foreach ($images as $key => $image) {
                $image->setAnnonce($annonce);
                $images->set($key, $image);
                if (!$image->getImageName() && !$image->getImageFile()){
                    $annonce->removeImage($image);
                }
            }
            dump($images);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute(
                'annonce_index');
        }

        return $this->render(
            'annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annonce_delete", methods={"DELETE"})
     * @param Request $request
     * @param Annonce $annonce
     * @return Response
     */
    public function delete(Request $request, Annonce $annonce): Response
    {
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonce_index');
    }
}
