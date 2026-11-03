<?php

namespace SmartDato\AsendiaSync\Enums;

enum Service: string
{
    case CustomsUnpaid = 'CUP';
    case CustomsPrepaidByRetailer = 'CPPR';
    case CustomsPrepaidByShopper = 'CPPS';
    case PrepaidReturn = 'RETPP';
    case PartiallyPaidReturn = 'RETPAP';
}
