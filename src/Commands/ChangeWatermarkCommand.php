<?php

declare(strict_types=1);

namespace IPT10\Command\Commands;

use IPT10\Command\Contracts\CommandInterface;
use IPT10\Command\Receiver\Document;
use IPT10\Command\Receiver\WatermarkType;

/**
 * Concrete Command: ChangeWatermarkCommand
 * 
 * Binds the Document receiver with the action of updating its classification watermark.
 * Records the previous watermark state prior to execution to enable undo.
 */
class ChangeWatermarkCommand implements CommandInterface
{
    private ?WatermarkType $previousWatermark = null;
    private bool $executed = false;

    public function __construct(
        private readonly Document $document,
        private readonly WatermarkType $newWatermark
    ) {
    }

    public function execute(): bool
    {
        $this->previousWatermark = $this->document->getWatermark();
        $this->document->setWatermark($this->newWatermark);
        $this->executed = true;

        return true;
    }

    public function undo(): void
    {
        if (!$this->executed || $this->previousWatermark === null) {
            return;
        }

        $this->document->setWatermark($this->previousWatermark);
        $this->executed = false;
    }

    public function getDescription(): string
    {
        return sprintf(
            "ChangeWatermarkCommand: %s -> %s",
            $this->previousWatermark?->value ?? 'PENDING',
            $this->newWatermark->value
        );
    }
}
