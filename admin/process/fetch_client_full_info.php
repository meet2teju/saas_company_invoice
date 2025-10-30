<?php
include '../../config/config.php';

$response = [
    'billing_html' => '<p class="text-danger">No billing data found.</p>',
    'shipping_html' => '<p class="text-danger">No shipping data found.</p>',
];

if (isset($_POST['client_id'])) {
    $client_id = intval($_POST['client_id']);

    // --- Billing info from client_address ---
    $query = "SELECT 
        ca.billing_name,
        ca.billing_address1,
        ca.billing_address2,
        ca.billing_pincode,
        b_country.name AS billing_country,
        b_state.name AS billing_state,
        b_city.name AS billing_city,
        c.phone_number,
        c.email,
        c.pan_number
    FROM client_address ca
    LEFT JOIN client c ON ca.client_id = c.id
    LEFT JOIN countries b_country ON ca.billing_country = b_country.id
    LEFT JOIN states b_state ON ca.billing_state = b_state.id
    LEFT JOIN cities b_city ON ca.billing_city = b_city.id
    WHERE ca.client_id = $client_id
    LIMIT 1";

    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $response['billing_html'] = '
            <h6 class="fs-14 fw-semibold mb-1">' . htmlspecialchars($row['billing_name']) . '</h6>
            <p class="mb-1 fs-13">Phone : ' . htmlspecialchars($row['phone_number']) . '</p>
            <p class="mb-1 fs-13">Email : ' . htmlspecialchars($row['email']) . '</p>
            <p class="text-dark fs-13">PAN : ' . htmlspecialchars($row['pan_number']) . '</p>
            <p class="mb-1 fs-13">' .
                htmlspecialchars($row['billing_address1']) . ' ' . htmlspecialchars($row['billing_address2']) . ', ' .
                htmlspecialchars($row['billing_city']) . ', ' . htmlspecialchars($row['billing_state']) . ', ' . htmlspecialchars($row['billing_country']) . ' - ' .
                htmlspecialchars($row['billing_pincode']) . '
            </p>
        ';
    }

    // --- Shipping info from company_info ---
    $companyRes = mysqli_query($conn, "SELECT 
        name, email, mobile_number, pan_number, 
        address, country_id, state_id, city_id, zipcode 
        FROM company_info 
        LIMIT 1"
    );

    if ($company = mysqli_fetch_assoc($companyRes)) {
        // Fetch country/state/city names
        $country = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM countries WHERE id=" . (int)$company['country_id']));
        $state   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM states WHERE id=" . (int)$company['state_id']));
        $city    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM cities WHERE id=" . (int)$company['city_id']));

        $response['shipping_html'] = '
            <h6 class="fs-14 fw-semibold mb-1">' . htmlspecialchars($company['name']) . '</h6>
            <p class="mb-1 fs-13">Phone : ' . htmlspecialchars($company['mobile_number']) . '</p>
            <p class="mb-1 fs-13">Email : ' . htmlspecialchars($company['email']) . '</p>
            <p class="text-dark fs-13">PAN : ' . htmlspecialchars($company['pan_number']) . '</p>
            <p class="mb-1 fs-13">' .
                htmlspecialchars($company['address']) . ', ' .
                htmlspecialchars($city['name'] ?? '') . ', ' . htmlspecialchars($state['name'] ?? '') . ', ' . htmlspecialchars($country['name'] ?? '') . ' - ' .
                htmlspecialchars($company['zipcode']) . '
            </p>
        ';
    }
}

echo json_encode($response);
