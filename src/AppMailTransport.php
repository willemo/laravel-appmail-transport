<?php

namespace Willemo\LaravelAppMailTransport;

use Illuminate\Mail\Transport\Transport;
use GuzzleHttp\ClientInterface;
use Swift_Mime_Message;

class AppMailTransport extends Transport
{
    /**
     * Guzzle client instance
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * AppMail API key
     *
     * @var string
     */
    protected $key;

    /**
     * Create a new AppMail transport instance
     *
     * @param \GuzzleHttp\ClientInterface  $client
     * @param string  $key
     * @return void
     */
    public function __construct(ClientInterface $client, $key)
    {
        $this->client = $client;
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $options = [
            'headers' => [
                'x-server-api-key' => $this->key,
            ],
            'json' => [
                'mail_from' => $this->formatAddress($message->getFrom()),
                'rcpt_to' => $this->getRcptToAddresses($message),
                'data' => base64_encode($message->toString()),
            ],
        ];

        return $this->client->post('https://api.appmail.io/api/v1/send/raw', $options);
    }

    /**
     * Formats an address to RFC2882 format
     *
     * @param  string|array $address
     * @return string
     */
    protected function formatAddress($address, $name = null)
    {
        if (is_array($address)) {
            $name = current($address);
            $address = key($address);
        }

        return !empty($name) ? $name . " <$address>" : $address;
    }

    /**
     * Gt the "rcpt_to" payload field for the API request
     *
     * @param \Swift_Mime_Message  $message
     * @return array
     */
    protected function getRcptToAddresses(Swift_Mime_Message $message)
    {
        $rcptTo = array_merge(
            (array) $message->getTo(),
            (array) $message->getCc(),
            (array) $message->getBcc()
        );

        $formatted = [];

        foreach ($rcptTo as $address => $name) {
            $formatted[] = $this->formatAddress($address, $name);
        }

        return $formatted;
    }

    /**
     * Get the API key being used by the transport
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the API key being used by the transport
     *
     * @param string $apiKey
     */
    public function setKey($key)
    {
        return $this->key = $key;
    }
}
