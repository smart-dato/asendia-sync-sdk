<?php

namespace SmartDato\AsendiaSync\Resources;

use SmartDato\AsendiaSync\Data\Documents\CommercialInvoiceData;
use SmartDato\AsendiaSync\Data\Documents\CustomsDocumentData;
use SmartDato\AsendiaSync\Data\Documents\DocumentData;
use SmartDato\AsendiaSync\Requests\Documents\CreateCommercialInvoiceRequest;
use SmartDato\AsendiaSync\Requests\Documents\UploadCustomsDocumentRequest;

class DocumentsResource extends BaseResource
{
    public function createCommercialInvoice(CommercialInvoiceData $invoice): DocumentData
    {
        return $this->download(new CreateCommercialInvoiceRequest($invoice));
    }

    public function uploadCustomsDocument(CustomsDocumentData $document, string $pdf, string $filename = 'customs-document.pdf'): void
    {
        $this->send(new UploadCustomsDocumentRequest($document, $pdf, $filename));
    }
}
