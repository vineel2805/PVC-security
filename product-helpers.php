<?php

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

if (!function_exists('pvc_display_title')) {
    /**
     * Converts an ALL-CAPS product name into a readable mixed-case title
     * for display, while preserving model codes / sizes (any token with a
     * digit, e.g. "4G", "180MTR") and short acronyms (<=4 letters, e.g.
     * "COFE", "CCA") exactly as stored. Purely cosmetic — does not touch
     * the underlying data, so search/cart matching is unaffected.
     */
    function pvc_display_title($name) {
        $name = trim((string)$name);
        if ($name === '') {
            return '';
        }
        $words = preg_split('/\s+/', $name);
        $out = [];
        foreach ($words as $w) {
            $hasDigit = (bool) preg_match('/\d/', $w);
            $isShortAcronym = (mb_strlen($w, 'UTF-8') <= 4) && ($w === mb_strtoupper($w, 'UTF-8'));
            if ($hasDigit || $isShortAcronym) {
                $out[] = $w;
            } else {
                $lower = mb_strtolower($w, 'UTF-8');
                $out[] = mb_convert_case($lower, MB_CASE_TITLE, 'UTF-8');
            }
        }
        return implode(' ', $out);
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
