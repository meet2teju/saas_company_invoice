<!-- Meta Tags -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php
$filename = basename($_SERVER['PHP_SELF'], '.php');

$acronyms = ['ui', 'ai', 'js', 'api', 'css', 'html', 'php', 'seo'];

if ($filename === 'index') {
    $title = 'Admin Dashboard';
} else {
    $parts = explode('-', str_replace('ui-', '', strtolower($filename)));

$hasIcon = false;
$hasChart = false;
$cleaned_parts = [];

foreach ($parts as $part) {
    if ($part === 'icon') {
        $hasIcon = true;
        continue;
    }
    if ($part === 'chart') {
        $hasChart = true;
        continue;
    }
    $cleaned_parts[] = $part;
}

$formatted_parts = array_map(function ($word) use ($acronyms) {
    return in_array($word, $acronyms) ? strtoupper($word) : ucfirst($word);
}, $cleaned_parts);

if ($hasIcon) {
    $formatted_parts[] = 'Icons';
}

if ($hasChart) {
    $formatted_parts[] = 'Charts';
}

    $title = implode(' ', $formatted_parts);
}
?>
<title> <?= $title ?> | CRM - Invoice and Billing Management Admin Dashboard Template</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Kanakku is a Sales, Invoices & Accounts Admin template for Accountant or Companies/Offices with various features for all your needs. Try Demo and Buy Now.">
<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
<meta name="author" content="Dreams Technologies">

    