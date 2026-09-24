# Manila Tower — Mediator Pattern Demo (PHP 8.1+)

A tiny, runnable PHP sample that shows the **Mediator design pattern**
using an airport-runway theme: several aircraft need to land or take off,
but there's only **one runway**. Instead of aircraft coordinating with
each other, they all talk to a single `ControlTower` (the mediator),
which decides who gets cleared and who has to hold.

## Files (4 PHP files total)

| File            | Role in the pattern                                              |
|-----------------|--------------------------------------------------------------------|
| `Mediator.php`  | `ControlTowerMediator` (Mediator interface) + `ControlTower` (Concrete Mediator) |
| `Aircraft.php`  | `Aircraft` (abstract Colleague) + `PassengerPlane`, `CargoPlane`, `PrivateJet` (Concrete Colleagues) |
| `index.php`     | Client code — wires everything together and renders the Bootstrap UI |
| `README.md`     | This file |

## Requirements

- PHP **8.1** or newer (uses constructor property promotion, `readonly`, `declare(strict_types=1)`)
- No Composer, no dependencies. Bootstrap 5 is loaded from a CDN in the browser.

## How to run

1. Make sure PHP is installed:
   ```bash
   php -v
   ```
2. From inside the project folder, start PHP's built-in web server:
   ```bash
   php -S localhost:8000
   ```
3. Open your browser at:
   ```
   http://localhost:8000
   ```

You'll see the fleet status, the current runway status, and a live
"Tower Communications Log" showing every request, clearance, and hold —
all mediated by `ControlTower`.

## Pattern participants

- **Mediator** (`ControlTowerMediator`): the interface that defines how
  colleagues communicate — `requestLanding()`, `requestTakeoff()`, `runwayVacated()`.
- **Concrete Mediator** (`ControlTower`): implements the interface, holds
  the shared state (who currently owns the runway) and the business rule
  ("only one aircraft on the runway at a time"). It's the *only* class
  that knows about every aircraft's request.
- **Colleague** (`Aircraft`, abstract): knows about the mediator only,
  never about other aircraft. Exposes `requestLanding()`, `requestTakeoff()`,
  `vacateRunway()`, and reacts to `receiveClearance()` / `receiveHold()`.
- **Concrete Colleagues**: `PassengerPlane`, `CargoPlane`, `PrivateJet` —
  differ only in their `$type` label; all behavior lives in `Aircraft`.
- **Client** (`index.php`): creates the mediator and colleagues, and
  triggers the scenario (two aircraft competing for one runway, one
  holding, then getting cleared once the runway is vacated).

## Why this is the Mediator pattern

Without a mediator, every `Aircraft` would need a reference to every
other `Aircraft` to check "is anyone using the runway right now?" — an
O(n²) web of dependencies that gets worse as you add more planes. With
the mediator, each `Aircraft` only holds one reference (`ControlTowerMediator`),
and all cross-aircraft coordination logic is centralized in `ControlTower`.
Adding a fourth aircraft type means adding one small class — zero changes
to existing aircraft.
