<?php

namespace SmartDato\AsendiaSync\Enums;

enum LabelRotation: string
{
    case Portrait = 'PORTRAIT';
    case Landscape90 = 'LANDSCAPE_90';
    case Portrait180 = 'PORTRAIT_180';
    case Landscape270 = 'LANDSCAPE_270';
}
