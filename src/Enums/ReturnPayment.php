<?php

namespace SmartDato\AsendiaSync\Enums;

enum ReturnPayment: string
{
    case Prepaid = 'RETPP';
    case PartiallyPaid = 'RETPAP';
}
