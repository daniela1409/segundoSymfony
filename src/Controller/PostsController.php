<?php

namespace App\Controller;

use App\Entity\Comentarios;
use App\Entity\Posts;
use App\Form\PostsType;
use App\Form\ComentarioType;
use FFI\Exception;
use PhpParser\Node\Expr\Throw_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;

use function PHPUnit\Framework\throwException;

class PostsController extends AbstractController
{
    /**
     * @Route("/registrarPosts", name="posts")
     */
    public function index(Request $request, SluggerInterface $slugger)
    {

        $post = new Posts();
        $form = $this->createForm(PostsType::class, $post); //crear formulario
        $form -> handleRequest($request); //Se determina si el formulario fue enviado

        if($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('foto')->getData(); // nombre campo

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('images_directory'), //ruta que es definida en services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ha ocurrido un error');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setFoto($newFilename);
            }
            $user = $this->getUser();
            $post->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            $this->addFlash('success', posts::REGISTRO_EXITOSO);
            return $this->redirectToRoute("dashboard");
        }

        return $this->render('posts/index.html.twig', [
            'controller_name' => 'PostsController',
            'formulario' => $form->createView()
        ]);
    }

    /**
     * @Route("/verPosts/{id}", name="verPosts")
     */
    public function verPost($id, Request $request){

        $comentario = new Comentarios();
        $form = $this->createForm(ComentarioType::class, $comentario);
        $form -> handleRequest($request); //Se determina si el formulario fue enviado
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Posts::class)->find($id);
        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            $comentario->setUser($user);
            $comentario->setPost($post);
            $comentario->setFechaPublicacion(new \DateTime());
            $em->persist($comentario);
            $em->flush();
            $this->addFlash('success', comentarios::REGISTRO_EXITOSO);
        }

        return $this->render('posts/verPost.html.twig', 
        ['post' => $post,
        'formulario' => $form->createView(),
        ]);

    }

    /**
     * @Route("/misPosts", name="misPosts")
     */

    public function misPosts(){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Posts::class)->findBy(['user' => $user]);

        return $this->render('posts/misPosts.html.twig', ['posts' => $posts]);
    }

    /**
     * @Route("/likes", options={"expose"=true}, name="likes")
     */
    public function agregarLikes(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $id = $request->request->get('id');
            $post = $em->getRepository(Posts::class)->find($id);
            $likes = $post->getLikes();
            $likes .= $post->getId() . ',';
            $post->setLikes($likes);
            $em->flush();

            return new JsonResponse(['likes' => $likes]);
        }
        else{
            throw new \Exception('Hpta, nos hackean');
        }
    }



}
