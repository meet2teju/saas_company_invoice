<?php

function check_is_access_new($mname, $utype = "") {
    global $conn;

    if ($utype == "" && isset($_SESSION['role_id'])) {
        $utype = $_SESSION['role_id'];
    }

    $sel = "SELECT is_access FROM crm_user_access WHERE role_id='$utype' AND mtitle='$mname' AND is_access = 1";
    $qry = mysqli_query($conn, $sel);

    if (mysqli_num_rows($qry) > 0) {
        return 1;
    }

    return 0;
}

function numberToWords($number) {
    // Convert to integer for array access
    $number = intval(floatval($number));
    
    $words = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
    ];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    // Safety check - ensure number is within array bounds
    if ($number < 0 || $number >= 10000000) {
        return "Number out of range";
    }

    if ($number < 20) {
        return isset($words[$number]) ? $words[$number] : '';
    }
    
    if ($number < 100) {
        $tensDigit = intval($number / 10);
        $onesDigit = $number % 10;
        return (isset($tens[$tensDigit]) ? $tens[$tensDigit] : '') . 
               ($onesDigit > 0 ? ' ' . (isset($words[$onesDigit]) ? $words[$onesDigit] : '') : '');
    }
    
    if ($number < 1000) {
        $hundreds = intval($number / 100);
        $remainder = $number % 100;
        return (isset($words[$hundreds]) ? $words[$hundreds] : '') . 
               ' Hundred' . ($remainder != 0 ? ' ' . numberToWords($remainder) : '');
    }
    
    if ($number < 100000) {
        $thousands = intval($number / 1000);
        $remainder = $number % 1000;
        return numberToWords($thousands) . ' Thousand' . ($remainder != 0 ? ' ' . numberToWords($remainder) : '');
    }
    
    if ($number < 10000000) {
        $lakhs = intval($number / 100000);
        $remainder = $number % 100000;
        return numberToWords($lakhs) . ' Lakh' . ($remainder != 0 ? ' ' . numberToWords($remainder) : '');
    }

    return "Number too large";
}

?>