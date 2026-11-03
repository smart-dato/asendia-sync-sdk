<?php

namespace SmartDato\AsendiaSync\Exceptions;

/**
 * HTTP 429: the tracking provider behind Asendia Sync throttled the lookup.
 */
class RateLimitExceededException extends AsendiaSyncApiException {}
