<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Image;
use App\Form\Annonce1Type;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="annonce_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(Annonce1Type::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $annonce->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('annonce_index');
        }


        return $this->render('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="annonce_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Annonce $annonce
     * @return Response
     */
    public function edit(Request $request, Annonce $annonce): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('annonce_index');
        }

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annonce_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Annonce $annonce
     * @return Response
     */
    public function delete(Request $request, Annonce $annonce): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonce_index');
    }

    /**
     * @Route("/create", name="annonce_create")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws Exception
     */
    public function createAnnonce(Request $request, EntityManagerInterface $manager)
    {
        $annonce = new Annonce();
        $form    = $this->createForm(AnnonceType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$annonce->getId()) {//if $article has no id which means it is not in DB
                $annonce->setCreatedAt(new DateTime());
                $annonce->setExpiredAt((new DateTime())->add(new DateInterval('P' . $this->getParameter('validityDays') . 'D')));
                $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
                $annonce->setUser($this->getUser());
            }

            $uploads_directory = $this->getParameter('uploads_directory');

            $files = $request->files->get('annonce')['images'];
            foreach ($files as $file) {

                if ($file instanceof UploadedFile) {
                    $filename = md5(uniqid()) . '.' . $file->guessExtension();
                    $image    = new Image();
                    $image->setSrc('img/' . $filename);
                    $annonce->addImage($image);
                    $manager->persist($image);
                    $manager->persist($annonce);
                    $file->move($uploads_directory, $filename);
                }
            }
            $manager->persist($annonce);
            $manager->flush();
            return $this->redirectToRoute('annonce_show', ['annonce' => $annonce, 'id' => $annonce->getId()]);
        }

        return $this->render(
            'annonce/new1.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annonce_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param Annonce $annonce
     * @return Response
     */
    public function show(Annonce $annonce): Response
    {
        dump($annonce->getImages());
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }
}
