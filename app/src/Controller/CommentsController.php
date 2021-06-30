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
    public function index(Request $request): Response
    {

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CommentsController.php',
        ]);
    }

    public function store(Request $request,ValidatorInterface $validator):Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $comment = new Comment();
        $comment->setText($request->text);
        $comment->setDate(date('d/m/Y h:i:s a', time()));
        $comment->setUserid($request->user_id);
        $comment->setObjectId($request->object_id);
        $comment->setObjectName($request->object_name);

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
            ]),200);
        }

    }

    public function update(Request $request):Response
    {
        $this->validate($request, [
            'text' => 'required|max:1000'
        ]);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CommentsController.php',
        ]);
    }

    public function destroy(Request $request):Response
    {

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CommentsController.php',
        ]);
    }
}
