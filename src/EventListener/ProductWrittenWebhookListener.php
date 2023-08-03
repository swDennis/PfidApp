<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Shopware\App\SDK\Context\Webhook\WebhookAction;
use Shopware\App\SDK\HttpClient\ClientFactory;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'webhook.product.written')]
class ProductWrittenWebhookListener
{
    public function __construct(private readonly ClientFactory $clientFactory, private readonly LoggerInterface $logger)
    {

    }

    public function __invoke(WebhookAction $action): void
    {

        $productId = $action->payload[0]['primaryKey'];

        $client = $this->clientFactory->createSimpleClient($action->shop);

        $response = $client->get(sprintf('%s/api/product/%s', $action->shop->getShopUrl() . '/public', $productId),
            [
                'ids' => [$productId],
            ]
        );

        if (!$response->ok()) {
            $this->logger->error($response->getContent());

            return;
        }

        $productDescription = $response->json()['data']['description'];
        // TODO: REMOVE AFTER DEBUG
        $errorLogFile = __DIR__ . '/error.log';
        \file_put_contents($errorLogFile, \var_export($productDescription, true) . PHP_EOL, FILE_APPEND);
        // TODO: REMOVE AFTER DEBUG
        // Currently we have a endless loop
//        $updateResponse = $client->patch(sprintf('%s/api/product/%s', $action->shop->getShopUrl() . '/public', $productId), [
//            'description' => $productDescription . ' Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
//        ]);
//
//        if (!$updateResponse->ok()) {
//            $this->logger->error($updateResponse->getContent());
//        }
    }
}