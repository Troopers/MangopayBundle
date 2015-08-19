MangopayBundle
===

[![Join the chat at https://gitter.im/AppVentus/MangopayBundle](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/AppVentus/MangopayBundle?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

This bundle is an implementation of the official mangopay php api for Symfony.


Configuration
---

```yaml
app_ventus_mangopay:
    client_id: your_mangopay_client_id
    client_password: your_mangopay_client_password
    base_url: your_mangopay_base_url
```

How to use it ?
---

The official sdk provides a "MangoMapApi" class which is a shortcut to all the "tools" like "ApiPayIns", "ApiWallets", "ApiUsers"...
You can access those "tools" througt the service "appventus_mangopay.mango_api".

```php
    $payIn = new PayIn();
    $this->get('appventus_mangopay.mango_api')->PayIns->create($payIn);
```

Additionnaly, there is some helpers that handle most of the mangopay actions. fell free to fork and implement yours

BankInformationHelper
---
Can register user BankInformations as it implements BankInformationInterface

```php
    $bankInformation = new BankInformation();
    $this->get('appventus_mangopay.bank_information_helper')->createBankAccount($bankInformation);
```

PaymentHelper
---
Can register a CardPreauthorisation and execute it 

```php
    $cardRegistration = new CardRegistration();
    $this->get('appventus_mangopay.payment_helper')->createPreAuthorisation($cardRegistration);
    
    $cardPreAuthorisation = new CardPreAuthorisation();
    $this->get('appventus_mangopay.payment_helper')->executePreAuthorisation($cardPreAuthorisation, $user, $wallet);
```

PaymentDirectHelper
---
Can create a new direct payment 

```php
    $transaction = new Transaction();
    $this->get('appventus_mangopay.payment_direct_helper')->createDirectTransaction($transaction);
```

UserHelper
---
Can create a new user in mangopay as the User object implements the UserInterface

```php
    $user = new User();
    $this->get('appventus_mangopay.user_helper')->createMangoUser($user);
```

WalletHelper
---
Can create a user wallet

```php
    $user = new User();
    $this->get('appventus_mangopay.wallet_helper')->createWalletForUser($user);
```

This is the general workflow for the mangopay payment page:

1) Displaying the patment form to user

![Step 1](https://raw.githubusercontent.com/AppVentus/MangopayBundle/master/Resources/doc/assets/step1.jpg)

2) Create mangopay user and the card registration through mangopay API

![Step 2](https://raw.githubusercontent.com/AppVentus/MangopayBundle/master/Resources/doc/assets/step2.jpg)

3) Call the tokenisation server to validate the user credit card, use 3d secure if needed, update the CardR
egistration with tokenized Card, create the PreAuthorisation then redirect the user to success page.
![Step 3](https://raw.githubusercontent.com/AppVentus/MangopayBundle/master/Resources/doc/assets/step3.jpg)

