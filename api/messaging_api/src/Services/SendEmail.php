<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Class SendEmail
 * @package App\Services
 */
class SendEmail
{
    private MailerInterface $mailer;

    public string $senderEmail;
    private RequestStack $requestStack;

    private $fromEmail;
    private $fromName;
    /**
     * SendEmail constructor.
     */
    public function __construct(
        MailerInterface $mailer,
        RequestStack $requestStack,
        string $senderEmail,
        string $fromEmail,
        string $fromName
    ) {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->requestStack = $requestStack;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    /** @param array<mixed> $arguments */
    public function send(array $arguments): void
    {
        $request = $this->requestStack->getCurrentRequest();
        [
            'recipient_email' => $recipientEmail,
            'subject' => $subject,
            'content' => $content,
            'attachment' => $attachment,
        ] = $arguments;

        $recipientEmail = explode(',', $recipientEmail);
        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to(...$recipientEmail)
            ->subject($subject)
            ->html($content);

        if (!empty($attachment) && file_exists($attachment)) {
            $email->attachFromPath($attachment);
        }
        $bccRecipients = array_key_exists('APP_BCC_RECIPIENTS', $_ENV) ? $_ENV['APP_BCC_RECIPIENTS'] : '';
        if ($bccRecipients) {
            $email->bcc($bccRecipients);
        }
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $mailerException) {
            $request->attributes->set('response_email', 'error');
            throw $mailerException;
        }
        $request->attributes->set('response_email', 'success');
    }
}
