let form = document.getElementById('table-form');
let addButton = document.getElementById('input-insert');
let tableBody =document.getElementById('table-body');

let updateButtons = document.querySelectorAll('button.update-button');
let deleteButtons = document.querySelectorAll('button.delete-button');

addButton.addEventListener('click', async function addClick(event) {
    event.preventDefault();
    let formData = new FormData(form);
    let response = await fetch('addEntry.php', {method: 'POST', body: formData, headers: {'Accept': 'application/json'}})
        if (response.ok) {
            let tableRow = document.createElement('tr');
            tableBody.appendChild(tableRow);
            let result = await response.json();
            let arr = Object.entries(result);
            arr.forEach(i => {
                let cell = document.createElement('td');
                cell.className = 'value-cell';
                let input = document.createElement('input');
                input.value = i[1];
                input.name = `update[${i[0]}]`;
                input.disabled = true;
                cell.appendChild(input);
                tableRow.appendChild(cell);
            });
            let cell = document.createElement('td');
            let updateButton = document.createElement('button');
            updateButton.className = 'update-button';
            updateButton.innerHTML = 'Обновить';
            let deleteButton = document.createElement('button');
            deleteButton.className = 'delete-button';
            deleteButton.innerHTML = 'Удалить';
            cell.appendChild(updateButton);
            cell.appendChild(deleteButton);
            tableRow.appendChild(cell);
            refreshButtons();
            
        }
    });
let enableclick = false;

function refreshButtons() {
    updateButtons = document.querySelectorAll('button.update-button');
    deleteButtons = document.querySelectorAll('button.delete-button');
    deleteButtons.forEach(b => {
        b.removeEventListener('click', deleteClick);
        b.addEventListener('click', deleteClick);
    });
    updateButtons.forEach(b => {
        b.removeEventListener('click', updateClick);
        b.addEventListener('click', updateClick);
    });
}

deleteButtons.forEach(b => {
    b.addEventListener('click', deleteClick);
});
updateButtons.forEach(b => {
    b.addEventListener('click', updateClick);
});

async function deleteClick(event) {
    event.preventDefault();
    let answer = confirm("Удалить запись?");
    if (!answer) {
        return;
    }
    let row = event.target.parentElement.parentElement;
    let cells = row.querySelectorAll('td.value-cell');
    cells.forEach(cell => {
        let input = cell.querySelector('input');
        input.disabled = false;
    });
    let formData = new FormData(form);
    let response = await fetch('deleteEntry.php', {method: 'POST', body: formData, headers: {'Accept': 'application/json'}});
    if (response.ok) {
        tableBody.removeChild(event.target.parentElement.parentElement);
        refreshButtons();
    }
}



async function updateClick(event) {
    let target = event.target;
    event.preventDefault();
    enableclick = !enableclick;
    let btns = Array.from(updateButtons);
    btns.splice(btns.indexOf(target), 1);
    btns.forEach(btn => {
        btn.disabled = enableclick;
    });
    deleteButtons.forEach(btn => {
        btn.disabled = enableclick;
    });
    let dataInputs = document.querySelectorAll('.data-input');
    dataInputs.forEach(dataInput => {
        dataInput.enabled = enableclick;
    });
    if (enableclick) {
        let row = target.parentElement.parentElement;
        let cells = row.querySelectorAll('td.value-cell');
        cells.forEach(cell => {
            let input = cell.querySelector('input');
            input.disabled = false;
        });
    }
    else {
        let formData = new FormData(form);
        let response = await fetch('updateEntry.php', {method: 'POST', body: formData, headers: {'Accept': 'application/json'}});
        let row = target.parentElement.parentElement;
        let cells = row.querySelectorAll("td.value-cell");
        if (response.ok) {
            let result = await response.json();
            let arr = Object.values(result);
            for (let index = 0; index < arr.length; index++) {
                let cell = row.children[index];
                let input = cell.children[0];
                input.value = arr[index];
            }
        }
        cells.forEach(cell => {
            let input = cell.querySelector('input');
            input.disabled = true;
        });
    }
}