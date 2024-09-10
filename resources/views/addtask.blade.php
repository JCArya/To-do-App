<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        .container {
            max-width: 900px;
        }
        input.form-control {
            height: 50px;
            font-size: 1.2rem;
        }
        table {
            width: 100%;
            font-size: 1.2rem;
        }
        td, th {
            white-space: nowrap;
        }
        tbody {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">To-Do List</h1>

        <div class="input-group mb-3">
            <input type="text" id="taskInput" class="form-control" placeholder="Enter task">
            <button id="addTaskBtn" class="btn btn-primary btn-lg">Add Task</button>
        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>S.No</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="taskList"></tbody>
        </table>

        <button id="showAllBtn" class="btn btn-secondary btn-lg">Show All Tasks</button>
    </div>

    <script>
        const taskInput = document.getElementById('taskInput');
        const taskList = document.getElementById('taskList');
        const showAllBtn = document.getElementById('showAllBtn');
        let allTasks = [];

        const loadTasks = () => {
            axios.get('/api/tasks')
                .then(response => {
                    allTasks = response.data;
                    showAllBtn.style.display = 'block'; // Ensure the button is visible
                });
        };

        const displayTasks = (tasks) => {
            taskList.innerHTML = '';
            tasks.forEach((task, index) => addTaskToList(task, index + 1));
        };

        const addTaskToList = (task, index) => {
            const row = document.createElement('tr');
            const taskStatus = task.is_completed ? 'Done' : 'Pending';
            const taskIcon = task.is_completed ? 'fas fa-check-square text-success' : 'far fa-square';

            row.innerHTML = `
            <td>${index}</td>
            <td>${task.title}</td>
            <td id="status-${task.id}">${taskStatus}</td>
            <td>
                <button class="btn btn-link p-2" onclick="toggleTask(${task.id}, ${task.is_completed})">
                    ${task.is_completed ? '' : `<i class="${taskIcon}" style="font-size: 24px; width: 30px; height: 30px;"></i>`}
                </button>
                <button class="btn btn-link text-danger p-2" onclick="deleteTask(${task.id})">
                    <i class="fas fa-delete-left" style="font-size: 24px; width: 30px; height: 30px;"></i>
                </button>
            </td>
            `;
            taskList.appendChild(row);
        };

        document.getElementById('addTaskBtn').addEventListener('click', () => {
            const title = taskInput.value.trim();
            if (title) {
                axios.post('/api/tasks', { title })
                    .then(response => {
                        if (response.data) {
                            allTasks.push(response.data);
                            taskInput.value = '';
                            showAllBtn.style.display = 'block'; // Ensure the button is visible after adding a task
                        }
                    })
                    .catch(error => alert(error.response?.data?.message || 'An unexpected error occurred.'));
            } else {
                alert("Please enter a task.");
            }
        });

        showAllBtn.addEventListener('click', () => {
            if (allTasks.length > 0) {
                displayTasks(allTasks);
                showAllBtn.style.display = 'none'; // Hide the button after displaying tasks
            }
        });

        window.toggleTask = (id, isCompleted) => {
            axios.put(`/api/tasks/${id}`, { is_completed: !isCompleted })
                .then(response => {
                    const statusCell = document.getElementById(`status-${id}`);
                    statusCell.innerText = response.data.is_completed ? 'Done' : 'Pending';
                    const icon = statusCell.nextElementSibling.querySelector('i');
                    icon?.remove();
                });
        };

        window.deleteTask = (id) => {
            if (confirm('Are you sure you want to delete this task?')) {
                axios.delete(`/api/tasks/${id}`)
                    .then(() => {
                        allTasks = allTasks.filter(task => task.id !== id);
                        displayTasks(allTasks);
                    });
            }
        };

        loadTasks();
    </script>
</body>
</html>
