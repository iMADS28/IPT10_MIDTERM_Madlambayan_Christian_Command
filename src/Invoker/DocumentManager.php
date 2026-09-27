<?php

declare(strict_types=1);

namespace IPT10\Command\Invoker;

use IPT10\Command\Contracts\CommandInterface;

/**
 * Invoker: DocumentManager
 * 
 * Responsible for initiating requests and managing command lifecycles.
 * It does not know the concrete receivers or the exact actions performed.
 * It maintains the undo stack, redo stack, and transactional audit log.
 */
class DocumentManager
{
    /** @var CommandInterface[] Stack of executed commands available for undo */
    private array $undoStack = [];

    /** @var CommandInterface[] Stack of undone commands available for redo */
    private array $redoStack = [];

    /** @var string[] Log of all executed operations for auditing */
    private array $auditLog = [];

    /**
     * Executes a command, registers it in the undo stack, and clears redo history.
     */
    public function executeCommand(CommandInterface $command): bool
    {
        $success = $command->execute();

        if ($success) {
            $this->undoStack[] = $command;
            $this->redoStack = []; // New action invalidates previous redo branch
            $this->logAction("EXECUTED: " . $command->getDescription());
            return true;
        }

        $this->logAction("FAILED: " . $command->getDescription());
        return false;
    }

    /**
     * Reverses the most recent command on the undo stack.
     */
    public function undo(): bool
    {
        if (empty($this->undoStack)) {
            $this->logAction("UNDO_SKIPPED: Undo stack is empty.");
            return false;
        }

        $command = array_pop($this->undoStack);
        $command->undo();
        $this->redoStack[] = $command;

        $this->logAction("UNDONE: " . $command->getDescription());
        return true;
    }

    /**
     * Re-applies the most recently undone command.
     */
    public function redo(): bool
    {
        if (empty($this->redoStack)) {
            $this->logAction("REDO_SKIPPED: Redo stack is empty.");
            return false;
        }

        $command = array_pop($this->redoStack);
        $command->execute();
        $this->undoStack[] = $command;

        $this->logAction("REDONE: " . $command->getDescription());
        return true;
    }

    public function getUndoStackCount(): int
    {
        return count($this->undoStack);
    }

    public function getRedoStackCount(): int
    {
        return count($this->redoStack);
    }

    /**
     * @return string[]
     */
    public function getAuditLog(): array
    {
        return $this->auditLog;
    }

    private function logAction(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $this->auditLog[] = sprintf("[%s] %s", $timestamp, $message);
    }
}
