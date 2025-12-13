<?php
// config/currency_helper.php

// Check if functions already exist to prevent redeclaration
if (!function_exists('getCompanyCurrency')) {
    /**
     * Get company currency from database and cache in session
     * @param mysqli $conn Database connection
     * @return array Currency details
     */
    function getCompanyCurrency($conn) {
        // Check if already cached in session
        if (isset($_SESSION['company_currency']) && !empty($_SESSION['company_currency'])) {
            return $_SESSION['company_currency'];
        }
        
        // Fetch from database
        $org_id = $_SESSION['org_id'] ?? 1;
        
        $query = "SELECT c.id, c.currency_name, c.currency_symbol, c.isocode as currency_code 
                  FROM company_info ci 
                  LEFT JOIN currency c ON ci.currency_symbol_id = c.id 
                  WHERE ci.org_id = '$org_id' 
                  LIMIT 1";
        
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $currency = mysqli_fetch_assoc($result);
            
            // Cache in session for future requests
            $_SESSION['company_currency'] = $currency;
            
            return $currency;
        }
        
        // Return default currency if none set
        return getDefaultCurrency();
    }
}

if (!function_exists('getDefaultCurrency')) {
    /**
     * Get default currency fallback
     * @return array Default currency details
     */
    function getDefaultCurrency() {
        return [
            'id' => 1,
            'currency_name' => 'Indian Rupee',
            'currency_symbol' => '₹',
            'currency_code' => 'INR'
        ];
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format amount with company currency symbol
     * @param float $amount Amount to format
     * @param mysqli $conn Database connection
     * @return string Formatted amount with currency symbol
     */
    function formatCurrency($amount, $conn) {
        $currency = getCompanyCurrency($conn);
        return $currency['currency_symbol'] . number_format($amount, 2);
    }
}

if (!function_exists('getCurrencySymbol')) {
    /**
     * Get just the currency symbol
     * @param mysqli $conn Database connection
     * @return string Currency symbol
     */
    function getCurrencySymbol($conn) {
        $currency = getCompanyCurrency($conn);
        return $currency['currency_symbol'];
    }
}

if (!function_exists('getCurrencyName')) {
    /**
     * Get currency name
     * @param mysqli $conn Database connection
     * @return string Currency name
     */
    function getCurrencyName($conn) {
        $currency = getCompanyCurrency($conn);
        return $currency['currency_name'];
    }
}

if (!function_exists('getCurrencyCode')) {
    /**
     * Get currency code (isocode from your table)
     * @param mysqli $conn Database connection
     * @return string Currency code (e.g., INR, USD)
     */
    function getCurrencyCode($conn) {
        $currency = getCompanyCurrency($conn);
        return $currency['currency_code'] ?? 'INR';
    }
}

if (!function_exists('clearCurrencyCache')) {
    /**
     * Clear currency cache - call this after updating company profile
     */
    function clearCurrencyCache() {
        unset($_SESSION['company_currency']);
    }
}

if (!function_exists('getCurrencyDropdownOptions')) {
    /**
     * Get all currencies for dropdown with company currency selected
     * @param mysqli $conn Database connection
     * @return string HTML options for select dropdown
     */
    function getCurrencyDropdownOptions($conn) {
        $companyCurrency = getCompanyCurrency($conn);
        
        $query = "SELECT * FROM currency ORDER BY currency_name";
        $result = mysqli_query($conn, $query);
        
        $options = '<option value="">Select Currency</option>';
        
        while ($row = mysqli_fetch_assoc($result)) {
            $selected = ($row['id'] == $companyCurrency['id']) ? 'selected' : '';
            $options .= "<option value='{$row['id']}' $selected>{$row['currency_name']} ({$row['currency_symbol']})</option>";
        }
        
        return $options;
    }
}

if (!function_exists('getCurrencyById')) {
    /**
     * Get currency by ID
     * @param mysqli $conn Database connection
     * @param int $currencyId Currency ID
     * @return array|null Currency details or null
     */
    function getCurrencyById($conn, $currencyId) {
        $query = "SELECT *, isocode as currency_code FROM currency WHERE id = " . (int)$currencyId;
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        
        return null;
    }
}

if (!function_exists('getCurrencySelect')) {
    /**
     * Get currency dropdown for forms with selected value
     * @param mysqli $conn Database connection
     * @param int $selectedCurrencyId Optional selected currency ID (for edit forms)
     * @return string HTML select options
     */
    function getCurrencySelect($conn, $selectedCurrencyId = null) {
        // If no specific selection, use company currency
        if ($selectedCurrencyId === null) {
            $companyCurrency = getCompanyCurrency($conn);
            $selectedCurrencyId = $companyCurrency['id'];
        }
        
        $query = "SELECT * FROM currency ORDER BY currency_name";
        $result = mysqli_query($conn, $query);
        
        $html = '';
        
        while ($row = mysqli_fetch_assoc($result)) {
            $selected = ($row['id'] == $selectedCurrencyId) ? 'selected' : '';
            $html .= "<option value='{$row['id']}' $selected>{$row['currency_name']} ({$row['currency_symbol']})</option>";
        }
        
        return $html;
    }
}
?>