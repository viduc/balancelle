<?php /** @noinspection PropertyCanBeStaticInspection */
/** @noinspection PropertyCanBeStaticInspection */

/** @noinspection PropertyCanBeStaticInspection */

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\User;
use BalancelleBundle\Form\MailType;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use BalancelleBundle\Form\UserPWDType;
use BalancelleBundle\Form\UserType;

/**
 * User controller.
 *
 */
class UserController extends AppController //implements FamilleInterface
{
    public static $roles = [
        'ROLE_USER',
        'ROLE_ADMIN',
        'ROLE_PARENT',
        'ROLE_PRO'
    ];

    /**
     * Liste tout les utilisateurs
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('BalancelleBundle:User')->findAll();

        return $this->render(
            '@Balancelle/User/index.html.twig',
            array('users' => $users)
        );
    }

    /**
     * Créé un nouvel utilisateur
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function newAction(Request $request, CommunicationController $com)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(md5(uniqid('', true)));
            $user->setEnabled(true);
            $user->setUsername($this->genererLogin($user));
            $user->setPlainPassword(md5(uniqid('', true)));
            $userManager->updateUser($user);
            $pdf = $this->getParameter('web_dir');
            $pdf .= '/bundles/balancelle/documents/MODE_EMPLOI_BALANCELLE.pdf';
            $com->envoyerMail(
                [$user->getEmail()],
                'Votre espace personnel',
                null,
                [$pdf],
                'comptes@labalancelle.yo.fr',
                $this->renderView(
                    '@Balancelle/Communication/enregistrement_email.html.twig',
                    array(
                        'token' => $user->getConfirmationToken(),
                        'login' => $user->getUsername())
                )
            );
            $succes = "L'utilisateur " . $user->getPrenom() . ' ';
            $succes .= $user->getNom() . ' a bien été enregistré';
            $this->addFlash('success', $succes);
            return $this->redirectToRoute(
                'user_edit',
                array('id' => $user->getId())
            );
        }

        return $this->render('@Balancelle/User/edit.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }

    /**
     * Génère un login unique pour l'utilisateur
     * @param User $user
     * @return string
     * @throws Exception
     */
    private function genererLogin(User $user)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $nom = strtolower($this->enleverCaracteresSpeciaux($user->getNom()));
        $prenom = strtolower($this->enleverCaracteresSpeciaux($user->getPrenom()));
        $i = 1;
        while (true) {
            if (strlen($prenom) >= $i) {
                $login = $nom . substr($prenom, 0, $i);
                $i++;
            } else {
                $login = $nom . $prenom . random_int(1, 999);
            }
            if (!$userManager->findUserByUsername($login)) {
                return $login;
            }
        }
        return null;
    }

    /**
     * Supprime les caractères spéciaux d'une chaine
     * @param String $str
     * @param string $charset
     * @return string|string[]|null
     */
    private function enleverCaracteresSpeciaux($str, $charset='utf-8')
    {

        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);

        return $str;
    }

    /**
     * Edite un utilisateur
     * @param Request $request
     * @param User $user
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm(
            UserType::class,
            $user
        );
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $succes = "L'utilisateur " . $user->getPrenom() . ' ';
            $succes .= $user->getNom() . ' a bien été modifié';
            $this->addFlash('success', $succes);
            return $this->redirectToRoute(
                'user_edit',
                array('id' => $user->getId())
            );
        }
        $em = $this->getDoctrine()->getManager();
        $famille = $em->getRepository(
            'BalancelleBundle:Famille'
        )->findByFamille($user->getId());
        return $this->render('@Balancelle/User/edit.html.twig', array(
            'familleUtilisateur' => $famille,
            'user' => $user,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Supprime un utilisateur
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur = $user->getPrenom() . ' ' . $user->getNom();
            $em = $this->getDoctrine()->getManager();
            if ($em->getRepository(
                'BalancelleBundle:Famille'
            )->findByFamille($user->getId()) === null) {
                $em->remove($user);
                $em->flush();
                $succes = "L'utilisateur " . $utilisateur;
                $succes .= ' a bien été modifié';
                $this->addFlash('success', $succes);
            } else {
                $error = "L'utilisateur " . $utilisateur;
                $error .= " n'a pas pu être supprimé car il est lié à une famille";
                $this->addFlash('danger', $error);
            }

        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return FormInterface
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl(
                'user_delete',
                array('id' => $user->getId())
            ))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Méthode qui permet à un utilisateur de finaliser son compte
     * @param type $token - le token
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function enregistrerMotDePasseUtilisateurAction(
        $token,
        Request $request
    ) {
        if ($token !== null) {
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserBy(
                array('confirmationToken' => $token
            ));
            $form = $this->createForm(
                UserPWDType::class,
                $user
            );
            $form->handleRequest($request);
            $data = $form->getData();
            if (
                $request->isMethod('POST')
                    && $form->isSubmitted() && $form->isValid()
                    && ($data->getPlainPassword() !== null
                    && !empty($data->getPlainPassword()))
            ) {
                $user->setPlainPassword($data->getPlainPassword());
                $user->setEnabled(true);
                $user->setConfirmationToken(null);
                $userManager->updateUser($user);

                return $this->redirect(
                    $this->generateUrl('fos_user_security_login')
                );
            }

            if ($user !== null) {
                return $this->render(
                    '@Balancelle/User/enregistrement_mdp.html.twig',
                    array('form' => $form->createView())
                );
            }
        }

        return $this->redirect(
            $this->generateUrl('fos_user_security_login')
        );
    }

    /**
     * @param User $user - l'utilisateur
     * @param Request $request - la requete
     * @return mixed
     */
    private function gestionDesRoles($user, $request)
    {//TODO vérifier cette méthode
        $user->removeRole('ROLE_USER');
        if (isset(
            $request->request->get('balancellebundle_user')['roleUser']
        )) {
            $user->addRole('ROLE_USER');
        }

        return $user;
    }


    /**
     * permet de vérifier qu'un mail n'est pas utilisé par un autre utilisateur
     * @param Request $request - requête ajax
     * @return JsonResponse (ok si pas utilisé, nok si déjà utilisé)
     */
    public function verifiermailAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mails = $em
            ->getRepository('BalancelleBundle:User')
            ->findBy(array('email' => $request->get('email')));
        if (count($mails)>0) {
            /* si l'id de l'utilisateur est null on est dans le mode création
             * donc si le mail est déjà présent en base on renvoie nok */
            if ($request->get('id') === null) {
                return new JsonResponse('nok');
            }
            /* sinon on vérifie que le mail n'est pas déjà celui de
             * l'utilisateur courant et si oui on renvoie ok */
            foreach ($mails as $mail) {
                if ($mail->getId() === $request->get('id')) {
                    return new JsonResponse('ok');
                }
            }
            /* si le mail est déjà utilisé pas un autre utilisateur on
             * renvoie nok */
            return new JsonResponse('nok');
        }

        return new JsonResponse('ok');
    }

    /**
     * Renvoi le mail de création de mot de passe à l'utilisateur
     * @param Request $request
     * @param CommunicationController $com
     * @return JsonResponse
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renvoyermailAction(
        Request $request,
        CommunicationController $com
    ) {
        $em = $this->getDoctrine()->getManager();
        $user = $em
            ->getRepository('BalancelleBundle:User')
            ->find($request->get('id'));
        $pdf = $this->getParameter('web_dir');
        $pdf .= '/bundles/balancelle/documents/MODE_EMPLOI_BALANCELLE.pdf';
        $com->envoyerMail(
            [$user->getEmail()],
            'Votre espace personnel',
            null,
            [$pdf],
            'comptes@labalancelle.yo.fr',
            $this->renderView(
                '@Balancelle/Communication/enregistrement_email.html.twig',
                array(
                    'token' => $user->getConfirmationToken(),
                    'login' => $user->getUsername())
            )
        );
        return new JsonResponse(
            'Le mail pour la génération du mot de passe a bien été envoyé'
        );
    }

    /**
     * Envoie un mail à l'utilisateur
     * @param Request $request
     * @param User $user - l'utilisateur' concerné
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function envoyerMailAction(Request $request, User $user)
    {
        $form = $this->createForm(
            MailType::class
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documents = $form->getData()['documents'];
            array_pop($documents);
            $this->get('communication')->envoyerMail(
                [$user->getEmail()],
                $form->getData()['sujet'],
                $form->getData()['message'],
                $documents
            );
            $succes = 'Votre email a bien été envoyé à ' . $user->getPrenom();
            $succes .= ' ' . $user->getNom();
            $this->addFlash('success', $succes);
            return $this->redirectToRoute(
                'user_edit',
                array('id' => $user->getId())
            );
        }
        $titre = 'Envoyer un email à '. $user->getPrenom() . ' ';
        $titre .= $user->getNom();
        return $this->render(
            '@Balancelle/Communication/email_create.html.twig',
            array(
                'form' => $form->createView(),
                'titre' => $titre
            )
        );
    }
}
