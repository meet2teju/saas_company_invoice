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
        // Build billing HTML dynamically - only show non-empty fields
        $billing_parts = [];
        
        // Name
        if (!empty($row['billing_name'])) {
            $billing_parts[] = '<h6 class="fs-14 fw-semibold mb-1">' . htmlspecialchars($row['billing_name']) . '</h6>';
        }
        
        // Phone
        if (!empty($row['phone_number'])) {
            $billing_parts[] = '<p class="mb-1 fs-13"><span class="text-dark">Phone:</span> ' . htmlspecialchars($row['phone_number']) . '</p>';
        }
        
        // Email
        if (!empty($row['email'])) {
            $billing_parts[] = '<p class="mb-1 fs-13"><span class="text-dark">Email:</span> ' . htmlspecialchars($row['email']) . '</p>';
        }
        
        // PAN
        if (!empty($row['pan_number'])) {
            $billing_parts[] = '<p class="mb-1 fs-13"><span class="text-dark">PAN:</span> ' . htmlspecialchars($row['pan_number']) . '</p>';
        }
        
        // Address - build address parts with labels only if they exist
        $has_address = false;
        $address_html = '';
        
        if (!empty($row['billing_address1'])) {
            $address_html .= '<p class="mb-1 fs-13"><span class="text-dark">Address:</span> ' . htmlspecialchars($row['billing_address1']);
            $has_address = true;
            
            if (!empty($row['billing_address2'])) {
                $address_html .= ', ' . htmlspecialchars($row['billing_address2']);
            }
            $address_html .= '</p>';
        }
        
        // City, State, Country, Pincode
        $location_parts = [];
        if (!empty($row['billing_city'])) $location_parts[] = htmlspecialchars($row['billing_city']);
        if (!empty($row['billing_state'])) $location_parts[] = htmlspecialchars($row['billing_state']);
        if (!empty($row['billing_country'])) $location_parts[] = htmlspecialchars($row['billing_country']);
        
        if (!empty($location_parts)) {
            $address_html .= '<p class="mb-1 fs-13"><span class="text-dark">Location:</span> ' . implode(', ', $location_parts);
            if (!empty($row['billing_pincode'])) {
                $address_html .= ' - ' . htmlspecialchars($row['billing_pincode']);
            }
            $address_html .= '</p>';
            $has_address = true;
        } elseif (!empty($row['billing_pincode'])) {
            $address_html .= '<p class="mb-1 fs-13"><span class="text-dark">Pincode:</span> ' . htmlspecialchars($row['billing_pincode']) . '</p>';
            $has_address = true;
        }
        
        if ($has_address) {
            $billing_parts[] = $address_html;
        }
        
        // If no data found, show message
        if (empty($billing_parts)) {
            $response['billing_html'] = '<p class="text-muted">No billing information available</p>';
        } else {
            $response['billing_html'] = implode('', $billing_parts);
        }
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

        // Build shipping HTML dynamically - only show non-empty fields
        $shipping_parts = [];
        
        // Company Name
        if (!empty($company['name'])) {
            $shipping_parts[] = '<h6 class="fs-14 fw-semibold mb-1">' . htmlspecialchars($company['name']) . '</h6>';
        }
        
        // Phone
        if (!empty($company['mobile_number'])) {
            $shipping_parts[] = '<p class="mb-1 fs-13"><span class="text-dark">Phone:</span> ' . htmlspecialchars($company['mobile_number']) . '</p>';
        }
        
        // Email
        if (!empty($company['email'])) {
            $shipping_parts[] = '<p class="mb-1 fs-13"><span class="text-dark">Email:</span> ' . htmlspecialchars($company['email']) . '</p>';
        }
        
        // PAN
        if (!empty($company['pan_number'])) {
            $shipping_parts[] = '<p class="mb-1 fs-13"><span class="text-dark">PAN:</span> ' . htmlspecialchars($company['pan_number']) . '</p>';
        }
        
        // Address - build address parts with labels only if they exist
        $has_address = false;
        $address_html = '';
        
        if (!empty($company['address'])) {
            $address_html .= '<p class="mb-1 fs-13"><span class="text-dark">Address:</span> ' . htmlspecialchars($company['address']) . '</p>';
            $has_address = true;
        }
        
        // City, State, Country, Zipcode
        $location_parts = [];
        if (!empty($city['name'])) $location_parts[] = htmlspecialchars($city['name']);
        if (!empty($state['name'])) $location_parts[] = htmlspecialchars($state['name']);
        if (!empty($country['name'])) $location_parts[] = htmlspecialchars($country['name']);
        
        if (!empty($location_parts)) {
            $address_html .= '<p class="mb-1 fs-13"><span class="text-dark">Location:</span> ' . implode(', ', $location_parts);
            if (!empty($company['zipcode'])) {
                $address_html .= ' - ' . htmlspecialchars($company['zipcode']);
            }
            $address_html .= '</p>';
            $has_address = true;
        } elseif (!empty($company['zipcode'])) {
            $address_html .= '<p class="mb-1 fs-13"><span class="text-dark">Zipcode:</span> ' . htmlspecialchars($company['zipcode']) . '</p>';
            $has_address = true;
        }
        
        if ($has_address) {
            $shipping_parts[] = $address_html;
        }
        
        // If no data found, show message
        if (empty($shipping_parts)) {
            $response['shipping_html'] = '<p class="text-muted">No company information available</p>';
        } else {
            $response['shipping_html'] = implode('', $shipping_parts);
        }
    }
}

echo json_encode($response);