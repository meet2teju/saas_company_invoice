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
    $words = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
    ];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    if ($number < 20) return $words[$number];
    if ($number < 100) return $tens[intval($number / 10)] . ' ' . $words[$number % 10];
    if ($number < 1000)
        return $words[intval($number / 100)] . ' Hundred ' . ($number % 100 != 0 ? numberToWords($number % 100) : '');
    if ($number < 100000)
        return numberToWords(intval($number / 1000)) . ' Thousand ' . ($number % 1000 != 0 ? numberToWords($number % 1000) : '');
    if ($number < 1000000)
        return numberToWords(intval($number / 100000)) . ' Lakh ' . ($number % 100000 != 0 ? numberToWords($number % 100000) : '');

    return "Number too large";
}
 ?>