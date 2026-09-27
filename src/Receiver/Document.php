<?php

declare(strict_types=1);

namespace IPT10\Command\Receiver;

/**
 * Receiver: Document
 * 
 * Knows how to perform the actual domain operations associated with carrying out
 * document management requests. The receiver maintains the core state.
 */
class Document
{
    private string $content = '';
    private WatermarkType $watermark = WatermarkType::NONE;
    private int $revision = 0;

    public function __construct(
        public readonly string $documentId,
        public readonly string $title,
        public readonly string $author
    ) {
    }

    /**
     * Appends text content to the document.
     */
    public function appendContent(string $text): void
    {
        $this->content .= $text;
        $this->revision++;
    }

    /**
     * Removes a specified length of text from the end of the document.
     */
    public function removeTrailingContent(int $length): void
    {
        if ($length <= 0) {
            return;
        }

        $currentLength = mb_strlen($this->content);
        if ($length >= $currentLength) {
            $this->content = '';
        } else {
            $this->content = mb_substr($this->content, 0, $currentLength - $length);
        }
        $this->revision++;
    }

    /**
     * Updates the classification watermark.
     */
    public function setWatermark(WatermarkType $watermark): void
    {
        $this->watermark = $watermark;
        $this->revision++;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getWatermark(): WatermarkType
    {
        return $this->watermark;
    }

    public function getRevision(): int
    {
        return $this->revision;
    }

    /**
     * Returns an informational summary of the document's current state.
     */
    public function getStateSummary(): string
    {
        $truncated = mb_strlen($this->content) > 40
            ? mb_substr($this->content, 0, 37) . '...'
            : $this->content;

        return sprintf(
            "[Doc ID: %s | Rev: %d | Watermark: %s | Content: \"%s\"]",
            $this->documentId,
            $this->revision,
            $this->watermark->value,
            $truncated
        );
    }
}
