<?php

declare(strict_types=1);

namespace IPT10\Command\Contracts;

/**
 * Command Interface
 * 
 * Declares the standard execution contract for all concrete commands.
 * Under GoF specifications, it defines an execute() method and an optional
 * undo() method for reversible operations.
 */
interface CommandInterface
{
    /**
     * Executes the encapsulated operation on the receiver.
     */
    public function execute(): bool;

    /**
     * Reverses the operation, restoring the receiver's previous state.
     */
    public function undo(): void;

    /**
     * Provides a human-readable description for audit logging and diagnostics.
     */
    public function getDescription(): string;
}
