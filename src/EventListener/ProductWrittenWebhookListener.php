<?php

namespace App\EventListener;

use App\Entity\RegisteredProduct;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Shopware\App\SDK\Context\Webhook\WebhookAction;
use Shopware\App\SDK\HttpClient\ClientFactory;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'webhook.product.written')]
class ProductWrittenWebhookListener
{
    public function __construct(
        private readonly ClientFactory $clientFactory,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager
    ) { }

    public function __invoke(WebhookAction $action): void
    {
        $productId = $action->payload[0]['primaryKey'];

        $registeredProduct = $this->getRegisteredProduct($productId);
        if ($registeredProduct instanceof RegisteredProduct) {
            return;
        }

        $registeredProduct = $this->registerProduct($productId);

        $client = $this->clientFactory->createSimpleClient($action->shop);

        $response = $client->get(sprintf('%s/api/product/%s', $action->shop->getShopUrl() . '/public', $productId));

        if (!$response->ok()) {
            $this->logger->error($response->getContent());
            $this->removeRegisteredProduct($registeredProduct);
            return;
        }

        $productDescription = $response->json()['data']['description'];

        $updateResponse = $client->patch(sprintf('%s/api/product/%s', $action->shop->getShopUrl() . '/public', $productId), [
            'description' => $productDescription . ' Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
        ]);

        if (!$updateResponse->ok()) {
            $this->logger->error($updateResponse->getContent());
            $this->removeRegisteredProduct($registeredProduct);
        }
    }

    private function getRegisteredProduct(string $productId): ?RegisteredProduct
    {
        return $this->entityManager->createQueryBuilder()
            ->select(['registeredProduct'])
            ->from(RegisteredProduct::class, 'registeredProduct')
            ->where('registeredProduct.productId = :productId')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function registerProduct(string $productId): RegisteredProduct
    {
        $registeredProduct = new RegisteredProduct();
        $registeredProduct->setProductId($productId);
        $this->entityManager->persist($registeredProduct);
        $this->entityManager->flush();

        return $registeredProduct;
    }

    private function removeRegisteredProduct(RegisteredProduct $registeredProduct): void
    {
        $this->entityManager->remove($registeredProduct);
        $this->entityManager->flush();
    }
}