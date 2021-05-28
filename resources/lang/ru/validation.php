<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Validation Language Lines
  |--------------------------------------------------------------------------
  |
  | The following language lines contain the default error messages used by
  | the validator class. Some of these rules have multiple versions such
  | as the size rules. Feel free to tweak each of these messages here.
  |
  */

  'accepted'             => 'Поле :attribute должно быть принято.',
  'active_url'           => ':attribute не является действительной ссылкой.',
  'after'                => ':attribute должно быть датой после :date.',
  'alpha'                => ':attribute может содержать только буквы.',
  'alpha_dash'           => ':attribute может содержать только буквы, цифры и чёрточку.',
  'alpha_num'            => ':attribute может содержать только буквы и цифры.',
  'array'                => ':attribute должен быть массивом.',
  'before'               => ':attribute должно быть датой до :date.',
  'between'              => [
    'numeric' => ':attribute должен быть между :min и :max.',
    'file'    => ':attribute должен быть между :min и :max килобайт.',
    'string'  => ':attribute должен быть между :min и :max символов.',
    'array'   => ':attribute должен иметь между :min и :max элементов.',
  ],
  'boolean'              => ':attribute должен быть да или нет.',
  'confirmed'            => ':attribute подтверждение не соответствует.',
  'date'                 => ':attribute не является действительной датой.',
  'date_format'          => ':attribute не соответствует формату :format.',
  'different'            => ':attribute и :other должны быть разными.',
  'digits'               => ':attribute должно иметь :digits цифр.',
  'digits_between'       => ':attribute должен иметь между :min и :max цифр.',
  'distinct'             => ':attribute поле имеет одинаковые элементы.',
  'email'                => ':attribute должен быть действительной почтой.',
  'exists'               => 'выбранный :attribute недействительный.',
  'filled'               => ':attribute поле обязательно.',
  'image'                => ':attribute должно быть изображением.',
  'in'                   => 'выбранный :attribute недействительный.',
  'in_array'             => ':attribute не присустствует в :other.',
  'integer'              => ':attribute должен быть числом.',
  'ip'                   => ':attribute должен быть действительным IP адресом.',
  'json'                 => ':attribute должен быть действительным JSON-ом.',
  'max'                  => [
    'numeric' => ':attribute не может превышать :max.',
    'file'    => ':attribute не может быть больше чем :max килобайт.',
    'string'  => ':attribute не может быть больше чем :max символов.',
    'array'   => ':attribute не может иметь больше чем :max элементов.',
  ],
  'mimes'                => ':attribute должен быть файлом типа: :values.',
  'min'                  => [
    'numeric' => ':attribute должен быть больше чеи :min.',
    'file'    => ':attribute должен быть больше чем :min килобайт.',
    'string'  => ':attribute должен быть больше чем :min символов.',
    'array'   => ':attribute должен иметь больше чем :min элементов.',
  ],
  'not_in'               => 'выбранный :attribute недействительный.',
  'numeric'              => ':attribute должен быть числом.',
  'present'              => ':attribute должно присутствовать.',
  'regex'                => ':attribute формат недействительный.',
  'required'             => 'Поле :attribute обязательно.',
  'required_if'          => ':attribute обязательно, если :other равен :value.',
  'required_unless'      => ':attribute обязательно за исключением, когда :other отсутствует в :values.',
  'required_with'        => ':attribute обязателен, когда :values присутствует.',
  'required_with_all'    => ':attribute обязателен, когда :values присуствуют.',
  'required_without'     => ':attribute обязателен, когда :values отсутствуются.',
  'required_without_all' => ':attribute обязателен, когда ни один из :values не имеется.',
  'same'                 => ':attribute и :other должны совпадать.',
  'size'                 => [
    'numeric' => ':attribute должен быть :size размера.',
    'file'    => ':attribute должен быть :size килобайт.',
    'string'  => ':attribute должен иметь :size символов.',
    'array'   => ':attribute должен иметь :size элементов.',
  ],
  'string'               => ':attribute должно быть строкой.',
  'timezone'             => ':attribute должно быть действительной зоной.',
  'unique'               => ':attribute уже используется.',
  'url'                  => ':attribute формать недействителен.',

  /*
  |--------------------------------------------------------------------------
  | Custom Validation Language Lines
  |--------------------------------------------------------------------------
  |
  | Here you may specify custom validation messages for attributes using the
  | convention "attribute.rule" to name the lines. This makes it quick to
  | specify a specific custom language line for a given attribute rule.
  |
  */

  'custom' => [
    'attribute-name' => [
      'rule-name' => 'custom-message',
    ],
    'password' => [
      'password-rule' => 'Пароль должен содержать хотя бы одну заглавную букву'
    ]
  ],

  /*
  |--------------------------------------------------------------------------
  | Custom Validation Attributes
  |--------------------------------------------------------------------------
  |
  | The following language lines are used to swap attribute place-holders
  | with something more reader friendly such as E-Mail Address instead
  | of "email". This simply helps us make messages a little cleaner.
  |
  */

  'attributes' => [],

];