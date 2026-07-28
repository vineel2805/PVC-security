<?php

if (!defined('CATEGORY_IMG_DIR')) {
    define('CATEGORY_IMG_DIR', 'uploads/products/img/');
}

if (!function_exists('clean_img_path')) {
    function clean_img_path($path) {
        $path = trim((string)$path);
        if ($path === '') {
            return '';
        }
        $path = str_replace('../', '', $path);
        $path = ltrim($path, '/');
        return $path;
    }
}

if (!function_exists('is_valid_db_image')) {
    function is_valid_db_image($path) {
        $clean = clean_img_path($path);
        return $clean !== '' && strpos($clean, 'uploads/') === 0;
    }
}

if (!function_exists('normalize_cat_name')) {
    function normalize_cat_name($name) {
        $name = (string)$name;
        $name = strtoupper(trim($name));
        $name = preg_replace('/\s+/', ' ', $name);
        return $name;
    }
}

if (!function_exists('get_category_image_map')) {
    function get_category_image_map() {
        static $map = null;
        if ($map !== null) {
            return $map;
        }

        $map = [];
        $dir = CATEGORY_IMG_DIR;

        if (is_dir($dir)) {
            $files = glob(rtrim($dir, '/') . '/*.{png,PNG,jpg,JPG,jpeg,JPEG,webp,WEBP}', GLOB_BRACE);
            if ($files) {
                foreach ($files as $file) {
                    $baseName = pathinfo($file, PATHINFO_FILENAME);
                    $key = normalize_cat_name($baseName);
                    if ($key !== '') {
                        $map[$key] = $file;
                    }
                }
            }
        }

        return $map;
    }
}

if (!function_exists('find_category_image')) {
    function find_category_image($catName) {
        $map = get_category_image_map();
        if (empty($map)) {
            return '';
        }

        $target = normalize_cat_name($catName);
        if ($target === '') {
            return '';
        }

        if (isset($map[$target])) {
            return $map[$target];
        }

        $best = '';
        $bestLen = 0;
        foreach ($map as $key => $path) {
            if ($key !== '' && strpos($target, $key) === 0 && strlen($key) > $bestLen) {
                $best = $path;
                $bestLen = strlen($key);
            }
        }
        if ($best !== '') {
            return $best;
        }

        foreach ($map as $key => $path) {
            if (strpos($key, $target) === 0 && strlen($target) > $bestLen) {
                $best = $path;
                $bestLen = strlen($target);
            }
        }

        return $best;
    }
}

if (!function_exists('get_default_placeholder_img')) {
    function get_default_placeholder_img() {
        static $img = null;
        if ($img !== null) {
            return $img;
        }

        $img = 'data:image/svg+xml;base64,' . base64_encode('
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300">
  <rect width="300" height="300" fill="#f3f3f3"/>
  <g fill="none" stroke="#c9a14a" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
    <rect x="60" y="105" width="180" height="120" rx="10"/>
    <circle cx="150" cy="165" r="38"/>
    <path d="M115 105 L130 80 H170 L185 105"/>
  </g>
  <text x="150" y="262" font-family="Arial, sans-serif" font-size="16" fill="#a0a0a0" text-anchor="middle">No Image Available</text>
</svg>');

        return $img;
    }
}

if (!function_exists('resolve_product_image')) {
    function resolve_product_image($product) {
        $catImg = find_category_image($product['cat_display'] ?? '');
        if ($catImg !== '') {
            return $catImg;
        }
        if (!empty($product['pimage']) && is_valid_db_image($product['pimage'])) {
            return clean_img_path($product['pimage']);
        }
        return '';
    }
}

if (!function_exists('is_product_out_of_stock')) {
    function is_product_out_of_stock($product) {
        $outOfStockStatuses = ['Inactive', 'Stock-Out', 'Out of Stock'];

        if (in_array($product['status'] ?? '', $outOfStockStatuses, true)) {
            return true;
        }

        if (in_array($product['cat_stock_status'] ?? '', $outOfStockStatuses, true)) {
            return true;
        }

        if (in_array($product['brand_stock_status'] ?? '', $outOfStockStatuses, true)) {
            return true;
        }

        return false;
    }
}
