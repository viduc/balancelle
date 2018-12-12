<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    private $roles = [
        'ROLE_USER',
        'ROLE_ADMIN',
        'ROLE_PARENT',
        'ROLE_ENFANT',
        'ROLE_PRO'
    ];
    
    private $view = 'user';
    
    /**
     * Lists all user entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('BalancelleBundle:User')->findAll();
        
        return $this->render('@Balancelle/User/index.html.twig', array(
            'users' => $users, 'view' => $this->view));
    }

    /**
     * Creates a new user entity.
     *
     */
    public function newAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(false);
        //$user->addRole('ROLE_ADMIN');
        $user->setUsername(uniqid()); // ici il faudra implémenter une méthode de génération de login
        $user->setPlainPassword(md5(uniqid()));
        
        $form = $this->createForm('BalancelleBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(md5(uniqid()));
            $userManager->updateUser($user);
            $message = \Swift_Message::newInstance()
                ->setSubject('Inscription')
                ->setFrom('comptes@labalancelle.yo.fr')
                ->setTo($user->getEmail())
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView(
                        '@Balancelle/User/enregistrement_email.html.twig',
                        array('token' => $user->getConfirmationToken()),
                        'text/html'
                    )
                );
            $this->get('mailer')->send($message);
            return $this->redirectToRoute(
                'user_edit',
                array('id' => $user->getId())
            );
        }

        return $this->render('@Balancelle/User/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
            'view' => $this->view
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm(
            'BalancelleBundle\Form\UserType',
            $user
        );
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($user);
            return $this->redirectToRoute(
                'user_edit',
                array('id' => $user->getId())
            );
        }

        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery(
                'SELECT u FROM BalancelleBundle:User u WHERE u.roles LIKE :role'
            )->setParameter('role', '%"ROLE_ENFANT"%');
        //var_dump($query);
        return $this->render('@Balancelle/User/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'enfants' => $query->execute(),
            'view' => $this->view
        ));
    }

    /**
     * Deletes a user entity.
     *
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
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
     * @param type $token
     * @param Request $request
     * @return type
     */
    public function enregistrerMotDePasseUtilisateurAction(
        $token,
        Request $request
    ) {
        if ($token !== NULL) {
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserBy(
                array("confirmationToken" => $token
            ));
            if ($request->isMethod('POST')
                && ($request->get("plainPassword") !== null
                && !empty($request->get("plainPassword")))) {
                $user->setPlainPassword($request->get("plainPassword"));
                $user->setEnabled(true);
                $user->setConfirmationToken(null);
                $userManager->updateUser($user);

                return $this->redirect(
                    $this->generateUrl("fos_user_security_login")
                );
            } elseif ($user !== NULL) {
                return $this->render(
                    '@Balancelle/User/enregistrement_mdp.html.twig',
                    array()
                );
            }
        } 
        return $this->redirect(
            $this->generateUrl("fos_user_security_login")
        );
    }
    
    private function gestionDesRoles($user, $request)
    {
        $user->removeRole("ROLE_USER");
        if (isset(
            $request->request->get('balancellebundle_user')['roleUser']
        )) {
            $user->addRole("ROLE_USER");
        }
        return $user;
    }
}
