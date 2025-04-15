document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('togglePassword').addEventListener('click', function () {
        togglePasswordVisibility('password', 'togglePassword');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
        togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword');
    });

    function togglePasswordVisibility(fieldId, iconId) {
        var field = document.getElementById(fieldId);
        var icon = document.getElementById(iconId);

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    function validatePassword() {
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirmPassword');

        if (passwordField.value !== confirmPasswordField.value) {
            confirmPasswordField.setCustomValidity("Passwords do not match");
        } else {
            confirmPasswordField.setCustomValidity("");
        }
    }

    document.getElementById('password').addEventListener('change', validatePassword);
    document.getElementById('confirmPassword').addEventListener('keyup', validatePassword);
});

function openAddUserModal() {
    document.getElementById('addUserModal').style.display = 'block';
}

function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
}

function validateAddUserForm() {
    var form = document.querySelector('#addUserModal form');
    if (form.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
    return form.checkValidity();
}

// Edit User Form Validation
function validateEditUserForm() {
    var form = document.querySelector('#editUserModal_ form');
    if (form.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
    return form.checkValidity();
}

function displaySelectedImage(input, userId) {
    const imgElement = document.getElementById('editUserImageDisplay_' + userId);

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            console.log('File path:', e.target.result); // Log the file path to the console
            imgElement.src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        imgElement.src = '';
    }
}

function clearModalContent(userId) {
    const imgElement = document.getElementById('editUserImageDisplay_' + userId);
    const fileInput = document.getElementById('editUserImage_' + userId);

    // Clear the image preview
    imgElement.src = '';

    // Reset the file input (clear selected file)
    fileInput.value = '';
}

document.addEventListener("DOMContentLoaded", function () {
    // Function to perform search
    function searchTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector("table");
        tr = table.getElementsByTagName("tr");

        var noResultsRow = document.getElementById("noResultsRow");

        // Remove the "No results" row if it exists
        if (noResultsRow) {
            noResultsRow.remove();
        }

        var resultsFound = false; // Flag to check if any matching rows are found

        for (i = 0; i < tr.length; i++) {
            var found = false; // Flag to check if at least one field matches the search criteria

            // Skip the header row
            if (i === 0) {
                continue;
            }

            // Check each column in a row for a match
            for (var j = 0; j < tr[i].cells.length; j++) {
                td = tr[i].cells[j];
                if (td) {
                    txtValue = td.textContent || td.innerText;

                    // Check if the current column contains the search criteria
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break; // Exit the loop if a match is found in any column
                    }
                }
            }

            // Show or hide the row based on the search result
            if (found) {
                tr[i].style.display = "";
                resultsFound = true;
            } else {
                tr[i].style.display = "none";
            }
        }

        // Display "No results" message if no matching rows are found
        if (!resultsFound) {
            var noResultsRow = table.insertRow(-1);
            var noResultsCell = noResultsRow.insertCell(0);
            noResultsCell.colSpan = tr[0].cells.length; // Span the cell across all columns
            noResultsCell.innerHTML = "No results";
            noResultsRow.id = "noResultsRow";
            noResultsCell.style.textAlign = "center"; // Center the text
            noResultsCell.style.fontWeight = "bold"; // Make the text bold
            noResultsCell.style.color = "gray"; // Set the text color to red
        }
    }

    // Attach the search function to both button click and Enter key press
    document.getElementById("searchButton").addEventListener("click", function () {
        searchTable();
    });

    document.getElementById("searchInput").addEventListener("keyup", function (event) {
        if (event.key === "Enter") {
            searchTable();
        }
    });
});