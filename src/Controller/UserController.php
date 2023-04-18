<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * @Route("/dashboard/users/{id}", name="app_dashboard_user_show")
     */
    public function show(User $user = null)
    {
        if(!$user){
            return $this->redirectToRoute('app_dashboard_users');
        }
        return $this->render('user/user-profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/dashboard/users/{id}/edit-credits", name="app_dashboard_edit_users")
     */
    public function edit(Request $request, User $user = null): Response
    {
        try {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            $credits = !empty($request->request->all()['credits']) ? $request->request->all()['credits'] : 0;
            $isAjax = !empty($request->request->all()['isAjax']) ? $request->request->all()['isAjax'] : false;
            if($credits > 0 && $isAjax){
                $user->setCredits($credits);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                return new JsonResponse([
                    'message' => 'OK',
                    'status' => 200
                ], 200);
            }
            return $this->render('user/edit-credits.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->redirectToRoute('app_dashboard_users');
        }
        
    }
}
