<?php

namespace SmartDato\AsendiaSync\Enums;

enum ParcelDocument: string
{
    case Label = 'label';
    case ReturnLabel = 'return-label';
    case CustomsDocument = 'customs-document';
    case CommercialInvoice = 'commercial-invoice-document';
}
