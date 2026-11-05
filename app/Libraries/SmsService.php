<?php

namespace App\Libraries;

class SmsService
{
    protected string $username;
    protected string $password;
    protected string $senderId;
    protected string $deptSecureKey;

    protected string $smsUrl =
        'https://msdgweb.mgov.gov.in/esms/sendsmsrequestDLT';

    public function __construct()
    {
        $this->username = (string) env('MMAPUA_USERNAME');
        $this->password = (string) env('MMAPUA_PASSWORD');
        $this->senderId = (string) env('SENDERID');
        $this->deptSecureKey = (string) env('DEPT_KEY');
    }

    /**
     * Send OTP SMS
     */
    public function sendOtp(string $mobile, string $otp): bool
    {
        $message = "Dear Sir/Madam, Your OTP for "
            . "Parivar Utthan Survey Portal is {$otp}. "
            . "Department of Social Justice and Empowerment, Haryana";

        $templateId = (string) env('TEMP_ID1');

        return $this->sendSingleSMS(
            $message,
            $mobile,
            $templateId
        );
    }

    /**
     * Send SMS
     */
    public function sendSingleSMS(
        string $message,
        string $mobile,
        string $templateId
    ): bool {

        if (
            empty($this->username) ||
            empty($this->password) ||
            empty($this->senderId) ||
            empty($this->deptSecureKey) ||
            empty($templateId)
        ) {
            log_message('error', 'SMS configuration missing.');
            return false;
        }

        if (!preg_match('/^[0-9]{10}$/', $mobile)) {
            log_message('error', 'Invalid mobile number.');
            return false;
        }

        $message = trim($message);

        $key = hash(
            'sha512',
            trim($this->username) .
            trim($this->senderId) .
            $message .
            trim($this->deptSecureKey)
        );

        $data = [
            'username'       => trim($this->username),
            'password'       => trim($this->password),
            'senderid'       => trim($this->senderId),
            'content'        => $message,
            'smsservicetype' => 'otpmsg',
            'mobileno'       => trim($mobile),
            'key'            => $key,
            'templateid'     => trim($templateId)
        ];

        $response = $this->post_to_url(
            $this->smsUrl,
            $data
        );

        log_message(
            'info',
            'SMS Gateway Response: ' . (string) $response
        );
        // print_r($response);
        // die;
        if ($response === false || trim((string) $response) === '') {
            return false;
        }

        // Check actual gateway response after confirming its format.
        return $this->isSuccessfulResponse($response);
    }

    /**
     * Send POST request using cURL
     */
   protected function post_to_url(string $url, array $data)
    {
        $post = curl_init();

        curl_setopt_array($post, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
        ]);

        $result = curl_exec($post);

        if ($result === false) {
            log_message('error', 'SMS cURL Error: ' . curl_error($post));
            curl_close($post);
            return false;
        }

        $httpCode = curl_getinfo($post, CURLINFO_HTTP_CODE);

        curl_close($post);

        log_message('info', 'SMS HTTP Status: ' . $httpCode);

        return $result;
    }

    /**
     * Gateway response validation
     */
   protected function isSuccessfulResponse(string $response): bool
    {
        $response = trim($response);

        if (
            preg_match(
                '/^402,\s*MsgID\s*=\s*([a-zA-Z0-9]+)/i',
                $response,
                $matches
            )
        ) {
            log_message(
                'info',
                'SMS Gateway Message ID: ' . $matches[1]
            );

            return true;
        }

        log_message(
            'error',
            'SMS Gateway rejected or returned an unknown response: '
            . $response
        );

        return false;
    }
}
