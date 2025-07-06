<?php

return [
    /**
     * API Token Key (string)
     * Accepted value:
     * Live Token: https://myfatoorah.readme.io/docs/live-token
     * Test Token: https://myfatoorah.readme.io/docs/test-token
     */
    'api_key' => 'u2OF9oYklvs8Dfx-63H_CdUAbnNRtUR0sBb_X46-v-sZjOqAni6Ztpw0_zeY2MdfkuebE7E6zWtN1Y8k-8_TGINHzz_oUzrPj2cHaCZ1DgYdBp9QVW4ziFfLDe7QpL6S67FfSCi-STRzKvUmxh0mfPOf1nlWhte477nuttTiPfOrqmVJEcB0VS7sNrvBU6k_jK0z0KcArW6vEZGRxXvJIshNdWJfyRBPHc75o8eZApS6b-gawhr_4KOTyMeaA3VNdu-DT3K1VPWySTErfgdbMZXS1WS5Xmsk3chca-zlg16fNlmlZgAFdfb2iq_o4YdeuFYcq3tSrAm2ZfkMocY9HsIBdTlparsd18nkM7vgikt-hp8zr4Rd_ymMdn_j3-QeOQX-bd6_WVkEG_-72iogZNzE6MO-BTExvQ6QI6Kb_6qTwgsU_vnfvV6dG0__uclbaicIuTxCoHIIwt3oR28awkrpjC1S7WBbGrGLsqDQvlc_SgkMDNF4bxh6ygGyN7NHIB2Oj1YN7ouaBy3JofzDlonjN0Z-Rd8J8ugCc5pD7ratAm6DGyIYPnojjHA7P1DEqBHyuCvccdnub7DPBom6kqi1ZkAcwgkG_TI8gDHGIECGgFAb6r7UViFrzQ04qXgeGM8tkVhcTn0XnlKMwFZUWXk0jdlUTD33UugMNiM95Dm0b_v9cx_n7yAuCQUvX_cdRL6xRYuoQ_IE9CgcVQHWQSqZNvk',
    /**
     * Test Mode (boolean)
     * Accepted value: true for the test mode or false for the live mode
     */
    'test_mode' => true,
    /**
     * Country ISO Code (string)
     * Accepted value: KWT, SAU, ARE, QAT, BHR, OMN, JOD, or EGY.
     */
    'country_iso' => 'KWT',
    /**
     * Save card (boolean)
     * Accepted value: true if you want to enable save card options.
     * You should contact your account manager to enable this feature in your MyFatoorah account as well.
     */
    'save_card' => true,
    /**
     * Webhook secret key (string)
     * Enable webhook on your MyFatoorah account setting then paste the secret key here.
     * The webhook link is: https://{example.com}/myfatoorah/webhook
     */
    'webhook_secret_key' => '',
    /**
     * Register Apple Pay (boolean)
     * Set it to true to show the Apple Pay on the checkout page.
     * First, verify your domain with Apple Pay before you set it to true.
     * You can either follow the steps here: https://docs.myfatoorah.com/docs/apple-pay#verify-your-domain-with-apple-pay or contact the MyFatoorah support team (tech@myfatoorah.com).
    */
    'register_apple_pay' => false
];
