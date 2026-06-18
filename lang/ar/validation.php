<?php

return [

    'min' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من أو تساوي :min.',
        'array'   => 'يجب أن يحتوي :attribute على :min عنصراً على الأقل.',
        'string'  => 'يجب أن يكون طول :attribute :min حرفاً على الأقل.',
        'file'    => 'يجب أن يكون حجم :message :min كيلوبايت على الأقل.',
    ],

    'max' => [
        'numeric' => 'يجب ألا تتجاوز قيمة :attribute :max.',
        'array'   => 'يجب ألا يحتوي :attribute على أكثر من :max عنصراً.',
        'string'  => 'يجب ألا يتجاوز طول :attribute :max حرفاً.',
        'file'    => 'يجب ألا يتجاوز حجم :attribute :max كيلوبايت.',
    ],

    'numeric'     => 'يجب أن تكون قيمة :attribute رقماً.',
    'integer'     => 'يجب أن تكون قيمة :attribute عدداً صحيحاً.',
    'between'     => ['numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.'],
    'gte'         => ['numeric' => 'يجب أن تكون قيمة :attribute أكبر من أو تساوي :value.'],
    'lte'         => ['numeric' => 'يجب أن تكون قيمة :attribute أصغر من أو تساوي :value.'],
    'after'       => 'يجب أن يكون :attribute تاريخاً بعد :date.',
    'after_or_equal' => 'يجب أن يكون :attribute مساوياً أو بعد :date.',
    'before'      => 'يجب أن يكون :attribute تاريخاً قبل :date.',
    'before_or_equal' => 'يجب أن يكون :attribute مساوياً أو قبل :date.',
    'date'        => ':attribute ليس تاريخاً صالحاً.',

];
