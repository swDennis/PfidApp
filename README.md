vendor/shopware/app-php-sdk/src/HttpClient/AuthenticatedClient.php anpassen mit public

# Infos for ShopwareApp and AppServer development

First you have to install the Shopware CLI with
> curl -1sLf \
'https://dl.cloudsmith.io/public/friendsofshopware/stable/setup.deb.sh' \
| sudo -E bash
sudo apt install shopware-cli

then 
> shopware-cli project config init

Now you can update every time update the ShopwareApp `manifest.xml` with the command
> shopware-cli project extension upload YOUR_PROJECT/release --activate --increase-version

Create a new file: `config/packages/shopware_app.yaml` with the content: 
```yml
shopware_app:
  shop_class: App\Entity\Shop
  name: '%env(APP_NAME)%'
  secret: TestSecret
  #  secret: '%env(APP_SECRET)%'
``` 

Then create a new file `config/routes/shopware_app.yaml` with the content:
```yaml
shopware_app_routes_lifecycle:
   resource: '@ShopwareAppBundle/Resources/config/routing/lifecycle.xml'

shopware_app_routes_webhook:
   resource: '@ShopwareAppBundle/Resources/config/routing/webhook.xml'
```
Register the Shopware app bundle in file `config/bundles.php`
```php
<?php

return [
    ...
    \Shopware\AppBundle\ShopwareAppBundle::class => ['all' => true],
    ...
];

```


## Events
### Entity event whitelist
> src/Core/Framework/Webhook/Hookable/HookableEventCollector::HOOKABLE_ENTITIES

### HookableEvents
Hookable events implements the src/Core/Framework/Webhook/Hookable.php interface.
The event name is in the file. 

# Improvements Core
 - Maybe add a new configuraton section for the manifest.xml of the ShopwareApp, to register entities which needs the entity events instead of the static const in the `HookableEventCollector`

# Doku issues
 - It is required to save the shop data for this u need the following symfony bundles:
 - AppServer requires
   - composer require make
   - composer require migrations
 - Then you have to create a Shop entity wich extends from Shopware\AppBundle\Entity\AbstractShop and use the annotation #[ORM\Entity]

```PHP
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shopware\AppBundle\Entity\AbstractShop;

#[ORM\Entity]
class Shop extends AbstractShop
{

}
```
 - After this you have to execute
   - > php bin/console make:migration
   - > php bin/console doctrine:migrations:migrate
 - The doku for the ShopwareApp and for the AppServer should tread the same topic. So you can understand what's happened.  
 - https://beta-developer.shopware.com/docs/guides/plugins/apps/webhook.html Who we are here? ShopwareApp or AppServer
 - make it possible to copy the Path of files
 - in navigation: 
   - "App Starter Guide" - https://beta-developer.shopware.com/docs/guides/plugins/apps/starter/product-translator.html#creating-the-manifest   
   - AND inside the same hierachy: "App Base Guide"  https://beta-developer.shopware.com/docs/guides/plugins/apps/app-base-guide.html?
     also make a clearer seperation from the app-server and the shop-app
