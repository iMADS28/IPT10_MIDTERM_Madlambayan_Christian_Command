<?php

declare(strict_types=1);

namespace IPT10\Command\Receiver;

/**
 * WatermarkType Enum (PHP 8.1+)
 * 
 * Represents classification levels for the document receiver.
 */
enum WatermarkType: string
{
    case NONE = 'NONE';
    case DRAFT = 'DRAFT';
    case CONFIDENTIAL = 'CONFIDENTIAL';
    case APPROVED = 'APPROVED';
    case ARCHIVED = 'ARCHIVED';
}
