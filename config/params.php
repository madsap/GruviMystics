<?php

return [
    'adminEmail' => 'admin@gruvimystics.com',
    'supportEmail' => 'info@gruvimystics.com',
    'stripe' => ['publishableKey' => 'pk_live_u1sC1LBlHOIp5nBGf31ffC0l', 'secretKey' => 'sk_live_sVTviGmXNrvFdhm4xi5ReuP5'],
    //'stripe' => ['publishableKey' => 'pk_test_6pRNASCoBOKtIshFeQd4XMUh', 'secretKey' => 'sk_test_BQokikJOvBiI2HlWgH4olfQ2'],
    'twilio' => [
        'accountSid' => 'AC0808c0df342a7f9a1422012c59fa07f6', 
        'authToken' => '4090470bff37d20752f8e2430db0810d', 
        'appSid' => 'AP46e55455f26ae83b57ee715bb9d1d492',
        'apiKey' => 'SK3da90a685078f915ee2671da474de31e',
        'apiKeySecret' => 'VaJtqkpdbYoSVCwl8dq4PgN7S8OIzxFG',
        'appSid' => 'AP46e55455f26ae83b57ee715bb9d1d492',
        'chatSid' => 'IS45409219ff49429096a9eae3a1a2da43',
        'androidToken' => 'CR5d80daef37f458c5828a95d815a7ac3f',
        'iosToken' => 'CR81d057b15aa5103640be7412b8623e69'//'CRe8f437d2a5dfe966b9711875dfe38834'
        ],
    /*'twilio' => [
        'accountSid' => 'AC0808c0df342a7f9a1422012c59fa07f6', 
        'authToken' => '4090470bff37d20752f8e2430db0810d', 
        'appSid' => 'AP46e55455f26ae83b57ee715bb9d1d492',
        'apiKey' => 'SK28a19da82ea82b21bbebfab00c55c8bc',
        'apiKeySecret' => 'eO8aNsvDLBLShmI6brFWKb0gZk54rt8J',
        'chatSid' => 'IS45409219ff49429096a9eae3a1a2da43',
        'androidToken' => '',
        'iosToken' => 'CRe8f437d2a5dfe966b9711875dfe38834'
        ],*/
    'user.passwordResetTokenExpire' => 3600,
    'paypal' => [
        'shipping' => 0.00,
        'tax' => 0.00,
        'currency' => 'USD',
        'environment' => 'production',
        'sandbox' => [
            'ClientID' => 'AUbxZMuRb20OlQIyPq-fk5KJu1SIwgWKqZ722VTyqMY0Lx-zb5C_2wBFsfdhNY4iMO0VCgcdHaFKovB2',
            'secret' => 'EA4HKsX-hlhZD6I3hVPNEIwdqzc_f44BhdMVWC6UDQccvhXPC2gQfvqSuX2GATyMcteUR9BkiRTKwyxN',
        ],
        'production' => [
            'ClientID' => 'ARYvD1rPEAASrixBVB3fh8pM5SXko5XDlwtkZK4hp8XuSL5UGt8QQR78G-wwr6Y734XHa4kuj2o1Hc1k',
            'secret' => 'EE2ZmKNQNKZk_ZWISpzWSbcUcVXgCB_EOhH6Unjz6DLK36LFXw5Ujbv6lRWPQQzUI8MQSlvpG7BB5iU4'
        ]
        
    ]
    /*'paypal' => [
        'environment' => 'sandbox',//'production',
        'sandbox' => [
            'ClientID' => 'AaUP3TBcg2IqFx0EctepynJ7wjDZfc2QxYx5qbWljKbdZcmEuaSXWQ3DJEPaSLYmvSdn4xaySlUAjuci',
            'secret' => 'EN9RusMQT2FW3IWQWUD7wscwTxJhWzaa5asBX8lQ9A_2oCXlfPNST6dOMJvfLuEYtpWy4lYmgabErnyo',
        ],
        'production' => [
            'ClientID' => 'ARYvD1rPEAASrixBVB3fh8pM5SXko5XDlwtkZK4hp8XuSL5UGt8QQR78G-wwr6Y734XHa4kuj2o1Hc1k',
            'secret' => 'EE2ZmKNQNKZk_ZWISpzWSbcUcVXgCB_EOhH6Unjz6DLK36LFXw5Ujbv6lRWPQQzUI8MQSlvpG7BB5iU4'
        ]
        
    ]*/
];
