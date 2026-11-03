<?php

namespace SmartDato\AsendiaSync\Exceptions;

/**
 * HTTP 400: the payload broke a constraint, see $fieldErrors, or the parcels
 * of a manifest request were manifested already, see $alreadyManifestedParcels.
 */
class ValidationException extends AsendiaSyncApiException {}
