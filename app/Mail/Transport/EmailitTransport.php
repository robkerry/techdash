<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailitTransport extends AbstractTransport
{
    /**
     * Create a new Emailit transport instance.
     */
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiKey,
    ) {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (! $email instanceof Email) {
            return;
        }

        // Get the from address
        $fromAddress = $email->getFrom()[0] ?? null;
        
        if (! $fromAddress) {
            throw new \RuntimeException('From address is required.');
        }

        // Build the payload according to Emailit API
        $payload = [
            'from' => $this->formatAddress($fromAddress),
            'to' => $this->formatAddress($email->getTo()[0] ?? null),
            'reply_to' => $fromAddress->getAddress(), // Always set reply_to to the from email address
            'subject' => $email->getSubject() ?? '',
            'html' => $email->getHtmlBody() ?? '',
            'text' => $email->getTextBody() ?? '',
        ];

        // Add attachments if present
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $body = $attachment->getBody();
            if (is_resource($body)) {
                $content = stream_get_contents($body);
            } else {
                $content = (string) $body;
            }

            $attachments[] = [
                'filename' => $attachment->getFilename(),
                'content' => base64_encode($content),
                'content_type' => $attachment->getContentType(),
            ];
        }

        if (count($attachments) > 0) {
            $payload['attachments'] = $attachments;
        }

        // Add custom headers if present
        $headers = [];
        foreach ($email->getHeaders()->all() as $header) {
            $name = $header->getName();
            // Skip standard headers that are already handled
            if (! in_array(strtolower($name), ['from', 'to', 'subject', 'reply-to', 'content-type'])) {
                $headers[$name] = $header->getBodyAsString();
            }
        }

        if (count($headers) > 0) {
            $payload['headers'] = $headers;
        }

        // Send the request
        $response = $this->client->request('POST', 'https://api.emailit.com/v1/emails', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        // Check for errors
        if ($response->getStatusCode() >= 400) {
            $error = json_decode($response->getContent(false), true);
            throw new \RuntimeException(
                'Emailit API error: '.($error['message'] ?? $response->getContent(false))
            );
        }
    }

    /**
     * Format an address for Emailit API.
     */
    private function formatAddress(?Address $address): string
    {
        if (! $address) {
            return '';
        }

        $name = $address->getName();

        if ($name) {
            return sprintf('%s <%s>', $name, $address->getAddress());
        }

        return $address->getAddress();
    }

    /**
     * Get the string representation of the transport.
     */
    public function __toString(): string
    {
        return 'emailit';
    }
}
