<?php

namespace SmartDato\AsendiaSync\Enums;

enum ReturnLabelType: string
{
    case EpaqDomesticReturns = 'EPAQRETDOM';
    case EpaqInternationalReturns = 'EPAQRETINT';
    /** @deprecated use EpaqDomesticReturns */
    case DomesticReturns = 'RETDOM';
    /** @deprecated use EpaqInternationalReturns */
    case InternationalReturns = 'RETINT';
}
