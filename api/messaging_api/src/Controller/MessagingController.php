<?php

namespace App\Controller;

use App\Entity\Emailing;
use App\Services\EmailingFormManager;
use App\Services\EmailingManager;
use App\Services\SendEmail;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Messaging controller.
 * @Route("/", name="api_")
 */
class MessagingController extends AbstractController
{
    use ControllerTrait;

    private MailerInterface $mailer;
    protected EmailingFormManager $emailingFormManager;
    protected EmailingManager $emailingManager;
    protected SendEmail $sendEmail;

    /**
     * MessagingController constructor.
     */
    public function __construct(
        SendEmail $sendEmail,
        MailerInterface $mailer,
        Security $security,
        EmailingFormManager $emailingFormManager,
        EmailingManager $emailingManager
    ) {
        $this->sendEmail = $sendEmail;
        $this->mailer = $mailer;
        $this->emailingFormManager = $emailingFormManager;
        $this->emailingManager = $emailingManager;
    }

    /**
     * Send email
     * @Rest\Post("/send-mail")
     *
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function sendMail(Request $request): Response
    {
        $emailing = new Emailing();
        $errorsValidation = $this->emailingFormManager->validateForm(
            $request,
            $emailing,
        );
        $filePath = '';

        if ($request->files->get('file')) {
            $uploadedFile = $request->files->get('file');
            $tempFile = $uploadedFile->getPathName();
            $filePath = '/tmp/' . $uploadedFile->getClientOriginalName();
            copy($tempFile, $filePath);
        }

        $errorsValidation = false;
        if (!$errorsValidation) {
            try {
                $this->sendEmail->send([
                    'recipient_email' => $emailing->toUser,
                    'subject' => $request->get('subject'),
                    'content' => $request->get('content'),
                    'attachment' => $filePath,
                ]);

                $emailing->fromUser = $this->sendEmail->senderEmail;
                $this->emailingManager->save($emailing);

                if ($filePath && file_exists($filePath)) {
                    unlink($filePath);
                }

                $response_email = $request->attributes->get('response_email');
                $response_email = trim(stripslashes($response_email), '"');
                $response = $this->handleView(
                    $this->view($response_email, Response::HTTP_CREATED),
                );
            } catch (TransportExceptionInterface $e) {
                if ($_SERVER['APP_ENV'] != 'prod') {
                    throw $e;
                } else {
                    $response = $this->handleView(
                        $this->view(
                            'The email could not be send',
                            Response::HTTP_INTERNAL_SERVER_ERROR,
                        ),
                    );
                }
            }
        } else {
            $response = $this->handleView(
                $this->view($errorsValidation, Response::HTTP_BAD_REQUEST),
            );
        }
        return $response;
    }
}
