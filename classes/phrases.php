<?php


class phrases
{
    //  badge-danger badge-dark badge-info badge-light badge-primary badge-secondary badge-success badge-warning
    static $badge_color = ['0'=>'info','1'=>'dark','2'=>'warning','3'=>'success','4'=>'danger','5'=>'light'];
    static $stat_name = ['0'=>'جديد','1'=>'قيد الصيانة','2'=>'جاهزة غير مسلمة','3'=>'تم التسليم للزبون','4'=>'بانتظار رد الزبون','5'=>'صيانات دبي','6'=>'معلقة'];
    static $error_sender = ['a rabbit 🐢','a ninja 🐱‍👤','a turtle 🐇' , 'a car 🐎','a horse 🚗','a bus 🚌'
    ,'a ballon 🤖' , 'a firefighter 🔥 👨‍🎤','a police car 🚔 🚓','a detector 🔬 ','a pizza 🎈','a robot 🍕'];
    static $error_header = [404 => 'HTTP/1.0 404 Not Found', 403 => 'HTTP/1.0 403 Forbidden',500 => 'HTTP/1.0 500 Internal Server Error'];
    static $file_format = ['xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    static $price_tags = ['رصيد جوال'=>'mobile_retail','جملة كازيات'=>'mobile_kazia'];
    static $errors = ['0'=>'No Error'];

    static $mobile_robot_stats = ['0'=>'متوقف','1'=>'بانتظار الطلبات','2'=>'وضع التحكم اليدوي','3'=>'تقوم بتنفيذ طلب'
        ,'4'=>'متوقفة يدوياً','5'=>'متوقفة بسبب خطأ','6'=>'متوقفة من الجوال','7'=>'استعلام عن الارصدة','8'=>'بانتظار تمكين الروبوت من قراءة الردود'];



    static $mobile_robot_service_names = ['mtn_cash'=>'MTN كاش','mtn_pos'=>'MTN لاحق الدفع','mtn_pre'=>'MTN مسبق الدفع',
        'syt_cash'=>'Syriatel كاش','syt_pos'=>'Syriatel لاحق الدفع','syt_pre'=>'Syriatel مسبق الدفع',
        'mtn_cash_j'=>'جملة MTN كاش','mtn_pos_j'=>'جملة MTN لاحق الدفع','mtn_pre_j'=>'جملة MTN مسبق الدفع',
        'syt_cash_j'=>'جملة Syriatel كاش','syt_pos_j'=>'جملة Syriatel لاحق الدفع','syt_pre_j'=>'جملة Syriatel مسبق الدفع'];
    static $mobile_robot_functions = ['added job'=>'added_job','confirmed'=>'confirmed','confirm'=>'confirmed','called'=>'called','i have something to say'=>'have_unsent'
    ,'sending'=>'receive_ans','waiting for send confirm'=>'confirm_is_receive'];
}