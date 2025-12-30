<?php
include '../../config/config.php';
session_start();

header('Content-Type: application/json');

if(isset($_POST['client_id']) && isset($_POST['org_id'])) {
    $clientId = intval($_POST['client_id']);
    $orgId = intval($_POST['org_id']);
    
    // Initialize response
    $response = [
        'success' => false,
        'gst_type' => 'non_gst',
        'tax_type' => 'non_gst',
        'company_country' => '',
        'client_country' => '',
        'company_state' => '',
        'client_state' => '',
        'company_country_id' => 0,
        'client_country_id' => 0,
        'company_state_id' => 0,
        'client_state_id' => 0,
        'message' => ''
    ];
    
    if ($clientId <= 0 || $orgId <= 0) {
        $response['message'] = 'Invalid parameters';
        echo json_encode($response);
        exit();
    }
    
    try {
        // Get company location from company_info table
        $companyQuery = "SELECT ci.*, c.name as country_name, c.id as country_id, 
                        s.name as state_name, s.id as state_id
                        FROM company_info ci 
                        LEFT JOIN countries c ON ci.country_id = c.id 
                        LEFT JOIN states s ON ci.state_id = s.id 
                        WHERE ci.org_id = ? AND ci.is_deleted = 0";
        $stmt = $conn->prepare($companyQuery);
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $companyResult = $stmt->get_result();
        $companyData = $companyResult->fetch_assoc();
        
        if (!$companyData) {
            $response['message'] = 'Company information not found';
            echo json_encode($response);
            exit();
        }
        
        $response['company_country'] = $companyData['country_name'] ?? '';
        $response['company_state'] = $companyData['state_name'] ?? '';
        $response['company_country_id'] = $companyData['country_id'] ?? 0;
        $response['company_state_id'] = $companyData['state_id'] ?? 0;
        $companyCountryId = $companyData['country_id'] ?? 0;
        $companyStateId = $companyData['state_id'] ?? 0;
        
        // Get client billing address from client_address table
        $clientQuery = "SELECT ca.*, c.name as country_name, c.id as country_id,
                       s.name as state_name, s.id as state_id
                       FROM client_address ca 
                       LEFT JOIN countries c ON ca.billing_country = c.id 
                       LEFT JOIN states s ON ca.billing_state = s.id 
                       WHERE ca.client_id = ? AND ca.is_deleted = 0 
                       ORDER BY ca.id DESC LIMIT 1";
        $stmt = $conn->prepare($clientQuery);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $clientResult = $stmt->get_result();
        $clientData = $clientResult->fetch_assoc();
        
        if (!$clientData) {
            $response['message'] = 'Client address information not found';
            $response['success'] = true;
            echo json_encode($response);
            exit();
        }
        
        $response['client_country'] = $clientData['country_name'] ?? '';
        $response['client_state'] = $clientData['state_name'] ?? '';
        $response['client_country_id'] = $clientData['country_id'] ?? 0;
        $response['client_state_id'] = $clientData['state_id'] ?? 0;
        $clientCountryId = $clientData['country_id'] ?? 0;
        $clientStateId = $clientData['state_id'] ?? 0;
        
        // Get India country ID
        $indiaQuery = "SELECT id FROM countries WHERE name = 'India' LIMIT 1";
        $indiaResult = $conn->query($indiaQuery);
        $indiaData = $indiaResult->fetch_assoc();
        $indiaCountryId = $indiaData['id'] ?? 101; // Default if not found
        
        // Determine GST type
        if ($companyCountryId > 0 && $clientCountryId > 0) {
            if ($companyCountryId == $indiaCountryId && $clientCountryId == $indiaCountryId) {
                // Both are in India
                if ($companyStateId > 0 && $clientStateId > 0) {
                    if ($companyStateId == $clientStateId) {
                        // Same state - apply CGST + SGST
                        $response['gst_type'] = 'cgst_sgst';
                        $response['tax_type'] = 'cgst_sgst';
                        $response['message'] = 'Same state within India - CGST + SGST applicable';
                    } else {
                        // Different state - apply IGST
                        $response['gst_type'] = 'igst';
                        $response['tax_type'] = 'igst';
                        $response['message'] = 'Different states within India - IGST applicable';
                    }
                } else {
                    // State information missing
                    $response['gst_type'] = 'igst';
                    $response['tax_type'] = 'igst';
                    $response['message'] = 'State information incomplete - IGST applied by default';
                }
            } 
            elseif ($companyCountryId == $indiaCountryId && $clientCountryId != $indiaCountryId) {
                // Company in India, client outside India - no tax
                $response['gst_type'] = 'non_gst';
                $response['tax_type'] = 'non_gst';
                $response['message'] = 'International client - No tax applicable';
            }
            else {
                // Both outside India or other scenarios - no tax
                $response['gst_type'] = 'non_gst';
                $response['tax_type'] = 'non_gst';
                $response['message'] = 'Non-India transaction - No tax applicable';
            }
        } else {
            // Missing country information
            $response['gst_type'] = 'non_gst';
            $response['tax_type'] = 'non_gst';
            $response['message'] = 'Country information missing - No tax applicable';
        }
        
        $response['success'] = true;
        
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}

// Return default response if no parameters
echo json_encode([
    'success' => false,
    'gst_type' => 'non_gst',
    'tax_type' => 'non_gst',
    'message' => 'No client selected'
]);
?>