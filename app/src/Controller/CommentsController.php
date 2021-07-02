<?php

namespace App\Controller;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    /**
     * @Route("/comments", name="comments")
     */
    public function list(Request $request):Response
    {
        /*$criteria_array = array('object_name'=>$request->query->get('object_name'),
            'object_id'=>$request->query->get('object_id'));
        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy($criteria_array);*/
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findAll();
        return new Response(json_encode($comments)
        ,200);
    }
    /**
     * @Route("/comments/{id}", name="comment_show",requirements={"id"="\d+"})
     */
    public function show(Request $request,$id): Response
    {
        $comment = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id);

        return new Response($this->json([
            'id'=> $comment->getId(),
            'text' => $comment->getText(),
            'date' => $comment->getDate(),
            'user_id'=>$comment->getUserid(),
            'object_name' => $comment->getObjectName(),
            'object_id'=>$comment->getObjectId()
        ]),200);
    }
    /**
     * @Route("/comments/create", name="comment_create")
     */
    public function create(Request $request,ValidatorInterface $validator):Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $comment = new Comment();
        $comment->setText($request->request->get('text'));
        $comment->setDate(new \DateTime());
        $comment->setUserid($request->request->get('user_id'));
        $comment->setObjectId($request->request->get('object_id'));
        $comment->setObjectName($request->request->get('object_name'));

        $errors = $validator->validate($comment);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        else{
            $entityManager->persist($comment);
            $entityManager->flush();
            return new Response($this->json([
                'id'=> $comment->getId(),
                'text' => $comment->getText(),
                'date' => $comment->getDate(),
                'user_id'=>$comment->getUserid(),
                'object_name' => $comment->getObjectName(),
                'object_id'=>$comment->getObjectName()
            ]),201);
        }

    }
    /**
     * @Route("/comments/update/{id}", name="comment_update",requirements={"id"="\d+"})
     */
    public function update(Request $request, ValidatorInterface $validator,$id):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id);

        $comment->setText($request->request->get('text'));
        $comment->setUserid($request->request->get('user_id'));
        $comment->setObjectId($request->request->get('object_id'));
        $comment->setObjectName($request->request->get('object_name'));
        $errors = $validator->validate($comment);
        if (count($errors) > 0) {
            return new Response($this->json(['errors'=>$errors]), 400);
        }
        else{
            $entityManager->flush();
            return new Response($this->json([
                'id'=> $comment->getId(),
                'text' => $comment->getText(),
                'date' => $comment->getDate(),
                'user_id'=>$comment->getUserid(),
                'object_name' => $comment->getObjectName(),
                'object_id'=>$comment->getObjectName()
            ]),200);
        }
    }
    /**
     * @Route("/comments/delete/{id}", name="comment_delete",requirements={"id"="\d+"})
     */
    public function delete(Request $request,$id):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id);
        if($comment){
            $entityManager->remove($comment);
            $entityManager->flush();
        }
        return new Response(null, 204);
    }
}
