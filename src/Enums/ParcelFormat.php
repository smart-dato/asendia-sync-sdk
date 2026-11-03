<?php

namespace SmartDato\AsendiaSync\Enums;

/**
 * The price differs per format, so the shipper picks it; Asendia does not
 * derive it. The Swagger says e-PAQ Select and Elite may omit it, but Asendia
 * Sync rejects a parcel without format ("Mandatory field") for those too.
 */
enum ParcelFormat: string
{
    case Boxable = 'B';
    case NonBoxable = 'N';
    case Large = 'L';
    case ExtraLarge = 'XL';
}
