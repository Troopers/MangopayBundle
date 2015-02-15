<?php

namespace AppVentus\MangopayBundle;

final class AppVentusMangopayEvents
{

    /**
     * The NEW_USER event occurs when a user is created
     */
    const NEW_USER = 'appventus_mangopay.user.new';

    /**
     * The NEW_WALLET event occurs when a wallet is created
     */
    const NEW_WALLET = 'appventus_mangopay.wallet.new';

    /**
     * The NEW_CARD_PREAUTHORISATION event occurs when a card preauthorisation is created
     */
    const NEW_CARD_PREAUTHORISATION = 'appventus_mangopay.card.preauthorisation.new';

    /**
     * The UPDATE_CARD_PREAUTHORISATION event occurs when a card preauthorisation is updated
     */
    const UPDATE_CARD_PREAUTHORISATION = 'appventus_mangopay.card.preauthorisation.update';

    /**
     * The CANCEL_CARD_PREAUTHORISATION event occurs when a card preauthorisation is canceled
     */
    const CANCEL_CARD_PREAUTHORISATION = 'appventus_mangopay.card.preauthorisation.cancel';

    /**
     * The NEW_CARD_REGISTRATION event occurs when a card registration is created
     */
    const NEW_CARD_REGISTRATION = 'appventus_mangopay.card.registration.new';

    /**
     * The UPDATE_CARD_REGISTRATION event occurs when a card registration is updated
     */
    const UPDATE_CARD_REGISTRATION = 'appventus_mangopay.card.registration.update';

    /**
     * The NEW_PAY_IN event occurs when a apyin is created
     */
    const NEW_PAY_IN = 'appventus_mangopay.pay_in.new';

    /**
     * The ERROR_PAY_IN event occurs when a apyin is errored
     */
    const ERROR_PAY_IN = 'appventus_mangopay.pay_in.error';
}
