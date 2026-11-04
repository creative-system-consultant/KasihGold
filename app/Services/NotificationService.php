<?php

namespace App\Services;

use App\Models\Wablas;
use App\Notifications\LelonganEmailNotification;

class NotificationService
{
    public function sendEmailNotification($cust, $lelongan)
    {
        $message = $this->buildMessage($cust, $lelongan, 'email');
        $cust->notify(new LelonganEmailNotification($message, $cust, $lelongan));
    }

    public function sendSMSNotification($cust, $lelongan)
    {

        $phone_num = '6' . $cust->phone_no;
        $message = $this->buildMessage($cust, $lelongan, 'sms');

        $client = new \GuzzleHttp\Client();
        $client->request('POST', "https://api.esms.com.my/sms/send", [
            'query' => [
                'user' => config('esms.key'),
                'pass' => config('esms.secret'),
                'to' => $phone_num,
                'msg' => $message,
            ]
        ]);
    }

    private function buildMessage($cust, $lelongan, $notificationType = 'sms')
    {
        $prefix = '';
        if ($notificationType === 'sms') {
            $prefix = 'RM0.00 CSC GOLD: ';
        }

        return $prefix . 'Permohonan Lelongan ' . strtoupper($cust->name) . ' (' . $cust->profile->ic . ') bagi *' . $lelongan->siri_no . '* sebanyak *RM ' . number_format($lelongan->bid, 2) . '* telah berjaya dihantar. Sila semak status permohonan anda dari semasa ke semasa. Terima kasih.';
    }
}