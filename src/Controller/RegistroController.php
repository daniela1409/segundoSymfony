<?php

namespace App\Controller;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegistroController extends AbstractController
{
    /**
     * @Route("/registro", name="registro")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user); //crear formulario
        $form -> handleRequest($request); //Se determina si el formulario fue enviado

        //form enviado
        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($passwordEncoder->encodePassword($user, $form['password']->getData()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', user::REGISTRO_EXITOSO);
            return $this->redirectToRoute("registro");
        }

        return $this->render('registro/index.html.twig', [
            'formulario' => $form->createView()
        ]);
    }
}
