document.addEventListener('DOMContentLoaded', () => {
    const popup = document.getElementById('popup');
    const openPopup = document.getElementById('openPopup');
    const closePopup = document.querySelector('.close');
    const studentForm = document.getElementById('studentForm');
    const studentTable = document.getElementById('studentTable');
    const errorDiv = document.createElement('div');

    openPopup.onclick = function() {
        clearForm();
        popup.style.display = "flex";
    }

    closePopup.onclick = function() {
        popup.style.display = "none";
    }

    if (window.location.hash === '#addStudent') {
        popup.style.display = "flex";
    }

    window.onclick = function(event) {
        if (event.target == popup) {
            popup.style.display = "none";
        }
    }

    studentForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const formData = new FormData(studentForm);

        fetch('/addStudent', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStudentList(data.student);
                popup.style.display = 'none';
            } else if (data.error) {
                showError(data.error);
            }
        })
        .catch(error => {
            showError(error);
        });
    });

    function clearForm() {
        studentForm.reset();
        errorDiv.innerHTML = '';
    }

    function showError(message) {
        errorDiv.classList.add('error-message');
        errorDiv.innerHTML = message;
        studentForm.prepend(errorDiv);
    }

    function updateStudentList(student) {
        let existingRow = document.querySelector(`[data-name="${CSS.escape(student.name)}"][data-email="${CSS.escape(student.email)}"][data-subject_code="${CSS.escape(student.subject_code)}"]`);

        if (existingRow) {
            existingRow.querySelector('.student-marks').textContent = student.mark;
        } else {
            var noDataMessage = document.getElementById('no_data_message');
            if (noDataMessage) {
                noDataMessage.style.display = 'none';
            }
            const newRow = document.createElement('tr');
            newRow.setAttribute('data-name', student.name);
            newRow.setAttribute('data-email', student.email);
            newRow.setAttribute('data-subject_code', student.subject_code);

            newRow.innerHTML = `
                <td>${student.name}</td>
                <td>${student.email}</td>
                <td>${student.subject_name}</td>
                <td class="student-marks">${student.mark}</td>
                <td>
                    <form method="GET" action="edit-student">
                            <input type="hidden" id="student_id" name="student_id" value="${student.id}" >
                            <input type="submit" class="btn" value="Edit">
                    </form><br>
                    <a href="/delete-student?id=${student.id}" id='delete' onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            `;

            studentTable.appendChild(newRow);
        }
    }
});

function validateMark(input) {
    var value = parseFloat(input.value);
    input.value = value.toFixed(2);
}
