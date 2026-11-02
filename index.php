<?php
// Main page: displays the form and the scheduling results.
session_start();

require_once __DIR__ . '/includes/algorithm.php';

// Keep the most recently submitted processes so the page can display them.
$processes = $_SESSION['processes'] ?? [];
$results = $_SESSION['results'] ?? null;
$averages = $_SESSION['averages'] ?? null;
$gantt = $_SESSION['gantt'] ?? [];
$error = $_SESSION['error'] ?? '';

unset($_SESSION['error']);

// Helper function to safely display text in HTML.
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCFS CPU Scheduling Simulator</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="container topbar-inner">
        <div>
            <p class="eyebrow">OPERATING SYSTEMS PROJECT</p>
            <h1>CPU Scheduling Simulator</h1>
            <p class="subtitle">First Come, First Served (FCFS)</p>
        </div>
        <span class="algorithm-badge">FCFS Algorithm</span>
    </div>
</header>

<main class="container page-content">
    <section class="intro-card">
        <div>
            <p class="eyebrow">SELECTED SCHEDULING ALGORITHM</p>
            <h2>First Come, First Served</h2>
            <p>
                FCFS is a non-preemptive scheduling algorithm. The process that
                arrives first in the ready queue is executed first and runs
                until it finishes.
            </p>
        </div>
        <div class="intro-icon" aria-hidden="true">CPU</div>
    </section>

    <?php if ($error !== ''): ?>
        <div class="alert error" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <section class="panel">
        <div class="section-heading">
            <div>
                <p class="eyebrow">STEP 1</p>
                <h2>Enter Process Information</h2>
            </div>
            <button type="button" class="button button-light" id="sampleButton">
                Load Sample Problem
            </button>
        </div>

        <p class="help-text">
            Enter each process ID, arrival time, and burst time. Arrival time
            must be zero or greater. Burst time must be greater than zero.
        </p>

        <form action="process.php" method="POST" id="processForm">
            <div class="table-wrap">
                <table class="input-table">
                    <thead>
                    <tr>
                        <th>Process ID</th>
                        <th>Arrival Time (AT)</th>
                        <th>Burst Time (BT)</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="processRows">
                    <?php if (!empty($processes)): ?>
                        <?php foreach ($processes as $index => $process): ?>
                            <tr>
                                <td>
                                    <input type="text" name="process_id[]" maxlength="12"
                                           value="<?= e($process['id']) ?>" required>
                                </td>
                                <td>
                                    <input type="number" name="arrival_time[]" min="0" step="1"
                                           value="<?= e($process['arrival']) ?>" required>
                                </td>
                                <td>
                                    <input type="number" name="burst_time[]" min="1" step="1"
                                           value="<?= e($process['burst']) ?>" required>
                                </td>
                                <td>
                                    <button type="button" class="remove-row" aria-label="Remove process">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td><input type="text" name="process_id[]" value="P1" maxlength="12" required></td>
                            <td><input type="number" name="arrival_time[]" value="0" min="0" step="1" required></td>
                            <td><input type="number" name="burst_time[]" value="5" min="1" step="1" required></td>
                            <td><button type="button" class="remove-row" aria-label="Remove process">Remove</button></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="button" class="button button-light" id="addRowButton">+ Add Process</button>
                <div class="action-right">
                    <a href="reset.php" class="button button-outline">Reset</a>
                    <button type="submit" class="button button-primary">Run FCFS Scheduling</button>
                </div>
            </div>
        </form>
    </section>

    <?php if (is_array($results) && is_array($averages)): ?>
        <section class="results-section" id="results">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">STEP 2</p>
                    <h2>Scheduling Results</h2>
                </div>
                <span class="status-badge">Completed</span>
            </div>

            <div class="panel">
                <h3>Process Calculation Table</h3>
                <p class="help-text">
                    CT = Completion Time · TAT = Turnaround Time ·
                    WT = Waiting Time · RT = Response Time
                </p>
                <div class="table-wrap">
                    <table class="results-table">
                        <thead>
                        <tr>
                            <th>Process</th><th>AT</th><th>BT</th><th>Start</th>
                            <th>CT</th><th>TAT</th><th>WT</th><th>RT</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td><strong><?= e($row['id']) ?></strong></td>
                                <td><?= e($row['arrival']) ?></td>
                                <td><?= e($row['burst']) ?></td>
                                <td><?= e($row['start']) ?></td>
                                <td><?= e($row['completion']) ?></td>
                                <td><?= e($row['turnaround']) ?></td>
                                <td><?= e($row['waiting']) ?></td>
                                <td><?= e($row['response']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card">
                    <span>Average Waiting Time</span>
                    <strong><?= number_format($averages['waiting'], 2) ?></strong>
                </article>
                <article class="stat-card">
                    <span>Average Turnaround Time</span>
                    <strong><?= number_format($averages['turnaround'], 2) ?></strong>
                </article>
                <article class="stat-card">
                    <span>Average Response Time</span>
                    <strong><?= number_format($averages['response'], 2) ?></strong>
                </article>
            </div>

            <div class="panel">
                <h3>Gantt Chart</h3>
                <p class="help-text">This timeline shows when each process uses the CPU.</p>
                <div class="gantt-scroll">
                    <div class="gantt">
                        <?php foreach ($gantt as $segment): ?>
                            <div class="gantt-segment <?= $segment['type'] === 'idle' ? 'idle-segment' : '' ?>"
                                 style="flex-grow: <?= max(1, (int)$segment['duration']) ?>;">
                                <strong><?= e($segment['label']) ?></strong>
                                <small><?= e($segment['start']) ?> – <?= e($segment['end']) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="gantt-caption">
                        <span>Timeline is proportional to each segment's duration.</span>
                        <span>End time: <?= e(end($gantt)['end'] ?? 0) ?></span>
                    </div>
                </div>
            </div>

            <div class="panel summary-panel">
                <h3>Execution Summary</h3>
                <ul>
                    <li><strong>Execution order:</strong>
                        <?php
                        $order = array_map(fn($row) => $row['id'], $results);
                        echo e(implode(' → ', $order));
                        ?>
                    </li>
                    <li><strong>Total processes:</strong> <?= count($results) ?></li>
                    <li><strong>Total CPU burst time:</strong> <?= array_sum(array_column($results, 'burst')) ?> time units</li>
                    <li><strong>Average waiting time:</strong> <?= number_format($averages['waiting'], 2) ?></li>
                    <li><strong>Average turnaround time:</strong> <?= number_format($averages['turnaround'], 2) ?></li>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <section class="formula-card">
        <p class="eyebrow">QUICK REFERENCE</p>
        <h2>FCFS Formulas</h2>
        <div class="formula-grid">
            <div><strong>Completion Time</strong><code>CT = Start Time + BT</code></div>
            <div><strong>Turnaround Time</strong><code>TAT = CT - AT</code></div>
            <div><strong>Waiting Time</strong><code>WT = TAT - BT</code></div>
            <div><strong>Response Time</strong><code>RT = Start Time - AT</code></div>
        </div>
    </section>
</main>

<footer>
    <div class="container">CPU Process Scheduling Simulator · FCFS Algorithm</div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>
