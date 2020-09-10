<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;

class DevTestController extends Controller
{

    public function index() {

        $gateway = Omnipay::create('Elavon_Converge')->initialize([
            'merchantId' => '2118425',
            'username' => 'web',
            'password' => 'MYC9Q8PZF0FJAFRFE5OK06D6XUS1XL36PPMIT2CCU4SGYFBNLGQXITTKS4BNTT64',
            'testMode' => false
        ]);
        
        $card = new CreditCard(array(
            'firstName'             => 'Sean',
            'lastName'              => 'Won',
            'number'                => '5409260477092064',
            'expiryMonth'           => '05',
            'expiryYear'            => '2022',
            'cvv'                   => '688',
            'billingAddress1'       => '1 Scrubby Creek Road',
            'billingCountry'        => 'AU',
            'billingCity'           => 'Scrubby Creek',
            'billingPostcode'       => '4999',
            'billingState'          => 'QLD',
        ));
        
        
        try {
            
             $transaction = $gateway->purchase(array(
                'amount'                => '0.50',
                'currency'              => 'USD',
                'description'           => 'This is a test purchase transaction.',
                'card'                  => $card,
                /*'ssl_invoice_number'    => 1,
                'ssl_show_form'         => 'false',
                'ssl_result_format'     => 'ASCII',*/
             ));
             
             $response = $transaction->send();
             $data = $response->getData();
             
             //echo "Gateway purchase response data == " . print_r($data, true) . "\n";
        
             if ($response->isSuccessful()) {
                 
                 //echo "Purchase transaction was successful!\n";
                 return response()->json(['success' => true, 'message' => 'Purchase transaction was successful!', 'response_data' => $data]);
                 
             } else {
                 
                 return response()->json(['success' => false, 'message' => 'Purchase transaction was Unsuccessful!', 'response_data' => $data]);
                 
             }
             
        } catch (\Exception $e) {
            
             //echo "Exception caught while attempting purchase.\n";
             //echo "Exception type == " . get_class($e) . "\n";
             //echo "Message == " . $e->getMessage() . "\n";
             
             return response()->json(['success' => false, 'message' => 'Exception caught while attempting purchase. Error Message: ' . $e->getMessage(), 'exception_type' => get_class($e)]);
        }

        //return response()->json(['success' => true, 'message' => $gateway->getMerchantId()]);

        //return strval($gateway);

    }


}