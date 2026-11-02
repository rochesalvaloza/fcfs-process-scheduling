<?php
/**
 * Runs First Come, First Served scheduling.
 *
 * @param array $processes Each process contains id, arrival, burst, and input_order.
 * @return array Results, averages, and Gantt chart segments.
 */
function runFCFS(array $processes): array
{
    // Sort by arrival time. If arrival times match, keep the original input order.
    usort($processes, function ($a, $b) {
        if ($a['arrival'] === $b['arrival']) {
            return $a['input_order'] <=> $b['input_order'];
        }
        return $a['arrival'] <=> $b['arrival'];
    });

    $currentTime = 0;
    $results = [];
    $gantt = [];

    // Process each item in the sorted ready-queue order.
    foreach ($processes as $process) {
        // If the CPU is free before this process arrives, record an idle period.
        if ($currentTime < $process['arrival']) {
            $gantt[] = [
                'label' => 'IDLE',
                'start' => $currentTime,
                'end' => $process['arrival'],
                'duration' => $process['arrival'] - $currentTime,
                'type' => 'idle'
            ];

            // Move the clock forward to the process arrival time.
            $currentTime = $process['arrival'];
        }

        // In non-preemptive FCFS, the process starts and runs until it finishes.
        $startTime = $currentTime;
        $completionTime = $startTime + $process['burst'];

        // Calculate the scheduling measurements.
        $turnaroundTime = $completionTime - $process['arrival'];
        $waitingTime = $turnaroundTime - $process['burst'];
        $responseTime = $startTime - $process['arrival'];

        $results[] = [
            'id' => $process['id'],
            'arrival' => $process['arrival'],
            'burst' => $process['burst'],
            'start' => $startTime,
            'completion' => $completionTime,
            'turnaround' => $turnaroundTime,
            'waiting' => $waitingTime,
            'response' => $responseTime
        ];

        // Add this process to the Gantt chart timeline.
        $gantt[] = [
            'label' => $process['id'],
            'start' => $startTime,
            'end' => $completionTime,
            'duration' => $process['burst'],
            'type' => 'process'
        ];

        // The next process can start only after this one completes.
        $currentTime = $completionTime;
    }

    // Calculate average values for all processes.
    $count = count($results);
    $totalWaiting = array_sum(array_column($results, 'waiting'));
    $totalTurnaround = array_sum(array_column($results, 'turnaround'));
    $totalResponse = array_sum(array_column($results, 'response'));

    $averages = [
        'waiting' => $totalWaiting / $count,
        'turnaround' => $totalTurnaround / $count,
        'response' => $totalResponse / $count
    ];

    return [
        'results' => $results,
        'averages' => $averages,
        'gantt' => $gantt
    ];
}
