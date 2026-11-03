<?php

namespace SmartDato\AsendiaSync\Enums;

enum ServiceOption: string
{
    case PrintedMatter = 'PM';
    case Economy = 'ECO';
    case PostOfficeBox = 'POX';
    case PersonalDelivery = 'PD';
    case Signature = 'SIG';
    case PickUpDropOff = 'PUDO';
    case MailboxPlus = 'MBX';
    case DangerousGoods = 'DG';
    case SaturdayDelivery = 'SAT';
    case DirectAccess = 'DA';
    case SmartgateFlex = 'SGF';
    case SmartgateDirect = 'SGD';
    case Option1 = 'OPT1';
    case Option2 = 'OPT2';
}
