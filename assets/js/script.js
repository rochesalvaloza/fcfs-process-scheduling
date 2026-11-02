// Add and remove process rows, and load the example data.
const rowsContainer = document.getElementById('processRows');
const addRowButton = document.getElementById('addRowButton');
const sampleButton = document.getElementById('sampleButton');

function createProcessRow(id, arrival, burst) {
    const row = document.createElement('tr');

    // Create each input separately and set values as text, not HTML.
    const idCell = document.createElement('td');
    const idInput = document.createElement('input');
    idInput.type = 'text';
    idInput.name = 'process_id[]';
    idInput.maxLength = 12;
    idInput.required = true;
    idInput.value = id;
    idCell.appendChild(idInput);

    const arrivalCell = document.createElement('td');
    const arrivalInput = document.createElement('input');
    arrivalInput.type = 'number';
    arrivalInput.name = 'arrival_time[]';
    arrivalInput.min = '0';
    arrivalInput.step = '1';
    arrivalInput.required = true;
    arrivalInput.value = arrival;
    arrivalCell.appendChild(arrivalInput);

    const burstCell = document.createElement('td');
    const burstInput = document.createElement('input');
    burstInput.type = 'number';
    burstInput.name = 'burst_time[]';
    burstInput.min = '1';
    burstInput.step = '1';
    burstInput.required = true;
    burstInput.value = burst;
    burstCell.appendChild(burstInput);

    const actionCell = document.createElement('td');
    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'remove-row';
    removeButton.textContent = 'Remove';
    removeButton.setAttribute('aria-label', 'Remove process');
    actionCell.appendChild(removeButton);

    row.append(idCell, arrivalCell, burstCell, actionCell);
    return row;
}

addRowButton.addEventListener('click', function () {
    const nextNumber = rowsContainer.querySelectorAll('tr').length + 1;
    rowsContainer.appendChild(createProcessRow(`P${nextNumber}`, 0, 1));
});

// Event delegation means one listener can handle every Remove button.
rowsContainer.addEventListener('click', function (event) {
    if (event.target.classList.contains('remove-row')) {
        const row = event.target.closest('tr');

        // Keep at least one row in the form.
        if (rowsContainer.querySelectorAll('tr').length > 1) {
            row.remove();
        } else {
            alert('At least one process is required.');
        }
    }
});

sampleButton.addEventListener('click', function () {
    // Sample data used in the manual FCFS example.
    const sampleProcesses = [
        ['P1', 0, 5],
        ['P2', 1, 3],
        ['P3', 2, 8],
        ['P4', 3, 2]
    ];

    rowsContainer.replaceChildren();

    sampleProcesses.forEach(function (process) {
        rowsContainer.appendChild(
            createProcessRow(process[0], process[1], process[2])
        );
    });
});
