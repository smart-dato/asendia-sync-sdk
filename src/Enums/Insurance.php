<?php

namespace SmartDato\AsendiaSync\Enums;

/**
 * e-PAQ Plus only offers EL45 and EL150.
 */
enum Insurance: string
{
    case EnhancedLiability45 = 'EL45';
    case EnhancedLiability150 = 'EL150';
    case EnhancedLiability500 = 'EL500';
}
