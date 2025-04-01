<?php

                    include APPPATH . "vendor/autoload.php"; // Path to autoload.php in your project

                    // // PayPal OAuth endpoint
                    // $url = 'https://api.sandbox.paypal.com/v1/oauth2/token';

                    // // PayPal API endpoint for refunds
                    // $refund_url = 'https://api.sandbox.paypal.com/v1/payments/sale/{sale_id}/refund';

                    $paypal_type = "sandbox";// or live

                    if($paypal_type == 'sandbox'){

                        $url = 'https://api.sandbox.paypal.com/v1/oauth2/token';
                        $refund_url = 'https://api.sandbox.paypal.com/v1/payments/sale/{sale_id}/refund';
                      
                    }elseif($paypal_type == 'production'){

                        $url = 'https://api.paypal.com/v1/oauth2/token';
                        $refund_url = 'https://api.paypal.com/v1/payments/sale/{sale_id}/refund';
                      
                    }

                    // Replace {sale_id} with the actual sale ID you want to refund
                        $sale_id = $payment_sale_id; // add actual amount

                    // PayPal app client ID and secret
                    $client_id = $client_id;
                    $client_secret = $secret_key ;

                    // Request headers for obtaining access token
                    $headers = array(
                        'Accept: application/json',
                        'Accept-Language: en_US'
                    );

                    // Request body for obtaining access token
                    $data = array(
                        'grant_type' => 'client_credentials'
                    );

                    // Initialize cURL session for obtaining access token
                    $ch = curl_init();

                    // Set cURL options for obtaining access token
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_USERPWD, $client_id . ':' . $client_secret);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    // Execute cURL request for obtaining access token
                    $response = curl_exec($ch);

                    // Close cURL session for obtaining access token
                    curl_close($ch);

                    // Decode JSON response for obtaining access token
                    $response_data = json_decode($response, true);

                    // Check if access token was obtained successfully
                    if (isset($response_data['access_token'])) {
                        $access_token = $response_data['access_token'];
                        echo 'Access token: ' . $access_token . "\n";

                        // Request headers for initiating refund
                        $headers_refund = array(
                            'Content-Type: application/json',
                            'Authorization: Bearer ' . $access_token
                        );

                        // Request body for initiating refund
                        $data_refund = array(
                            'amount' => array(
                                'total' => $refund_amount,  // Total amount to refund
                                'currency' => 'EUR'   // Currency code
                            )
                        );

                        // Convert data to JSON format for initiating refund
                        $data_json_refund = json_encode($data_refund);

                        // Initialize cURL session for initiating refund
                        $ch_refund = curl_init();

                        // Set cURL options for initiating refund
                        curl_setopt($ch_refund, CURLOPT_URL, str_replace('{sale_id}', $sale_id, $refund_url));
                        curl_setopt($ch_refund, CURLOPT_HTTPHEADER, $headers_refund);
                        curl_setopt($ch_refund, CURLOPT_POST, true);
                        curl_setopt($ch_refund, CURLOPT_POSTFIELDS, $data_json_refund);
                        curl_setopt($ch_refund, CURLOPT_RETURNTRANSFER, true);

                        // Execute cURL request for initiating refund
                        $response_refund = curl_exec($ch_refund);

                        // Close cURL session for initiating refund
                        curl_close($ch_refund);

                        // Decode JSON response for initiating refund
                        $response_data_refund = json_decode($response_refund, true);

                        // Check if refund was successful
                        if (isset($response_data_refund['id'])) {
                            echo 'Refund successful!' . "\n";
                            echo 'Refund details: ' . "\n";
                            print_r($response_data_refund);
                        } else {
                            // Error handling for initiating refund
                            echo 'Error during refund: ' . $response_data_refund['name'] . ' - ' . $response_data_refund['message'] . "\n";
                        }

                        $up_data123 = array(

                            'paid_to_renter' => 'Y',
                            'paid_amount_to_renter' => $refund_amount
                        ); 
        
                        $this->Allmodeldb->update_record('booking_information', array('booking_id' => $booking_id), $up_data123);

                    } else {
                        // Error handling for obtaining access token
                        echo 'Error occurred while obtaining access token: ' . $response_data['error_description'] . "\n";
                    }

?>
                