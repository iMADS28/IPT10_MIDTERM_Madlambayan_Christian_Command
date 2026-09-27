<?php

declare(strict_types=1);

namespace IPT10\Command\Commands;

use IPT10\Command\Contracts\CommandInterface;
use IPT10\Command\Receiver\Document;

/**
 * Concrete Command: AppendTextCommand
 * 
 * Binds the Document receiver with the action of appending content.
 * Preserves the exact length of appended text to provide exact, reversible undo.
 */
class AppendTextCommand implements CommandInterface
{
    private bool $executed = false;
    private int $appendedLength = 0;

    public function __construct(
        private readonly Document $document,
        private readonly string $textToAppend
    ) {
    }

    public function execute(): bool
    {
        if ($this->textToAppend === '') {
            return false;
        }

        $this->appendedLength = mb_strlen($this->textToAppend);
        $this->document->appendContent($this->textToAppend);
        $this->executed = true;

        return true;
    }

    public function undo(): void
    {
        if (!$this->executed) {
            return;
        }

        $this->document->removeTrailingContent($this->appendedLength);
        $this->executed = false;
    }

    public function getDescription(): string
    {
        return sprintf("AppendTextCommand: \"%s\" (Length: %d)", $this->textToAppend, mb_strlen($this->textToAppend));
    }
}
