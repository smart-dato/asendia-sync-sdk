<?php

namespace SmartDato\AsendiaSync\Enums;

/**
 * e-PAQ Select and e-PAQ Elite may omit the format. The price differs per
 * format, so the shipper picks it, Asendia does not derive it.
 */
enum ParcelFormat: string
{
    case Boxable = 'B';
    case NonBoxable = 'N';
    case Large = 'L';
    case ExtraLarge = 'XL';
}
