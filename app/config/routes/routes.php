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
), array(
    'id' => '\d+')));
$collection->add('comment_update', new Route('/comments/update/{id}', array(
    '_controller' => [CommentsController::class, 'update']
), array(
    'id' => '\d+')));
$collection->add('comment_create', new Route('/comments/create', array(
    '_controller' => [CommentsController::class, 'create']
)));
$collection->add('comment_delete', new Route('/comments/delete/{id}', array(
    '_controller' => [CommentsController::class, 'delete'],
), array(
    'id' => '\d+')));


return $collection;