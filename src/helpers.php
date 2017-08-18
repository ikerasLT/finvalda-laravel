<?php

/**
 * @param string|int $needle
 * @param array $haystack
 *
 * @return mixed
 */
function arr_find($needle, $haystack) {
    if (array_key_exists($needle, $haystack)) {
        return $haystack[$needle];
    }

    foreach ($haystack as $item) {
        if (is_array($item)) {
            $result = arr_find($needle, $item);

            if ($result !== false) {
                return $result;
            }
        }
    }

    return false;
}