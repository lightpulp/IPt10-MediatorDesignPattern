<?php

declare(strict_types=1);

require_once __DIR__ . '/Mediator.php';

/**
 * COLLEAGUE (abstract)
 *
 * Each Aircraft only knows about its Mediator (the ControlTower), never
 * about other aircraft. All coordination is routed through the tower.
 */
abstract class Aircraft
{
    protected string $type = 'Aircraft';
    protected string $status = 'Airborne';

    public function __construct(
        private readonly ControlTowerMediator $tower,
        private readonly string $callSign
    ) {
    }

    public function getCallSign(): string
    {
        return $this->callSign;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function requestLanding(): void
    {
        $this->tower->requestLanding($this);
    }

    public function requestTakeoff(): void
    {
        $this->tower->requestTakeoff($this);
    }

    public function vacateRunway(): void
    {
        $this->status = 'Taxiing';
        $this->tower->runwayVacated($this->callSign);
    }

    public function receiveClearance(string $action): void
    {
        $this->status = $action === 'landing' ? 'Landed' : 'Airborne (departed)';
    }

    public function receiveHold(): void
    {
        $this->status = 'Holding';
    }
}

/** CONCRETE COLLEAGUE */
final class PassengerPlane extends Aircraft
{
    protected string $type = 'Passenger Plane';
}

/** CONCRETE COLLEAGUE */
final class CargoPlane extends Aircraft
{
    protected string $type = 'Cargo Plane';
}

/** CONCRETE COLLEAGUE */
final class PrivateJet extends Aircraft
{
    protected string $type = 'Private Jet';
}
