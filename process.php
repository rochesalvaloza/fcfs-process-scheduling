<?php
// Receives the form data, validates it, runs FCFS, and saves results in session.
session_start();
require_once __DIR__ . '/includes/algorithm.php';

try {
    // Make sure the form submitted all three arrays.
    if (
        !isset($_POST['process_id'], $_POST['arrival_time'], $_POST['burst_time']) ||
        !is_array($_POST['process_id']) ||
        !is_array($_POST['arrival_time']) ||
        !is_array($_POST['burst_time'])
    ) {
        throw new InvalidArgumentException('Please enter at least one process.');
    }

    $ids = $_POST['process_id'];
    $arrivals = $_POST['arrival_time'];
    $bursts = $_POST['burst_time'];

    // Every process must have an ID, arrival time, and burst time.
    if (count($ids) === 0 || count($ids) !== count($arrivals) || count($ids) !== count($bursts)) {
        throw new InvalidArgumentException('The process information is incomplete.');
    }

    if (count($ids) > 20) {
        throw new InvalidArgumentException('For this classroom simulator, enter no more than 20 processes.');
    }

    $processes = [];
    $usedIds = [];

    foreach ($ids as $index => $rawId) {
        $id = trim((string)$rawId);
        $arrivalRaw = $arrivals[$index];
        $burstRaw = $bursts[$index];

        if ($id === '' || strlen($id) > 12) {
            throw new InvalidArgumentException('Each process must have an ID from 1 to 12 characters.');
        }

        // Avoid duplicate process IDs so the results are easy to identify.
        if (in_array(strtolower($id), $usedIds, true)) {
            throw new InvalidArgumentException('Process IDs must be unique.');
        }
        $usedIds[] = strtolower($id);

        // Validate that arrival and burst times are whole numbers.
        $arrival = filter_var($arrivalRaw, FILTER_VALIDATE_INT);
        $burst = filter_var($burstRaw, FILTER_VALIDATE_INT);

        if ($arrival === false || $arrival < 0) {
            throw new InvalidArgumentException('Arrival Time must be a whole number that is zero or greater.');
        }
        if ($burst === false || $burst <= 0) {
            throw new InvalidArgumentException('Burst Time must be a whole number greater than zero.');
        }

        $processes[] = [
            'id' => $id,
            'arrival' => $arrival,
            'burst' => $burst,
            'input_order' => $index
        ];
    }

    // Run the FCFS algorithm using the validated processes.
    $schedule = runFCFS($processes);

    $_SESSION['processes'] = $processes;
    $_SESSION['results'] = $schedule['results'];
    $_SESSION['averages'] = $schedule['averages'];
    $_SESSION['gantt'] = $schedule['gantt'];

} catch (InvalidArgumentException $exception) {
    $_SESSION['error'] = $exception->getMessage();
} catch (Throwable $exception) {
    // Show a safe message instead of exposing technical server details.
    $_SESSION['error'] = 'Something went wrong. Please check your inputs and try again.';
}

header('Location: index.php#results');
exit;
