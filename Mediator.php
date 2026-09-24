<?php

declare(strict_types=1);

/**
 * MEDIATOR (interface)
 *
 * Declares the methods that Colleagues (Aircraft) use to talk to the
 * Mediator (ControlTower) instead of talking to each other directly.
 */
interface ControlTowerMediator
{
    public function requestLanding(Aircraft $aircraft): void;

    public function requestTakeoff(Aircraft $aircraft): void;

    public function runwayVacated(string $callSign): void;
}

/**
 * CONCRETE MEDIATOR
 *
 * Owns the shared resource (the single runway) and the rules for using it.
 * Aircraft never check the runway status themselves — they ask the tower,
 * and the tower decides who gets cleared and who has to hold.
 */
final class ControlTower implements ControlTowerMediator
{
    private ?string $runwayOccupiedBy = null;

    /** @var string[] */
    private array $log = [];

    public function requestLanding(Aircraft $aircraft): void
    {
        $this->handleRequest($aircraft, 'landing');
    }

    public function requestTakeoff(Aircraft $aircraft): void
    {
        $this->handleRequest($aircraft, 'takeoff');
    }

    public function runwayVacated(string $callSign): void
    {
        if ($this->runwayOccupiedBy === $callSign) {
            $this->runwayOccupiedBy = null;
            $this->log[] = "{$callSign} has cleared Runway 27L. Runway is now FREE.";
        }
    }

    private function handleRequest(Aircraft $aircraft, string $action): void
    {
        $callSign = $aircraft->getCallSign();
        $type = $aircraft->getType();
        $verb = $action === 'landing' ? 'land on' : 'take off from';

        if ($this->runwayOccupiedBy === null) {
            $this->runwayOccupiedBy = $callSign;
            $this->log[] = "Tower clears {$callSign} ({$type}) to {$verb} Runway 27L.";
            $aircraft->receiveClearance($action);
        } else {
            $this->log[] = "Tower tells {$callSign} ({$type}) to HOLD — "
                . "Runway 27L occupied by {$this->runwayOccupiedBy}.";
            $aircraft->receiveHold();
        }
    }

    /** @return string[] */
    public function getLog(): array
    {
        return $this->log;
    }

    public function getRunwayStatus(): string
    {
        return $this->runwayOccupiedBy === null
            ? 'FREE'
            : "OCCUPIED by {$this->runwayOccupiedBy}";
    }
}
