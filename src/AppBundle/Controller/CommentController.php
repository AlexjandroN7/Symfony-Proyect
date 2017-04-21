<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;


class CommentController extends Controller
{


    /**
     *
     *@Route("/doCreate/{id}", name="app_comment_createAction")
     *
     */
        public function doCreateAction(Request $req, Post $id) {


            $m = $this->getDoctrine()->getManager();
            $r = $m->getRepository('AppBundle:Post');
            $post = $r->find($id);


            $comment =  new Comment();

            $comment->setMessage($req->request->get('messageInput'));
            $comment->setCreator($this->getUser());
            $comment->setPost($post);
            $m->persist($comment);
            $m->flush();


            return $this->redirectToRoute('app_posts_lista');
        }


    /**
     * @Route("/deleteComment/{id}", name="app_comment_delete")
     *
     *
     */

        public function deleteCommentAction(Comment $comment) {

            $Post = $comment->getPost();
            if ($this->getUser() == $comment->getCreator() or $this->getUser() == 'Alejandro' or $this->getUser() == $Post->getAuthor()){
                $m = $this->getDoctrine()->getManager();
                $m->remove($comment);
                $m->flush();
                return $this->redirectToRoute('app_posts_lista');
            } else {
                return $this->redirectToRoute('app_posts_lista');
            }
        }




}
