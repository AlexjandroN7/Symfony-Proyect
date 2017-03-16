<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
   /**
    * @Route (path="/", name="app_posts_lista")
    */

   public function IndexAction()
   {
       $m = $this->getDoctrine()->getManager();
       $repo = $m->getRepository('AppBundle:Post');

       $m->flush();
       $posts = $repo->findAll();
       return $this->render(':posts:lista.html.twig',
           [
               'posts' => $posts
           ]);
   }

    /**
     * @Route (path="/add",
     * name="app_posts_add")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function AddAction()
    {
        $Post = new Post();
        $form = $this->createForm(PostType::class, $Post);

        return $this->render(':posts:form.html.twig',
            [
                'form'  => $form->createView(),
                'action'  => $this->generateUrl('app_posts_doAdd'),
            ]);
    }

    /**
     * @Route (path="/doadd",
     *      name="app_posts_doAdd")
     * @Security("has_role('ROLE_USER')")
     */

    public function doAddAction(Request $request)
    {

        $Post= new Post();
        $form = $this->createForm(PostType::class, $Post);

        $form->handleRequest($request);

        if($form->isValid()) {
            $user = $this->getUser();
            $Post->setAuthor($user);
            $m = $this->getDoctrine()->getManager();
            $m->persist($Post);
            $m->flush();

            return $this->redirectToRoute('app_index_index');
        }
            return $this->render(':posts:form.html.twig',
                 [
                     'form'  => $form->createView(),
                     'action'  => $this->generateUrl('app_posts_doAdd')
                 ]);

    }

    /**
     * @Route (
     *     path="/update/{id}",
     *     name="app_posts_update"
     * )
     * @Security("has_role('ROLE_USER')")
     */

    public function updateAction($id)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Post');

        $Post = $repo->find($id);

        $form = $this->createForm(PostType::class, $Post);

        return $this->render(':posts:form.html.twig',
            [
                'form' => $form->CreateView(),
                'action' => $this->generateUrl('app_posts_doUpdate', ['id' => $id]),
            ]);
    }

    /**
     * @Route (
     *     path="/doUpdate/{id}",
     *     name="app_posts_doUpdate")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */

    public function doUpdateAction($id, Request $request)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Post');
        $Post = $repo->find($id);
        $form = $this->createForm(PostType::class, $Post);

        $form->handleRequest($request);
        if($form->isValid()){
            $m->flush();

            return $this->redirectToRoute('app_index_index');
        }

        return $this->render(':posts:form.html.twig',
            [
                'form' => $form->CreateView(),
                'action' => $this->generateUrl('app_posts_doUpdate', ['id' => $id]),
            ]);

    }

    /**
     * @Route (
     *     path="/remove/{id}",
     *     name="app_posts_remove"
     * )
     * @Security("has_role('ROLE_USER')")
     */

    public function removeAction($id)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Post');

        $Post = $repo->find($id);
        $m->remove($Post);
        $m->flush();

        $this->addFlash('messages', 'Post Deleted');

        return $this->redirectToRoute('app_index_index');

    }

}
