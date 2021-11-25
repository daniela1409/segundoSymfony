<?php

namespace App\Controller;

use App\Entity\Posts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $user = $this->getUser();

        if($user){
            $em = $this->getDoctrine()->getManager();

            $query = $em->getRepository(Posts::class)->buscarPosts();
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                5 /*limit per page*/
            );
            return $this->render('dashboard/index.html.twig', [
                'controller_name' => 'Bienvenido a Dashboard',
                'pagination' => $pagination
            ]);
        }
        else{
            return $this->redirectToRoute('app_login');
        }
        
    }
}
