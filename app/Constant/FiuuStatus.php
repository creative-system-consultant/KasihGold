<?php

namespace App\Constant;

class FiuuStatus
{
    // The statuses below are retrieved from Fiuu documentation see: https://github.com/FiuuPayment/Documentation-Fiuu_API_Spec/blob/main/Fiuu%20Recurring%20API%20v7.1.4.pdf
    const FIUU_STATUS_SUCCESS = 00;
    const FIUU_STATUS_FAILED = 11;
    const FIUU_STATUS_PENDING = 22;

    public function getFiuuStatus()
    {
        return[
            self::FIUU_STATUS_SUCCESS,
            self::FIUU_STATUS_FAILED,
            self::FIUU_STATUS_PENDING,
        ];
    }
}