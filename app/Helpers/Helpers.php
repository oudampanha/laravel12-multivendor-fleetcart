<?php

use Illuminate\Support\Str;

if (!function_exists('create_slug')) {
  /**
   * description
   *
   * @param string $str
   * @return string lowercase
   */
  function create_slug($string)
  {
    $t = $string;
    $specChars = array(
      ' ' => '-',
      '!' => '',
      '"' => '',
      '#' => '',
      '$' => '',
      '%' => '',
      '&' => 'and',
      '\'' => '',
      '(' => '',
      ')' => '',
      '*' => '',
      '+' => '',
      ',' => '',
      '₹' => '',
      '.' => '',
      '/-' => '',
      ':' => '',
      ';' => '',
      '<' => '',
      '=' => '',
      '>' => '',
      '?' => '',
      '@' => '',
      '[' => '',
      '\\' => '',
      ']' => '',
      '^' => '',
      '_' => '',
      '`' => '',
      '{' => '',
      '|' => '',
      '}' => '',
      '~' => '',
      '-----' => '-',
      '----' => '-',
      '---' => '-',
      '/' => '',
      '--' => '-',
      '/_' => '-',
    );
    foreach ($specChars as $k => $v) {
      $t = str_replace($k, $v, $t);
    }
    return Str::lower($t);
  }
}

// if (!function_exists('setting')) {
//   function setting($key = false, $defaultValue = false)
//   {
//     $setting = app('Setting');
//     if ($key === false) {
//       return $setting;
//     }

//     $value = $setting->get($key);

//     return $value ? $value : $defaultValue;
//   }
// }

if (!function_exists('assetUrl')) {
  function assetUrl()
  {
    $host = $_SERVER['HTTP_HOST'] ?? null;
    $config = request()->getScheme() . "://" . $host;
    // $config .= '/public/';
    $config .= '/';   //use for localhost:8000 or 127.0.0.1:8000
    return $config;
  }
}

if (!function_exists('uploadUrl')) {
  function uploadUrl()
  {
    return asset('public/uploads/');
  }
}

if (!function_exists('errorImageUrl')) {
  function errorImageUrl()
  {
    // return asset('public/images/avatar3.png');
    return asset('/images/avatar3.png'); //for using localhost:8000 or 127.0.0.1:8000
  }
}

// //check trans('Key') is set or not create a key for it
// if (!function_exists('checkTrans')) {
//   function checkTrans($key = null, $replace = [], $locale = null)
//   {
//     if (is_null($key)) {
//       return app('translator');
//     } else {
//       $translation = app('translator')->get($key, $replace, $locale);
//       if ($translation === $key) {
//         // If the translation is not found, you can log it or handle it as needed
//         // For example, you can log it to a file or database
//         // Log::warning("Translation key '{$key}' not found.");
//       }
//       return $translation;
//     }
//   }
// }

// if (!function_exists('trans')) {
//   function trans($key = null, $replace = [], $locale = null)
//   {
//     if (is_null($key)) {
//       return app('translator');
//     }

//     return app('translator')->get($key, $replace, $locale);
//   }
// }

// if (!function_exists('__')) {
//   function __($key = null, $replace = [], $locale = null)
//   {
//     return trans($key, $replace, $locale);
//   }
// }

// if (!function_exists('setting')) {
//   function setting($key, $default = null)
//   {
//     return \App\Models\BusinessSetting::get($key, $default);
//   }
// }
