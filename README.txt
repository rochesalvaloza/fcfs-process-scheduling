CPU PROCESS SCHEDULING SIMULATOR - FCFS
=======================================

TECHNOLOGIES
------------
- PHP
- HTML
- CSS
- JavaScript
- XAMPP (Apache)

No database is needed.

HOW TO RUN USING XAMPP
----------------------
1. Install and open XAMPP.
2. Start Apache. MySQL is NOT required.
3. Copy the folder named "fcfs-scheduling" into:
   C:\xampp\htdocs\
4. Open your browser.
5. Visit:
   http://localhost/fcfs-scheduling/
6. Click "Load Sample Problem".
7. Click "Run FCFS Scheduling".

SAMPLE INPUT
------------
Process   Arrival Time   Burst Time
P1        0              5
P2        1              3
P3        2              8
P4        3              2

EXPECTED OUTPUT
---------------
Process   CT   TAT   WT   RT
P1        5    5     0    0
P2        8    7     4    4
P3        16   14    6    6
P4        18   15    13   13

Average Waiting Time:    5.75
Average Turnaround Time: 10.25
Average Response Time:   5.75

FCFS FORMULAS
-------------
Start Time = max(Previous Completion Time, Arrival Time)
CT  = Start Time + Burst Time
TAT = CT - Arrival Time
WT  = TAT - Burst Time
RT  = Start Time - Arrival Time

IMPORTANT CONCEPTS
------------------
Arrival Time (AT):
The time when a process enters the ready queue.

Burst Time (BT):
The amount of CPU time required by a process.

Completion Time (CT):
The time when a process finishes execution.

Turnaround Time (TAT):
The total time from arrival until completion.

Waiting Time (WT):
The total time a process waits in the ready queue.

Response Time (RT):
The time from arrival until the process first gets the CPU.

FCFS:
The first process to arrive is served first. It is non-preemptive,
meaning a running process is not interrupted by another process.
