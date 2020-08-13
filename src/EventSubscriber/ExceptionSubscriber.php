<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $errorMessage = substr($exception->getMessage(), 0, -35);
        $errorSecondMessage = substr($exception->getMessage(), 0, -37);

        if ($errorMessage == 'App\Entity\Shop object not found') {
            $message = 'Resource not found';
        } else if ($errorSecondMessage == 'No route found') {
            $message = $exception->getMessage();
        } else {
            $message = $exception->getMessage();
        }

        $data = [
            'status' => $exception->getStatusCode(),
            'message' => $message
        ];

        $response = new JsonResponse($data);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
           'kernel.exception' => 'onKernelException',
        ];
    }
}