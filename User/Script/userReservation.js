document.addEventListener('DOMContentLoaded', function () {
    const continueButton = document.getElementById('continue-button');
    const modal = document.getElementById('myModal');
    const closeModalButton = document.getElementsByClassName('close')[0];
    const confirmReservationButton = document.getElementById('confirm-reservation-btn');
    const reservationDetails = document.getElementById('reservation-details');
    const reservationForm = document.getElementById('reservation-form');
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notification-message');

    continueButton.addEventListener('click', function (event) {
        // Prevent the default form submission
        event.preventDefault();

        // Get reservation details
        const selectedSection = document.getElementById('selected-section').textContent;
        const selectedDate = document.getElementById('date-picker').value;
        const selectedStartTime = document.getElementById('start-time-dropdown').textContent.trim().replace('Start time: ', '');
        const selectedEndTime = document.getElementById('end-time-dropdown').textContent.trim().replace('End time: ', '');
        const selectedSeats = document.getElementById('selected-seats-input').value;

        // Prepare reservation details text with bold formatting, including start and end times
        let detailsText = `<strong>Section:</strong> ${selectedSection}<br>
        <strong>Date:</strong> ${selectedDate}<br>`;

        // Check if start and end times are not the default values
        if (selectedStartTime !== 'Select start time' && selectedEndTime !== 'Select end time') {
            detailsText += `<strong>Start time:</strong> ${selectedStartTime}<br>
             <strong>End time:</strong> ${selectedEndTime}<br>`;
        }

        detailsText += `<strong>Seat/s:</strong> ${selectedSeats}`;

        // Set reservation details in the modal
        reservationDetails.innerHTML = detailsText;

    });

    closeModalButton.addEventListener('click', function () {
        // Close the modal when the close button is clicked
        modal.style.display = 'none';
    });

    confirmReservationButton.addEventListener('click', function () {
        const formData = new FormData(reservationForm);
        formData.append('userId', userId);

        // Perform AJAX request to submit the form data
        fetch('../User/userReservation.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // Check if the reservation was successful
                if (data.success) {
                    // Display success message notification
                    notificationMessage.textContent = 'Reservation successful!';
                    notification.style.display = 'block';

                    resetForm();

                    // Hide the notification after 5 seconds
                    setTimeout(function () {
                        notification.classList.add('show');
                        notification.classList.add('success');
                        setTimeout(function () {
                            notification.style.display = 'none';
                        }, 5000); // You can adjust the timeout value based on your transition duration (300ms in this case)
                    }, 50);
                }
                else {
                    // Display error message received from the server
                    notificationMessage.textContent = data.message;
                    notification.style.display = 'block';
                    notification.classList.add('error'); // Add a class for styling

                    // Check if the user reached the maximum reservation limit
                    if (data.maxReservationReached) {
                        // Display a notification message for reaching the maximum reservation limit
                        notificationMessage.textContent = 'You have reached the maximum reservation limit of 3.';
                        notification.style.display = 'block';

                        // Hide the notification after 5 seconds
                        setTimeout(function () {
                            notification.classList.add('show');
                            notification.classList.add('error');
                            setTimeout(function () {
                                notification.style.display = 'none';
                            }, 5000); // You can adjust the timeout value based on your transition duration (300ms in this case)
                        }, 50);
                    } else if (data.invalidDateTime) {
                        // Display a notification message for invalid date, start time, or end time
                        notificationMessage.textContent = 'Invalid date, start time, or end time. Please check your selection.';
                        notification.style.display = 'block';

                        // Hide the notification after 5 seconds
                        setTimeout(function () {
                            notification.classList.add('show');
                            notification.classList.add('error');
                            setTimeout(function () {
                                notification.style.display = 'none';
                            }, 5000); // You can adjust the timeout value based on your transition duration (300ms in this case)
                        }, 50);
                    }
                }
            })
            .catch(error => {
                // Handle other errors that might occur during the AJAX request
                notificationMessage.textContent = 'An error occurred. Please try again later.';
                notification.style.display = 'block';
                notification.classList.add('error'); // Add a class for styling
            });

        // Close the modal after clicking the "Confirm Reservation" button
        modal.style.display = 'none';
    });

    function resetForm() {
        // Reset input fields, dropdowns, and selected seats
        document.getElementById('reservation-form').reset();
        document.getElementById('selected-section').textContent = 'Select Section';
        document.getElementById('selected-section').style.color = ''; // Reset color to default

        // Reset the start time dropdown
        const startTimeDropdown = document.getElementById('start-time-dropdown');
        startTimeDropdown.innerHTML = `<span class="dropdown-icon"><i class="fas fa-caret-down"></i></span>Select start time`;
        startTimeDropdown.style.color = ''; // Reset color to default

        // Reset the end time dropdown
        const endTimeDropdown = document.getElementById('end-time-dropdown');
        endTimeDropdown.innerHTML = `<span class="dropdown-icon"><i class="fas fa-caret-down"></i></span>Select end time`;
        endTimeDropdown.style.color = ''; // Reset color to default

        // Clear date picker value
        const datePicker = document.getElementById('date-picker');
        datePicker._flatpickr.clear(); // Clear the date picker value

        const selectedSeats = document.querySelectorAll('.chair.selected');
        selectedSeats.forEach(seat => seat.classList.remove('selected'));

        const diningTablesContainer = document.getElementById('dining-tables-container');
        diningTablesContainer.innerHTML = '';

        // Reset the color of the dropdown icons
        const dropdownIcons = document.querySelectorAll('.dropdown-icon');
        dropdownIcons.forEach(icon => icon.style.color = ''); // Reset color to default

        // Hide the continue button after resetting the form
        const continueButton = document.querySelector('.continue-button');
        continueButton.style.display = 'none';
    }

    // Close the modal if the user clicks outside of it
    window.addEventListener('click', function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const continueButton = document.getElementById('continue-button');
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notification-message');

    // Add click event listener to the continue button
    continueButton.addEventListener('click', function (event) {
        const selectedSection = document.getElementById('selected-section').textContent;
        const selectedDate = document.getElementById('date-picker').value;
        const selectedStartTime = document.getElementById('start-time-dropdown').textContent.trim();
        const selectedEndTime = document.getElementById('end-time-dropdown').textContent.trim();
        const selectedSeats = document.querySelectorAll('.chair.selected');

        // Check if all required details are filled out
        if (selectedSection && selectedDate && selectedStartTime && selectedEndTime && selectedSeats.length > 0 && selectedStartTime !== 'Select start time' && selectedEndTime !== 'Select end time') {
            // Format date in YYYY-MM-DD
            const formattedDate = new Date(selectedDate);
            const formattedDateString = formattedDate.toISOString().split('T')[0];

            // Format start time and end time
            const selectStartTimePrefix = 'Start time: ';
            const selectEndTimePrefix = 'End time: ';
            const formattedStartTime = selectedStartTime.startsWith(selectStartTimePrefix) ? selectedStartTime.slice(selectStartTimePrefix.length) : selectedStartTime;
            const formattedEndTime = selectedEndTime.startsWith(selectEndTimePrefix) ? selectedEndTime.slice(selectEndTimePrefix.length) : selectedEndTime;

            // Get selected seat numbers
            const selectedSeatNumbers = Array.from(selectedSeats).map(chair => chair.dataset.seatNumber);
            const selectedSeatsString = selectedSeatNumbers.join(',');

            // Update hidden input fields with formatted selected data before submitting the form
            document.getElementById('selected-section-input').value = selectedSection;
            document.getElementById('selected-date-input').value = formattedDateString;
            document.getElementById('selected-start-time-input').value = formattedStartTime;
            document.getElementById('selected-end-time-input').value = formattedEndTime;
            document.getElementById('selected-seats-input').value = selectedSeatsString;

            // Display the modal
            const modal = document.getElementById('myModal');
            modal.style.display = 'block';
        } else {
            // Show custom notification if all details are not filled out
            notificationMessage.textContent = 'Please fill out all the details before confirming your reservation.';
            notification.style.display = 'block';

            // Hide the notification after 5 seconds
            setTimeout(function () {
                notification.classList.add('show');
                notification.classList.add('error');
                setTimeout(function () {
                    notification.style.display = 'none';
                }, 5000); // You can adjust the timeout value based on your transition duration (300ms in this case)
            }, 50); // Delay before adding 'show' class (50ms in this case)
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Room Dropdown
    const roomDropdown = document.getElementById('room-dropdown');
    const roomOptions = document.getElementById('room-options');
    const selectedSectionText = document.getElementById('selected-section');
    const dropdownIcon = document.querySelector('.dropdown-icon');
    const startTimeDropdown = document.getElementById('start-time-dropdown');
    const endTimeDropdown = document.getElementById('end-time-dropdown');

    roomDropdown.addEventListener('click', function (event) {
        if (roomOptions.style.display === 'block') {
            roomOptions.style.display = 'none';
        } else {
            roomOptions.style.display = 'block';
        }
        event.stopPropagation();
    });

    document.addEventListener('click', function (event) {
        if (event.target !== roomDropdown && event.target !== selectedSectionText) {
            roomOptions.style.display = 'none';
        }
    });

    roomOptions.addEventListener('click', function (event) {
        const selectedSection = event.target.textContent;
        const selectedDate = datePickerInput.value;
        selectedSectionText.textContent = selectedSection;
        const formattedStartTime = startTimeDropdown.textContent.trim();
        const formattedEndTime = endTimeDropdown.textContent.trim();
        selectedSectionText.style.color = '#555';
        dropdownIcon.style.color = '#555';
        roomOptions.style.display = 'none';

        generateDiningTables(selectedSection, selectedDate, formattedStartTime, formattedEndTime);
    });

    // Date Dropdown
    const dateDropdown = document.getElementById('date-dropdown');
    const datePickerInput = dateDropdown.querySelector('#date-picker');
    let isDateSelected = false;

    const holidays = [
        new Date("2023-11-01"),
        new Date("2023-11-02"), // Example holiday date
        // Add more holiday dates as needed
    ];

    const datepicker = flatpickr(datePickerInput, {
        dateFormat: "Y-m-d",
        minDate: "today",
        maxDate: new Date().fp_incr(30),
        disable: [
            function (date) {
                return (date.getDay() === 6 || date.getDay() === 0);
            },
            ...holidays
        ],
        onChange: function (selectedDates) {
            isDateSelected = selectedDates.length > 0;
            updateDropdownIconColor();

            const selectedDate = isDateSelected ? formatDate(selectedDates[0]) : null;
            const selectedSection = selectedSectionText.textContent.trim();
            const formattedStartTime = selectedStartTime;
            const formattedEndTime = endTimeDropdown.textContent.trim();

            if (selectedDate && selectedSection) {
                generateDiningTables(selectedSection, selectedDate, formattedStartTime, formattedEndTime);
            }
        },

        onValueUpdate: function (selectedDates) {
            const selectedDateElement = document.querySelector('.flatpickr-day.selected');
            const allSelectedDates = document.querySelectorAll('.flatpickr-day.selected');
            allSelectedDates.forEach(date => {
                date.style.backgroundColor = '';
                date.style.color = '';
                date.style.border = '';
            });

            selectedDateElement.style.backgroundColor = '#112B3C';
            selectedDateElement.style.color = '#fff';
            selectedDateElement.style.border = 'none';
        }
    });

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    dateDropdown.addEventListener('click', function (event) {
        event.stopPropagation();
        datepicker.open();
    });

    document.addEventListener('click', function (event) {
        if (!dateDropdown.contains(event.target)) {
            datepicker.close();
            updateDropdownIconColor();
        }
    });

    function updateDropdownIconColor() {
        if (isDateSelected) {
            dateDropdown.querySelector('.dropdown-icon').style.color = '#555';
        } else {
            dateDropdown.querySelector('.dropdown-icon').style.color = '';
        }
    }

    datepicker.calendarContainer.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    // Dining Tables Functionality
    function generateDiningTables(selectedSection, selectedDate, formattedStartTime, formattedEndTime) {
        const diningTablesContainer = document.getElementById('dining-tables-container');
        diningTablesContainer.innerHTML = ''; // Clear previous dining tables

        let numberOfTables = 0;
        switch (selectedSection) {
            // Define the number of dining tables based on the selected section
            case 'Tech4ed Center':
                numberOfTables = 2;
                break;
            case 'Circulation Section':
                numberOfTables = 1;
                break;
            case 'Reference Section':
                numberOfTables = 2;
                break;
            case 'Bulacaniana Section':
                numberOfTables = 2;
                break;
            case 'Filipiniana Section':
                numberOfTables = 2;
                break;
            default:
                numberOfTables = 0;
                break;
        }

        // Generate dining tables and seats
        for (let i = 1; i <= numberOfTables; i++) {
            const tableContainer = document.createElement('div');
            tableContainer.className = 'table-container';
            diningTablesContainer.appendChild(tableContainer);

            const leftChairsContainer = document.createElement('div');
            leftChairsContainer.className = 'chairs-container2 left-chairs';
            tableContainer.appendChild(leftChairsContainer);

            // Create left chairs
            for (let j = 1; j <= 1; j++) {
                const leftChair = document.createElement('div');
                leftChair.className = 'chair';
                const seatNumber = `${selectedSection.charAt(0)}-T${i}-L${j}`; // Example seat number format: C-T1-L1
                leftChair.setAttribute('data-seat-number', seatNumber); // Assign seat number as data attribute
                leftChairsContainer.appendChild(leftChair);
            }

            const topChairsContainer = document.createElement('div');
            topChairsContainer.className = 'chairs-container top-chairs';
            tableContainer.appendChild(topChairsContainer);

            // Create top chairs
            for (let j = 1; j <= 8; j++) {
                const topChair = document.createElement('div');
                topChair.className = 'chair';
                const seatNumber = `${selectedSection.charAt(0)}-T${i}-${j}`; // Example seat number format: C-T1-16
                topChair.setAttribute('data-seat-number', seatNumber); // Assign seat number as data attribute
                topChairsContainer.appendChild(topChair);
            }

            const table = document.createElement('div');
            table.className = 'dining-table';
            table.textContent = `Table`;
            tableContainer.appendChild(table);

            const bottomChairsContainer = document.createElement('div');
            bottomChairsContainer.className = 'chairs-container bottom-chairs';
            tableContainer.appendChild(bottomChairsContainer);

            // Create bottom chairs
            for (let j = 1; j <= 8; j++) {
                const bottomChair = document.createElement('div');
                bottomChair.className = 'chair';
                const seatNumber = `${selectedSection.charAt(0)}-T${i}-${j + 8}`; // Example seat number format: C-T1-16
                bottomChair.setAttribute('data-seat-number', seatNumber); // Assign seat number as data attribute
                bottomChairsContainer.appendChild(bottomChair);
            }

            const rightChairsContainer = document.createElement('div');
            rightChairsContainer.className = 'chairs-container2 right-chairs';
            tableContainer.appendChild(rightChairsContainer);

            // Create right chairs
            for (let j = 1; j <= 1; j++) {
                const rightChair = document.createElement('div');
                rightChair.className = 'chair';
                const seatNumber = `${selectedSection.charAt(0)}-T${i}-R${j}`; // Example seat number format: C-T1-R1
                rightChair.setAttribute('data-seat-number', seatNumber); // Assign seat number as data attribute
                rightChairsContainer.appendChild(rightChair);
            }
        }

        const chairs = document.querySelectorAll('.chair');
        const continueButton = document.querySelector('.continue-button');
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notification-message');

        const url = `../User/func/getOccupiedSeats.php?selectedDate=${selectedDate}&startTime=${formattedStartTime}&endTime=${formattedEndTime}`;

        fetch(url)

            .then(response => response.json())
            .then(data => {
                const occupiedSeats = data.occupiedSeats.flatMap(seat => seat.seatNumber.split(','));
                chairs.forEach(chair => {
                    const seatNumber = chair.dataset.seatNumber;
                    if (occupiedSeats.includes(seatNumber)) {
                        chair.classList.add('occupied');
                        chair.disabled = true;
                    }
                });
            })
            .catch(error => {

            });

        // Add click event listener to each chair
        chairs.forEach(chair => {
            chair.addEventListener('click', () => {
                // Check if the chair is occupied
                if (chair.classList.contains('occupied')) {
                    // Display error message notification
                    notificationMessage.textContent = 'This seat is already occupied.';
                    notification.style.display = 'block';

                    // Hide the notification after 5 seconds
                    setTimeout(function () {
                        notification.classList.add('show');
                        notification.classList.add('error');
                        setTimeout(function () {
                            notification.style.display = 'none';
                        }, 5000);
                    }, 50);
                    return; // Exit the function and do not proceed further
                }

                // Toggle the 'selected' class when a chair is clicked
                chairs.forEach(c => c.classList.remove('selected'));
                chair.classList.add('selected');

                // Update the hidden input field with selected seat number
                const selectedSeatsInput = document.getElementById('selected-seats-input');
                selectedSeatsInput.value = chair.dataset.seatNumber;

                // Display continue button
                continueButton.style.display = 'block';
            });
        });
    }

    const startTimeOptions = document.getElementById('start-time-options');
    const endTimeOptions = document.getElementById('end-time-options');

    const now = new Date();
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();

    const availableTimes = [
        '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM',
        '12:00 PM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM'
    ];

    const openingTime = '8:00 AM';
    const closingTime = '4:00 PM';

    // Filter out times that have already passed for the current day
    const currentTimeIndex = currentHour * 60 + currentMinute;
    const filteredTimes = availableTimes.filter(time => {
        const [hours, minutes, period] = time.match(/(\d+):(\d+) (AM|PM)/).slice(1);
        const timeInMinutes = (period === 'AM' ? parseInt(hours) : parseInt(hours) + 12) * 60 + parseInt(minutes);
        return timeInMinutes >= currentTimeIndex && time !== closingTime;
    });

    const filteredTimes2 = availableTimes.filter(time => {
        const [hours, minutes, period] = time.match(/(\d+):(\d+) (AM|PM)/).slice(1);
        const timeInMinutes = (period === 'AM' ? parseInt(hours) : parseInt(hours) + 12) * 60 + parseInt(minutes);
        return timeInMinutes >= currentTimeIndex && time !== openingTime;
    });

    // Populate start time options dropdown
    filteredTimes.forEach(time => {
        const timeOption = document.createElement('div');
        timeOption.className = 'dropdown-item';
        timeOption.textContent = time;
        timeOption.addEventListener('click', function () {
            selectStartTime(time);
        });
        startTimeOptions.appendChild(timeOption);
    });

    // Populate end time options dropdown
    filteredTimes2.forEach(time => {
        const timeOption = document.createElement('div');
        timeOption.className = 'dropdown-item';
        timeOption.textContent = time;
        timeOption.addEventListener('click', function () {
            selectEndTime(time);
        });
        endTimeOptions.appendChild(timeOption);
    });

    let selectedStartTime = null;
    let selectedEndTime = null;

    function selectStartTime(startTime) {
        // Additional logic to check if the selected start time is not later than closing time
        if (compareTimes(startTime, closingTime) < 0) {
            selectedStartTime = startTime;
            const dropdownButton = document.getElementById('start-time-dropdown');
            const dropdownIcon = dropdownButton.querySelector('.dropdown-icon');
            dropdownButton.innerHTML = `<span class="dropdown-icon"><i class="fas fa-caret-down" style="color: #555;"></i></span> Start time: ${selectedStartTime}`;
            dropdownButton.style.color = '#555';
            dropdownIcon.style.color = '#555';
            startTimeOptions.style.display = 'none';

            updateSelectedDateTime();
        } else {
            // Display an error message or handle as appropriate
            console.log('Selected start time exceeds or equals closing time.');
        }
    }

    function selectEndTime(endTime) {
        // Check if end time is not lower than start time
        if (compareTimes(endTime, selectedStartTime) > 0) {
            selectedEndTime = endTime;
            const dropdownButton = document.getElementById('end-time-dropdown');
            const dropdownIcon = dropdownButton.querySelector('.dropdown-icon');

            if (selectedEndTime) {
                dropdownButton.innerHTML = `<span class="dropdown-icon"><i class="fas fa-caret-down" style="color: #555;"></i></span> End time: ${selectedEndTime}`;
                dropdownButton.style.color = '#555'; // Set the text color to #555
                dropdownIcon.style.color = '#555'; // Set dropdown icon color to #555
            } else {
                dropdownButton.innerHTML = `<span class="dropdown-icon"><i class="fas fa-caret-down"></i></span> Select end time`;
                dropdownButton.style.color = ''; // Reset the text color
                dropdownIcon.style.color = ''; // Reset dropdown icon color
            }

            endTimeOptions.style.display = 'none';

            updateSelectedDateTime();
        } else {
            // Display an error message or handle as appropriate
            const notificationMessage = document.getElementById('notification-message');
            const notification = document.getElementById('notification');

            notificationMessage.textContent = 'End time must be later than start time.';
            notification.style.display = 'block';

            // Hide the notification after 5 seconds
            setTimeout(function () {
                notification.classList.add('show');
                notification.classList.add('error');
                setTimeout(function () {
                    notification.style.display = 'none';
                }, 5000);
            }, 50);
        }
    }

    function updateSelectedDateTime() {
        const selectedDate = datePickerInput.value;
        const selectedSection = selectedSectionText.textContent.trim();
        const formattedStartTime = selectedStartTime;
        const formattedEndTime = selectedEndTime || ''; // Replace 'DefaultEndTime' with an actual default value

        // Check if the required values are available
        if (selectedDate && selectedSection && formattedStartTime) {
            // Call the function to generate dining tables based on the selected options
            generateDiningTables(selectedSection, selectedDate, formattedStartTime, formattedEndTime);
        }
    }

    function compareTimes(time1, time2) {
        let [hours1, minutes1, period1] = time1.match(/(\d+):(\d+) (AM|PM)/).slice(1);
        let [hours2, minutes2, period2] = time2.match(/(\d+):(\d+) (AM|PM)/).slice(1);

        if (period1 === 'PM' && period2 === 'AM') {
            return 1;
        } else if (period1 === 'AM' && period2 === 'PM') {
            return -1;
        } else if (parseInt(hours1) === 12 && period1 === 'PM') {
            hours1 = 0; // Convert 12 PM to 0 for proper comparison
        } else if (parseInt(hours2) === 12 && period2 === 'PM') {
            hours2 = 0; // Convert 12 PM to 0 for proper comparison
        }

        if (parseInt(hours1) !== parseInt(hours2)) {
            return parseInt(hours1) - parseInt(hours2);
        } else {
            return parseInt(minutes1) - parseInt(minutes2);
        }
    }

    startTimeDropdown.addEventListener('click', function (event) {
        toggleDropdown(startTimeOptions);
        event.stopPropagation();
    });

    endTimeDropdown.addEventListener('click', function (event) {
        toggleDropdown(endTimeOptions);
        event.stopPropagation();
    });

    // Close the dropdowns when clicking outside of them
    document.addEventListener('click', function (event) {
        if (event.target !== startTimeDropdown && event.target !== endTimeDropdown) {
            startTimeOptions.style.display = 'none';
            endTimeOptions.style.display = 'none';
        }
    });

    function toggleDropdown(dropdown) {
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        } else {
            dropdown.style.display = 'block';
        }
    }
});