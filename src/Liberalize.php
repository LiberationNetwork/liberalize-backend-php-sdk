<?php

namespace Liberalize;
use \Exception;

class LiberalizeBackend
{
    private $paymentApi;
    private $customerApi;
    private $privateKey;
    /*
    public scope to make that property/method available from anywhere, other classes and instances of the object.
    private scope when you want your property/method to be visible in its own class only.
    protected scope when you want to make your property/method visible in all classes that extend current class including the parent class.
    */

    function __construct($privateKey, $environment = "production")
    {
        switch ($environment) {
            case 'production':
                $this->paymentApi = "https://payment.api.liberalize.io/payments";
                $this->customerApi = "https://customer.api.liberalize.io";
                break;
            case 'staging':
                $this->paymentApi = "https://payment.api.staging.liberalize.io/payments";
                $this->customerApi = "https://customer.api.staging.liberalize.io";
                break;
            default:
                $this->paymentApi = "https://payment.api.liberalize.io/payments";
                $this->customerApi = "https://customer.api.liberalize.io";
                break;
        }
        $this->privateKey = base64_encode($privateKey . ":");
    }

    public function createPayment($requestBody, $libService="elements")
    {
        try {
            $validatedRequest = [];
            foreach($requestBody as $key => $value) {
                switch ($key) {
                    case 'amount':
                        $target_amount = (int)$value;
                        $validatedRequest["amount"] = $target_amount;
                        break;
                    case 'source':
                        if (substr($value, 0, 4 ) === "card") {
                            $validatedRequest["source"] = "lib:customer:paymentMethods/$value";
                        } else {
                            $validatedRequest["source"] = "$value";
                        }
                        break;
                    default:
                        $validatedRequest[$key] = $value;
                    break;
                }
            }
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $this->paymentApi);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_HEADER, 1);
            curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            // curl_setopt($curlHandle, CURLOPT_VERBOSE, 1);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            $headers = array(
                "Content-type: application/json",
                "Authorization: Basic $this->privateKey",
                "x-lib-pos-type: $libService"
            );
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($validatedRequest));
            $result = curl_exec($curlHandle);
            $header_size = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            // $header = substr($result, 0, $header_size);
            $body = substr($result, $header_size);
            // $info = curl_getinfo($curlHandle);
            // echo "INFO:";
            // print_r($info);
            $body = json_decode($body,true);
            curl_close($curlHandle);
            return $body;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function authorizePayment($requestBody, $libService="elements")
    {
        try {
            $validatedRequest = [];
            $paymentId = "";
            foreach($requestBody as $key => $value) {
                switch ($key) {
                    case 'source':
                        if (substr($value, 0, 4 ) === "card") {
                            $validatedRequest["source"] = "lib:customer:paymentMethods/$value";
                        } else {
                            $validatedRequest["source"] = "$value";
                        }
                        break;
                    case 'paymentId':
                        $paymentId = $value;
                        break;
                    default:
                        $validatedRequest[$key] = $value;
                    break;
                }
            }
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, "$this->paymentApi/$paymentId/authorizations");
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_HEADER, 1);
            curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            // curl_setopt($curlHandle, CURLOPT_VERBOSE, 1);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            $headers = array(
                "Content-type: application/json",
                "Authorization: Basic $this->privateKey",
                "x-lib-pos-type: $libService"
            );
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($validatedRequest));
            $result = curl_exec($curlHandle);
            $header_size = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            // $header = substr($result, 0, $header_size);
            $body = substr($result, $header_size);
            // $info = curl_getinfo($curlHandle);
            // echo "INFO:";
            // print_r($info);
            $body = json_decode($body,true);
            curl_close($curlHandle);
            return $body;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function capturePayment($requestBody, $libService="elements")
    {
        try {
            $validatedRequest = [];
            $paymentId = "";
            foreach($requestBody as $key => $value) {
                switch ($key) {
                    case 'amount':
                        $target_amount = (int)$value;
                        $validatedRequest["amount"] = $target_amount;
                        break;
                    case 'paymentId':
                        $paymentId = $value;
                        break;
                    default:
                        $validatedRequest[$key] = $value;
                    break;
                }
            }
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, "$this->paymentApi/$paymentId/captures");
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_HEADER, 1);
            curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            // curl_setopt($curlHandle, CURLOPT_VERBOSE, 1);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            $headers = array(
                "Content-type: application/json",
                "Authorization: Basic $this->privateKey",
                "x-lib-pos-type: $libService"
            );
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($validatedRequest));
            $result = curl_exec($curlHandle);
            $header_size = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            // $header = substr($result, 0, $header_size);
            $body = substr($result, $header_size);
            // $info = curl_getinfo($curlHandle);
            // echo "INFO:";
            // print_r($info);
            $body = json_decode($body,true);
            curl_close($curlHandle);
            return $body;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function refundPayment($requestBody, $libService="elements")
    {
        try {
            $validatedRequest = [];
            $paymentId = "";
            foreach($requestBody as $key => $value) {
                switch ($key) {
                    case 'amount':
                        $target_amount = (int)$value;
                        $validatedRequest["amount"] = $target_amount;
                        break;
                    case 'paymentId':
                        $paymentId = $value;
                        break;
                    default:
                        $validatedRequest[$key] = $value;
                    break;
                }
            }
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, "$this->paymentApi/$paymentId/refunds");
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_HEADER, 1);
            curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            // curl_setopt($curlHandle, CURLOPT_VERBOSE, 1);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            $headers = array(
                "Content-type: application/json",
                "Authorization: Basic $this->privateKey",
                "x-lib-pos-type: $libService"
            );
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($validatedRequest));
            $result = curl_exec($curlHandle);
            $header_size = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            // $header = substr($result, 0, $header_size);
            $body = substr($result, $header_size);
            // $info = curl_getinfo($curlHandle);
            // echo "INFO:";
            // print_r($info);
            $body = json_decode($body,true);
            curl_close($curlHandle);
            return $body;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function voidPayment($requestBody, $libService="elements")
    {
        try {
            $validatedRequest = [];
            $paymentId = "";
            foreach($requestBody as $key => $value) {
                switch ($key) {
                    case 'paymentId':
                        $paymentId = $value;
                        break;
                    default:
                        $validatedRequest[$key] = $value;
                    break;
                }
            }
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, "$this->paymentApi/$paymentId/voids");
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_HEADER, 1);
            curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            // curl_setopt($curlHandle, CURLOPT_VERBOSE, 1);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            $headers = array(
                "Content-type: application/json",
                "Authorization: Basic $this->privateKey",
                "x-lib-pos-type: $libService"
            );
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($validatedRequest));
            $result = curl_exec($curlHandle);
            $header_size = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            // $header = substr($result, 0, $header_size);
            $body = substr($result, $header_size);
            // $info = curl_getinfo($curlHandle);
            // echo "INFO:";
            // print_r($info);
            $body = json_decode($body,true);
            curl_close($curlHandle);
            return $body;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getPayment($paymentId, $libService="elements")
    {
        try {
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, "$this->paymentApi/$paymentId");
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_HEADER, 1);
            curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curlHandle, CURLOPT_HTTPGET, 1);
            // curl_setopt($curlHandle, CURLOPT_VERBOSE, 1);
            $headers = array(
                "Content-type: application/json",
                "Authorization: Basic $this->privateKey",
                "x-lib-pos-type: $libService"
            );
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($curlHandle);
            $header_size = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            // $header = substr($result, 0, $header_size);
            $body = substr($result, $header_size);
            // $info = curl_getinfo($curlHandle);
            // echo "INFO:";
            // print_r($info);
            $body = json_decode($body,true);
            curl_close($curlHandle);
            return $body;
        } catch (Exception $e) {
            return $e;
        }
    }
}