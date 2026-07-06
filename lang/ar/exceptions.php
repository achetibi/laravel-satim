<?php

declare(strict_types=1);

return [
    'connection_failed' => 'تعذّر الاتصال ببوابة الدفع.',
    'malformed_response' => 'تم استلام استجابة غير صالحة من البوابة.',
    'json_encode_failed' => 'تعذّر ترميز بيانات الطلب بصيغة JSON.',
    'http_error' => 'استجابت البوابة برمز الحالة HTTP :status (:reason).',
    'config' => [
        'missing' => 'مفتاح الإعداد «:key» غير موجود.',
        'invalid_environment' => 'البيئة «:value» غير صالحة.',
        'missing_base_url' => 'لا يوجد رابط أساسي مُعدّ للبيئة «:env».',
        'invalid_value' => 'القيمة «:value» غير صالحة لمفتاح الإعداد «:key».',
    ],
    'validation' => [
        'failed' => 'فشل التحقق من صحة الطلب.',
    ],
    'gateway' => [
        '1' => 'رقم الطلب مستخدَم بالفعل.',
        '5' => 'تم رفض الوصول (بيانات اعتماد غير صحيحة).',
        'unknown' => 'حدث خطأ غير متوقع.',
    ],
];
