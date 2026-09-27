<?php

declare(strict_types=1);

namespace IPT10\Command\Commands;

use IPT10\Command\Contracts\CommandInterface;

/**
 * Composite Command: BatchMacroCommand
 * 
 * Implements the GoF MacroCommand pattern. Aggregates multiple commands
 * into a single atomic transactional unit. Undos are executed in reverse (LIFO) order.
 */
class BatchMacroCommand implements CommandInterface
{
    /** @var CommandInterface[] */
    private array $commands = [];
    /** @var CommandInterface[] */
    private array $executedCommands = [];
    private bool $executed = false;

    public function __construct(
        private readonly string $batchName,
        array $commands = []
    ) {
        foreach ($commands as $cmd) {
            $this->addCommand($cmd);
        }
    }

    public function addCommand(CommandInterface $command): void
    {
        $this->commands[] = $command;
    }

    public function execute(): bool
    {
        $this->executedCommands = [];

        foreach ($this->commands as $command) {
            $success = $command->execute();
            if (!$success) {
                // Transactional failure: rollback partially executed commands in reverse
                $this->undo();
                return false;
            }
            $this->executedCommands[] = $command;
        }

        $this->executed = true;
        return true;
    }

    public function undo(): void
    {
        // Rollback executed child commands in reverse order
        while (!empty($this->executedCommands)) {
            $lastCommand = array_pop($this->executedCommands);
            $lastCommand->undo();
        }
        $this->executed = false;
    }

    public function getDescription(): string
    {
        return sprintf(
            "BatchMacroCommand [\"%s\"] containing %d sub-commands",
            $this->batchName,
            count($this->commands)
        );
    }
}
