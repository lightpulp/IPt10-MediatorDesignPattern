<?php

declare(strict_types=1);

require_once __DIR__ . '/Aircraft.php';

/**
 * CLIENT CODE
 *
 * Builds one Mediator (ControlTower) and several Colleagues (Aircraft),
 * then drives a scenario. Notice the aircraft never reference each other —
 * every interaction goes through $tower.
 */
$tower = new ControlTower();

$flight1 = new PassengerPlane($tower, 'PAL118');
$flight2 = new CargoPlane($tower, 'FEDEX205');
$flight3 = new PrivateJet($tower, 'RPC77');

$flight1->requestLanding();   // Runway free -> PAL118 cleared to land
$flight2->requestTakeoff();   // Runway busy  -> FEDEX205 told to hold
$flight1->vacateRunway();     // PAL118 taxis off -> runway freed, tower notified
$flight2->requestTakeoff();   // Runway free -> FEDEX205 cleared to take off
$flight3->requestLanding();   // Runway busy  -> RPC77 told to hold
$flight2->vacateRunway();     // FEDEX205 departs airspace -> runway freed
$flight3->requestLanding();   // Runway free -> RPC77 cleared to land

$log = $tower->getLog();
$fleet = [$flight1, $flight2, $flight3];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manila Tower &mdash; Mediator Pattern Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --tower-red: #b3131a; }
        body { background: #f7f1f1; }
        .navbar-tower { background-color: var(--tower-red); }
        .card-header-tower { background-color: var(--tower-red); color: #fff; }
        .btn-tower { background-color: var(--tower-red); border-color: var(--tower-red); color: #fff; }
        .badge-tower { background-color: var(--tower-red); }
        .runway-strip {
            background: #333;
            color: #fff;
            border-radius: .5rem;
            padding: 1rem;
            font-family: monospace;
            letter-spacing: .2em;
            text-align: center;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-tower navbar-dark mb-4 shadow">
    <div class="container">
        <span class="navbar-brand d-flex align-items-center gap-2">
            <img src="https://www.auf.edu.ph/home/images/mascot/CCS.png" alt="Logo" height="40">
            Manila Tower &mdash; Runway Mediator Demo
        </span>
    </div>
</nav>

<div class="container pb-5">

    <div class="row mb-4">
        <div class="col-12">
            <div class="runway-strip">27L / 09R &mdash; SINGLE ACTIVE RUNWAY</div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header card-header-tower">✈️ Fleet Status</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($fleet as $plane): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <strong><?= htmlspecialchars($plane->getCallSign()) ?></strong>
                                &mdash; <?= htmlspecialchars($plane->getType()) ?>
                            </span>
                            <span class="badge badge-tower rounded-pill">
                                <?= htmlspecialchars($plane->getStatus()) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card shadow-sm">
                <div class="card-header card-header-tower">🗼 Current Runway Status</div>
                <div class="card-body">
                    <p class="mb-0 fs-5">
                        Runway 27L: <strong><?= htmlspecialchars($tower->getRunwayStatus()) ?></strong>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header card-header-tower">📻 Tower Communications Log</div>
                <div class="card-body">
                    <?php foreach ($log as $line): ?>
                        <div class="alert alert-light border-start border-4 border-danger py-2 mb-2">
                            <?= htmlspecialchars($line) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <p class="text-center text-muted mt-4">
        Every message above travels through <code>ControlTower</code>. No <code>Aircraft</code>
        object ever calls another <code>Aircraft</code> object directly &mdash; that's the Mediator pattern.
    </p>
</div>

</body>
</html>
