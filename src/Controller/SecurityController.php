<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\RedWireAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // dump($this->getUser()->);
        //if($this->getUser()){
            // if ($this->getUser()->getRoles()[0] == 'ROLE_ADMIN') {
            //     dump($this->getUser());
           // return $this->redirectToRoute('easyadmin');
        //}
        

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    // /**
    //  * @Route("/admin", name="admin")
    //  */
    // public function admin(){
    //     return $this->render('/../vendor/easycorp/easyadmin-bundle/src/Resources/views/default/layout.html.twig');
    // }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
//        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/mdp", name="app_forgotten_password", methods="GET|POST")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param \Swift_Mailer $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     */
    public function forgottenPassword(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        if ($request->isMethod('POST')) {
 
            $email = $request->request->get('email');
 
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
 
            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu, recommence !');
                return $this->redirectToRoute('app_forgotten_password');
            }
            $token = $tokenGenerator->generateToken();
 
            try{
                $user->setToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('rubrique');
            }
 
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
 
            $message = (new \Swift_Message('Oubli de mot de passe - Réinisialisation'))
                ->setFrom('send@example.com')
                ->setTo($user->getEmail())
                ->setBody(
                $this->renderView(
                    'security/resetPasswordMail.html.twig',
                    [
                        'user'=>$user,
                        'url'=>$url
                    ]
                ),
                    'text/html'
                );
            $mailer->send($message);
 
            $this->addFlash('info', 'Mail envoyé, tu vas pouvoir te connecter à nouveau !');
 
            return $this->redirectToRoute('app_login');
        }
 
        return $this->render('security/forgottenPassword.html.twig');
    }
    
    /** Réinisialiation du mot de passe par mail
     * @Route("/reinitialiser-mot-de-passe/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, RedWireAuthenticator $authenticator)
    {
        //Reset avec le mail envoyé
        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();
 
            $user = $entityManager->getRepository(User::class)->findOneBy(['token'=>$token]);
            /* @var $user User */
 
            if ($user === null) {
                $this->addFlash('danger', 'Mot de passe non reconnu');
                return $this->redirectToRoute('home');
            }
 
            $user->setToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->flush();
 
            $this->addFlash('notice', 'Mot de passe mis à jour !');
 
            // return $this->redirectToRoute('app_login');

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'); // firewall name in security.yaml


        }else {
 
            return $this->render('security/resetPassword.html.twig', ['token' => $token]);
        }
 
    }
}
