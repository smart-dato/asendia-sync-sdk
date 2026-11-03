<?php

namespace SmartDato\AsendiaSync\Enums;

/**
 * Reason of export printed on a commercial invoice (`reasonOfExport`).
 */
enum ReasonOfExport: string
{
    case Documents = 'DOCS';
    case SaleOfGoods = 'SALE_OF_GOODS_PERMANENT';
    case ReturnedGoods = 'RETURNED_GOODS';
    case Samples = 'SAMPLES';
    case Gift = 'GIFT_PERMANENT';
    case Others = 'OTHERS';
}
