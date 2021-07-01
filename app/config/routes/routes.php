<?php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use App\Controller\CommentsController;

$collection = new RouteCollection();
$collection->add('comments', new Route('/comments', array(
    '_controller' => [CommentsController::class, 'list']
)));
$collection->add('comment_show', new Route('/comments/{id}', array(
    '_controller' => [CommentsController::class, 'show']
)));
$collection->add('comment_update', new Route('/comments/{id}/update', array(
    '_controller' => [CommentsController::class, 'update']
)));
$collection->add('comment_create', new Route('/comments/create', array(
    '_controller' => [CommentsController::class, 'create']
)));
$collection->add('comment_delete', new Route('/comments/{id}/delete}', array(
    '_controller' => [CommentsController::class, 'delete']
)));


return $collection;