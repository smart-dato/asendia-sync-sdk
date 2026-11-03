<?php

namespace SmartDato\AsendiaSync\Enums;

enum Product: string
{
    case EpaqStandard = 'EPAQSTD';
    case EpaqPlus = 'EPAQPLS';
    case EpaqSelect = 'EPAQSCT';
    case EpaqElite = 'EPAQELT';
    case EpaqGo = 'EPAQGO';
    case EpaqDomesticReturns = 'EPAQRETDOM';
    case EpaqInternationalReturns = 'EPAQRETINT';
    /** @deprecated use EpaqDomesticReturns */
    case DomesticReturns = 'RETDOM';
    /** @deprecated use EpaqInternationalReturns */
    case InternationalReturns = 'RETINT';
}
