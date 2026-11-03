<?php

namespace SmartDato\AsendiaSync\Enums;

/**
 * Content category of a parcel for the customs declaration (`reasonForExport`).
 */
enum ReasonForExport: string
{
    case Document = 'D';
    case Merchandise = 'M';
    case Gift = 'G';
    case Sample = 'S';
    case ReturnedGoods = 'R';
    case Other = 'O';
}
