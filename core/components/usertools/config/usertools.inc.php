<?php

$utConfig = array(

    //TODO изменить
    'ut_signup_page' => 1,
    'ut_signin_page' => 1,
    'ut_profile_page' => 1,
    'ut_profile_settings_page' => 1,
    'ut_forgot_password_page' => 1,
    'ut_reset_password_page' => 1,
    'ut_logout_page' => 1,
    'ut_order_page' => 1,
    'ut_order_history_page' => 1,

    'ut_new_registration_email_tpl' => 'emailNewRegistration',
    'ut_new_registration_email_user_tpl' => 'emailNewRegistrationToUser',
    'ut_activated_email_tpl' => 'emailUserActivated',
    'ut_change_info_email_tpl' => 'emailUserChangedInfo',
    'ut_forgot_password_email_tpl' => 'emailUserForgotPassword',

    //История заказов (шаблоны вывода)
    'ut_order_history_product_tpl' => 'e.cartTableRow.history',
    'ut_order_history_products_outer_tpl' => 'e.cartTable.history',

    //Почта для уведомления о регистрациях пользователей
    'ut_admin_email' => 'info@carole-smokes.ru',

    //Контексты, в которых производится авторизация пользователя front-end
    'ut_auth_contexts' => 'web',
    'ut_login_lifetime' => 28800,

    //Группа и роль, в которые автоматом добавляется пользователь после регистрации через frontend. Если не указывать, то никуда добавляться не будет
    //'ut_user_group' => 4,
    //'ut_user_group_role' => 1, //Member
);
return $utConfig;