# Code Explanation — Mediator Pattern (Airport Runway Control)

## The problem it solves

Three aircraft (`PassengerPlane`, `CargoPlane`, `PrivateJet`) share **one runway**.
Without a mediator, every aircraft would need to know about every other
aircraft to check "is the runway free?" before acting — a tangled, hard-to-extend
web of direct references. The **Mediator pattern** removes those direct
references: aircraft only ever talk to a central `ControlTower`, and the
tower alone decides who is cleared and who has to hold.

## The four participants

| Role | Class | Responsibility |
|---|---|---|
| **Mediator** (interface) | `ControlTowerMediator` | Declares the contract colleagues use to communicate: `requestLanding()`, `requestTakeoff()`, `runwayVacated()`. |
| **Concrete Mediator** | `ControlTower` | Owns the shared state — `$runwayOccupiedBy` — and the one business rule ("only one aircraft on the runway at a time"). It grants clearance or issues a hold, and keeps a communications log. |
| **Colleague** (abstract) | `Aircraft` | Holds a single reference to the mediator (never to other aircraft). Exposes `requestLanding()`/`requestTakeoff()`, which simply forward to the tower, and reacts to `receiveClearance()` / `receiveHold()`. |
| **Concrete Colleagues** | `PassengerPlane`, `CargoPlane`, `PrivateJet` | Thin subclasses that only set a `$type` label — all coordination logic lives in `Aircraft` and `ControlTower`, not here. |

`index.php` is the **client**: it builds one `ControlTower` and three
`Aircraft` objects, then plays out a scenario — two planes competing for
the runway, one holding, then getting cleared once the runway is vacated —
and renders the result as a Bootstrap page (red theme, CCS logo in the navbar).

## How a request flows

1. `$flight2->requestTakeoff()` is called on the **colleague**.
2. `Aircraft::requestTakeoff()` simply forwards to `$this->tower->requestTakeoff($this)`.
3. The **mediator** (`ControlTower::handleRequest()`) checks its own state:
   - If the runway is free → it marks itself occupied, logs a clearance
     message, and calls `$aircraft->receiveClearance('takeoff')`.
   - If the runway is busy → it logs a hold message and calls
     `$aircraft->receiveHold()`.
4. When an aircraft calls `vacateRunway()`, it tells the tower
   `runwayVacated($callSign)`, which frees the runway for the next request.

Notice step 3: **all decision-making lives in `ControlTower`**. `Aircraft`
never inspects another aircraft's state — it can't, because it has no
reference to one. This is the essence of the pattern: it converts
many-to-many coupling between colleagues into one-to-many coupling between
each colleague and a single mediator.

## Why this matters in practice

- **Adding a new aircraft type** (e.g. `Helicopter`) means writing one
  small subclass — zero changes to `ControlTower` or any existing aircraft.
- **Changing the runway rule** (e.g. allowing two parallel runways) means
  editing `ControlTower` only — every `Aircraft` subclass is untouched.
- **Testing is simpler**: you can unit-test `ControlTower`'s clearance
  logic in isolation, and test `Aircraft` with a mock mediator, without
  wiring up a full fleet.

## Files at a glance

- `Mediator.php` — the interface + concrete mediator (2 classes)
- `Aircraft.php` — the abstract colleague + 3 concrete colleagues (4 classes)
- `index.php` — client code + Bootstrap 5 view
- `README.md` — run instructions
- `uml-diagram.drawio` — UML class diagram (open at app.diagrams.net)
