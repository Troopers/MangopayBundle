[![Troopers](https://cloud.githubusercontent.com/assets/618536/18787530/83cf424e-81a3-11e6-8f66-cde3ec5fa82a.png)](http://troopers.agency)

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/troopers-MangopayBundle/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)
[![License](https://img.shields.io/packagist/l/troopers/mangopay-bundle.svg)](https://packagist.org/packages/troopers/mangopay-bundle)
[![Version](https://img.shields.io/packagist/v/troopers/mangopay-bundle.svg)](https://packagist.org/packages/troopers/mangopay-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4896b24c-74ee-4506-8c4c-842a9c660b66/mini.png)](https://insight.sensiolabs.com/projects/4896b24c-74ee-4506-8c4c-842a9c660b66)
=============

MangopayBundle
===

This bundle provides integration of the official [SDK PHP for Mangopay api V2](https://github.com/Mangopay/mangopay2-php-sdk) into Symfony.

This branch does support the [v2.01 API version](https://docs.mangopay.com/endpoints/v2.01).
The v1 branch does support the [v2 API version](https://docs.mangopay.com/endpoints/v2).


Configuration
---

```yaml
troopers_mangopay:
    client_id: your_mangopay_client_id
    client_password: your_mangopay_client_password
    base_url: your_mangopay_base_url
```

How to use it ?
---

The official sdk provides a "MangoMapApi" class which is a shortcut to all the "tools" like "ApiPayIns", "ApiWallets", "ApiUsers"...
You can access those "tools" through the service "troopers_mangopay.mango_api".

```php
    $payIn = new PayIn();
    $this->get('troopers_mangopay.mango_api')->PayIns->create($payIn);
```

Additionnaly, there is some helpers that handle most of the mangopay actions. fell free to fork and implement yours

BankInformationHelper
---
It can register user BankInformations as it implements BankInformationInterface

```php
    $bankInformation = new BankInformation();
    $this->get('troopers_mangopay.bank_information_helper')->createBankAccount($bankInformation);
```

PaymentHelper
---
It can register a CardPreauthorisation and execute it

```php
    $cardRegistration = new CardRegistration();
    $this->get('troopers_mangopay.payment_helper')->createPreAuthorisation($cardRegistration);

    $cardPreAuthorisation = new CardPreAuthorisation();
    $this->get('troopers_mangopay.payment_helper')->executePreAuthorisation($cardPreAuthorisation, $user, $wallet);
```

PaymentDirectHelper
---
It can create a new direct payment

```php
    $transaction = new Transaction();
    $this->get('troopers_mangopay.payment_direct_helper')->createDirectTransaction($transaction);
```

UserHelper
---
It can create a new user in mangopay as the User object implements the UserInterface

```php
    $user = new User();
    $this->get('troopers_mangopay.user_helper')->createMangoUser($user);
```

WalletHelper
---
It can create a user wallet

```php
    $user = new User();
    $this->get('troopers_mangopay.wallet_helper')->createWalletForUser($user);
```

This is the general workflow for the mangopay payment page:

1) Displaying the payment form to user

![Step 1](https://raw.githubusercontent.com/Troopers/MangopayBundle/master/Resources/doc/assets/step1.jpg)

2) Create mangopay user and the card registration through mangopay API

![Step 2](https://raw.githubusercontent.com/Troopers/MangopayBundle/master/Resources/doc/assets/step2.jpg)

3) Call the tokenisation server to validate the user credit card, use 3d secure if needed, update the CardR
egistration with tokenized Card, create the PreAuthorisation then redirect the user to success page.
![Step 3](https://raw.githubusercontent.com/Troopers/MangopayBundle/master/Resources/doc/assets/step3.jpg)

