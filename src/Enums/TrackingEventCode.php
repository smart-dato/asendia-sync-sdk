<?php

namespace SmartDato\AsendiaSync\Enums;

/**
 * The main milestones of the harmonized Asendia event codes. The API returns
 * further codes for customs handling, delivery attempts and returns, so
 * events keep the raw code and only map to these where they match.
 */
enum TrackingEventCode: string
{
    case ElectronicNotification = 'ELECNOT';
    case CheckIn = 'CHECKIN';
    case DepartedOrigin = 'DEPSUB';
    case ArrivedAtDestination = 'ARRDEST';
    case OutOfDeliveryCenter = 'OUTDELIVCENTER';
    case Delivered = 'DELIVERY';
}
