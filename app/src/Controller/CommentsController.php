<?php

namespace App\Controller;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comment;
use App\Service\NormalizeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    /**
     * @Route("/comments", name="comments")
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function list(Request $request):Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $object_id=$request->query->get('object_id');
        $object_name=$request->query->get('object_name');
        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(array('object_id'=>$object_id,'object_name'=>$object_name));
            $data=(new NormalizeService())->normalizeByGroup($comments);

        return new Response($this->json($data),200);
    }

    /**
     * @Route("/comments/{id}", name="comment_show",requirements={"id"="\d+"})
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function show(Request $request,$id): Response
    {
        $comment = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id);
        $data=(new NormalizeService())->normalizeByGroup($comment);
        return new Response($this->json($data),200);
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
            return new Response($this->json($comment),201);
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
            return new Response($this->json($comment),200);
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
