Voilà ce que Smaïl, Bill et moi nous rappelons avoir fait pour la récup de mot de passe par mail.

Installer Swifmailer

Modifier .env pour l'envoi de mail via gmail, utiliser votre propre compte pour les tests.

``` php ###> symfony/swiftmailer-bundle ###
# For Gmail as a transport, use: "gmail://username:password@localhost"
# For a generic SMTP server, use: "smtp://localhost:25?encryption=&auth_mode="
# Delivery is disabled by default via "null://localhost"
MAILER_URL=gmail://username:password@localhost
###< symfony/swiftmailer-bundle ###
```
Ajout du token dans User Entity

``` php     
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;
```
Dans notre SecurityController ajout de :

``` php
    /**
     * @Route("/mdp", name="app_forgotten_password", methods="GET|POST")
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
                return $this->redirectToRoute('home');
            }
 
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
 
            $message = (new \Swift_Message('Oubli de mot de passe - Réinitialisation'))
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
    
    /** Réinitialiation du mot de passe par mail
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
```
Création des templates :

forgottenPassword.html.twig :

``` twig 
    {% extends 'base.html.twig' %}
 
{% block title %}Réinit de mot de passe{% endblock %}
 
{% block body %}
<div class="container bubble">
    <form method="post">
 
        <h2 class="bubble-title">Réinit du mot de passe</h2>
        <label for="inputemail" class="">Entrer votre email</label>
        <input type="email" name="email" id="inputPassword" class="form-control" placeholder="Email" required>
<div class="text-center mt-2">
        <button class="btn btn-lg btn-success mt-2 ml-0" type="submit">
            Envoyer !
        </button>
        </div>
    </form>
    </div>
 
{% endblock %}
```
resetPasswordMail.html.twig :

```html
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
            <title>Réinitialisation Mot de passe</title>
        </head>
        <body>
            <div>
                <h2 style="color:#253f58; font-size: 25px;">Redwire Réinitialisation du mot de passe</h2>
                <p style="font-size: 15px;">
                    <strong style="color: #d92139; font-size: 15px;">
                        {{user.userName}}</strong>
                    tu as oublié ton mot de passe, tu vas pouvoir le réinitialiser en suivant ce lien :</p>
                <p style="font-size: 15px;">
                    <a href="{{ url }}">Réinitialisation du mot de passe</a>
                </p>
                <p style="font-size: 15px;">Redwire Team.
                    <br/>
                    Enjoy !
                </p>
            </div>
        </body>
    </html>
```
resetPassword.html.twig :
``` twig 
    {% extends 'base.html.twig' %}
 
{% block title %}Réinitialisation du mot de passe{% endblock %}
 
{% block body %}
<div class="container bubble">
    <form method="post">
 
        <h2 class="bubble-title">Réinitialisation du mot de passe</h2>
        <label for="inputPassword" class="">Nouveau mot de passe*</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Mot de passe" required>
        <input type="hidden" name="token" value="{token}">
<div class="text-center mt-2">
        <button class="btn btn-lg btn-success mt-2 ml-0" type="submit">
            Envoyer !
        </button>
        </div>
    </form>
    </div>
 
{% endblock %}
```