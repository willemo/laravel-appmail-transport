<?php

namespace Willemo\LaravelAppMail;

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
    protected $apiKey;

    /**
     * AppMail API host
     *
     * @var string
     */
    protected $host;

    /**
     * AppMail API version
     *
     * @var string
     */
    protected $version;

    /**
     * Create a new AppMail transport instance
     *
     * @param \GuzzleHttp\ClientInterface  $client
     * @param string  $apiKey
     * @param string  $host
     * @param string  $version
     * @return void
     */
    public function __construct(ClientInterface $client, $apiKey, $host, $version)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->host = $host;
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $options = [
            'headers' => [
                'x-server-api-key' => $this->apiKey,
            ],
            'json' => [
                'mail_from' => $this->formatAddresses($message->getFrom()),
                'rcpt_to' => $this->getRcptToAddresses($message),
                'data' => base64_encode($message->toString()),
            ],
        ];

        return $this->client->post($this->getUrl(), $options);
    }

    /**
     * Formats an address to RFC2882 format
     *
     * @param  string|array $address
     * @return string
     */
    protected function formatAddresses($address)
    {
        $name = null;

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

        return array_map([$this, 'formatAddresses'], $rcptTo);
    }

    /**
     * Get the URL to post the API request to
     *
     * @return string
     */
    protected function getUrl()
    {
        return sprintf('https://%s/api/%s/send/raw', $this->host, $this->version);
    }

    /**
     * Get the API key being used by the transport
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set the API key being used by the transport
     *
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        return $this->apiKey = $apiKey;
    }

    /**
     * Get the host being used by the transport
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the host being used by the transport
     *
     * @param string $host
     */
    public function setHost($host)
    {
        return $this->host = $host;
    }

    /**
     * Get the version being used by the transport
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the version being used by the transport
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        return $this->version = $version;
    }
}
