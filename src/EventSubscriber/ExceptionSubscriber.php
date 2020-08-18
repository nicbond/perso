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

        $message = $exception->getMessage();
        $errorMessage = substr($exception->getMessage(), 0, -35);
        $errorSecondMessage = substr($exception->getMessage(), 0, -38);

        $findme   = 'Invalid data';

        $pos = strpos($message, $findme);

        if ($errorMessage == 'App\Entity\Shop object not found') {
            $message = 'Resource not found';
            $status = 404;
        } else if ($errorSecondMessage == 'No route found') {
            $status = 404;
        } else if ($pos !== false) {
            $status = 400;
        } else {
            $status = 500;
        }

        $data = [
            'status' => $status,
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