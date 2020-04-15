 <?php

// Routes
$app->get('/uninstall', 'App\Controller\HomeController:uninstallAction')->setName('uninstall');
$app->get('/auth', 'App\Controller\HomeController:authAction')->setName('auth');
$app->get('/entry', 'App\Controller\HomeController:entryAction')->setName('entry');
$app->get('/failed', 'App\Controller\HomeController:failedAction')->setName('failed');
$app->get('/start_recurring_charge', 'App\Controller\HomeController:startRecurringChargeAction')->setName('startRecurringCharge');
$app->get('/start_one_time_charge', 'App\Controller\HomeController:startOneTimeChargeAction')->setName('startOneTimeCharge');
$app->get('/payment_success', 'App\Controller\HomeController:paymentSuccessAction')->setName('paymentSuccess');
$app->get('/payment_failed', 'App\Controller\HomeController:paymentFailedAction')->setName('paymentFailed');
