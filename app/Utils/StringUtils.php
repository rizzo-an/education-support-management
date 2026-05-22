<?php

namespace App\Utils;

class StringUtils {

    public static function is_valid_text(?string $value, int $max_length): bool {
        if ($value === null) {
            return false;
        }

        $trimmed = trim($value);

        return $trimmed !== '' && mb_strlen($trimmed) <= $max_length;
    }

    public static function is_valid_email(?string $value): bool {
        if ($value === null) {
            return false;
        }

        return filter_var(trim($value), FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function is_valid_phone(?string $value): bool {
        if ($value === null) {
            return false;
        }

        $trimmed = preg_replace('/\s+/', '', trim($value));

        return $trimmed !== '' && mb_strlen($trimmed) <= 32;
    }
}