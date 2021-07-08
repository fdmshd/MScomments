<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Comment;
use App\Service\NormalizeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CommentsController extends AbstractController
{
    /**
     * @Route("/comments", name="comments",methods={"GET"})
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function list(CommentRepository $commentRepository, Request $request):Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $this->getDoctrine()->getManager();
        $object_id=$request->query->get('object_id');
        $object_name=$request->query->get('object_name');
        $order = $request->query->get('order','DESC');
        $page = $request->query->getInt('page',1);
        $limit = $request->query->getInt('limit',10);
        if(!in_array($order,['ASC', 'DESC'], true)){
            return $this->json(['Incorrect order. Only ASC or DESC are allowed.'],400);
        }
        $comments=$commentRepository->findComments($page,$limit,$object_id,$object_name,$order);
        $data=(new NormalizeService())->normalizeByGroup($comments);

        return new Response($this->json($data),200);
    }

    /**
     * @Route("/comments/{id}", name="comment_show",requirements={"id"="\d+"},methods={"GET"})
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function show(Request $request,$id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $comment = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id);

        $data=(new NormalizeService())->normalizeByGroup($comment);
        return new Response($this->json($data),200);
    }
    /**
     * @Route("/comments/create", name="comment_create",methods={"POST"})
     */
    public function create(Request $request,ValidatorInterface $validator):Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $comment->setText($request->request->get('text'));
        $comment->setDate(new \DateTime());
        $comment->setUserid($user->getId());
        $comment->setUserName($user->getUsername());
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
     * @Route("/comments/update/{id}", name="comment_update",requirements={"id"="\d+"},methods={"PUT"})
     * @IsGranted("ROLE_USER")
     */
    public function update(Request $request, ValidatorInterface $validator,$id):Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $comment = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id);
        $user = $this->getUser();
        if($user instanceof User and $user->getId()!==$comment->getUserId())
        {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }
        $decoded_request=json_decode($request->getContent());

        $comment->setText($decoded_request->text);
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
     * @Route("/comments/delete/{id}", name="comment_delete",requirements={"id"="\d+"},methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request,$id):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id);
        $user = $this->getUser();
        if($user instanceof User and $user->getId()!==$comment->getUserId())
        {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }
        if($comment){
            $entityManager->remove($comment);
            $entityManager->flush();
        }
        return new Response($this->json(['message'=>'Content successfully removed']), 200);
    }
}
