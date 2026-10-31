// Chin Jia Jun

var fp;

function displayCalendar(dates) {
    if (fp) {
        fp.destroy();
    }

    // Initialize flatpickr
    fp = flatpickr(".datepicker", {
        dateFormat: "D, d-M-Y",
        minDate: new Date().fp_incr(1),         // tomorrow
        maxDate: new Date().fp_incr(360),       // 1 year
        mode: "range",
        showMonths: 2,
        locale: {
            rangeSeparator: "    ->    ",
        },
        disable: dates,
        onClose: function (selectedDates, dateStr, instance) {      
            // Validate date range selection
            if (selectedDates.length === 2) {
                // Ensure check-out date is after check-in date
                if (selectedDates[0].getTime() == selectedDates[1].getTime()) {
                    showSnackbar("Please select an end date to complete the range.");
                    instance.open();
                }
                // Display number of nights
                else {
                    var night = (selectedDates[1].getTime() - selectedDates[0].getTime()) / (1000 * 60 * 60 * 24);
                    document.getElementById('nights').innerText = night;
                }
            }
        },
        onChange: function (selectedDates, dateStr, instance) {
            // Reset room list if already displayed
            if (document.getElementById('room-list').style.display == 'block' || document.getElementById('no-room-msg').style.display == 'block') {
                resetRooms();
                instance.close();
                setTimeout(() => {
                    instance.open();
                }, 300);
            }

            // Enable first disable date after check-in date selection
            if (selectedDates.length === 1) {
                const checkIn = selectedDates[0];
                const firstUnavailable = dates.find(date => date > checkIn);
                const checkInUnavailable = dates.some(date => date.getTime() === checkIn.getTime());

                let tempDates;
                if (!checkInUnavailable && firstUnavailable) {
                    tempDates = dates.filter(date => date.getTime() != firstUnavailable.getTime());
                } else {
                    tempDates = dates;
                }
                instance.set('disable', tempDates);
            }
        }
    });
}

// Fetch fully booked dates based on paxNo
async function getFullyBookedDates() {
    paxNo = parseInt(document.getElementById('adultno').value) + parseInt(document.getElementById('childno').value);
    document.getElementById('dates').value = "";

    try {
        const response = await fetch(`/api/fullyBookedDates?paxNo=${paxNo}`);
        if (!response.ok) {
            console.error(`Error ${response.status}: ${response.statusText}`);
            showSnackbar("Something went wrong. Please try again");
            return;
        }

        var dates = await response.json();
        dates = dates.map(date => new Date(date));

        displayCalendar(dates);
    } catch (error) {
        console.error('Failed to load fully booked dates:', error);
        showSnackbar("Failed to load fully booked dates. Please try again");
    }
}

document.addEventListener('DOMContentLoaded', () => {
    getFullyBookedDates();
});

// Update paxNo and reset room list if already displayed
function updatePaxNo(inputId, change) {
    const input = document.getElementById(inputId);
    const newPax = parseInt(input.value) + change;

    if (newPax >= parseInt(input.min) && newPax <= parseInt(input.max)) {
        input.value = newPax;
        getFullyBookedDates();
    }

    if (document.getElementById('room-list').style.display == 'block' || document.getElementById('no-room-msg').style.display == 'block') {
        resetRooms();
    }
}

document.getElementById('minus-adult').addEventListener('click', () => {
    updatePaxNo('adultno', -1);
});

document.getElementById('plus-adult').addEventListener('click', () => {
    updatePaxNo('adultno', 1);
});

document.getElementById('minus-child').addEventListener('click', () => {
    updatePaxNo('childno', -1);
});

document.getElementById('plus-child').addEventListener('click', () => {
    updatePaxNo('childno', 1);
});

// Fetch available room types based on selected dates and paxNo
async function getAvailableRoomType(startDate, endDate, paxNo) {
    try {
        const response = await fetch(`/api/roomTypesToBook?startDate=${startDate}&endDate=${endDate}&paxNo=${paxNo}`);
        if (!response.ok) {
            console.error(`Error ${response.status}: ${response.statusText}`);
            showSnackbar("Something went wrong. Please try again");
            return;
        }

        var roomTypes = await response.json();
        return roomTypes;
    } catch (error) {
        console.error('Failed to load available room types:', error);
        showSnackbar("Failed to load available room types. Please try again");
    }
}

// Display available room types
function displayRoomTypes(roomTypes) {
    const roomTypeList = document.getElementById('room-list');
    roomTypeList.innerHTML = "";

    roomTypes.forEach(roomType => {
        const roomTypeContainer = document.createElement('div');
        roomTypeContainer.classList.add('room');
        roomTypeContainer.innerHTML = `
            <img src="${roomType.imageUrl}" alt="${roomType.name}">
            <div class="room-desc">
                <h2>${roomType.name}</h2>
                <h3><span id="price">RM${roomType.price}</span> per night</h3>
                <table>
                    <tr class="desc-label">
                        <td>Size</td>
                        <td>Bed Type</td>
                        <td>Max occupancy</td>
                    </tr>
                    <tr class="desc-detail">
                        <td>${roomType.roomAmenities['Size']}</td>
                        <td>${roomType.roomAmenities['Bed']}</td>
                        <td>${roomType.paxNo} <span class='fas fa-user'></span></td>
                    </tr>
                </table>

                <span class="details-link">View Details &nbsp;<i class='fas fa-arrow-alt-circle-right'></i></span>

                <form id="add-to-cart-form" action="/cart/add" method="POST">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="startDate" id="startDate" value="">
                    <input type="hidden" name="endDate" id="endDate" value="">
                    <input type="hidden" name="paxNo" id="paxNo" value="">
                    <input type="hidden" name="room_id" id="room_id" value="">
                    <button type="submit">Book Now</button>
                </form>
            </div>
        `;

        roomTypeList.appendChild(roomTypeContainer);

        // Add event listeners for details link and book now button
        const detailsLink = roomTypeContainer.querySelector('.details-link');
        detailsLink.addEventListener('click', () => {
            displayDetailModal(roomType);
        });

        const button = roomTypeContainer.querySelector('form').querySelector('button');

        button.addEventListener('click', function (event) {
            event.preventDefault();
            addToCart(roomType.id, this);
        });
    });
}

// Handle booking form submission
document.getElementById('bookingform').addEventListener('submit', async function (e) {
    e.preventDefault();

    // Validate date selection
    if (fp.selectedDates.length != 2) {
        showSnackbar("Please select check-in date & check-out date!");
        document.getElementById('dates').style.borderColor = 'red';
        fp.open();
        return;
    }

    paxNo = parseInt(document.getElementById('adultno').value) + parseInt(document.getElementById('childno').value);
    const roomTypes = await getAvailableRoomType(formatDateToYMD(fp.selectedDates[0]), formatDateToYMD(fp.selectedDates[1]), paxNo);

    if (roomTypes.length > 0) {
        displayRoomTypes(roomTypes);
        document.getElementById('room-list').style.display = 'block';
        document.getElementById('no-room-msg').style.display = 'none';
    }
    else {
        document.getElementById('room-list').style.display = 'none';
        document.getElementById('no-room-msg').style.display = 'block';
    }

    const bookingForm = document.getElementById('bookingform');
    bookingForm.style.scrollMarginTop = "75px";
    bookingForm.scrollIntoView({ behavior: 'smooth' });
});

function formatDateToYMD(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Display room detail modal
function displayDetailModal(roomType) {
    const modal = document.getElementById('room-detail-modal');
    modal.style.display = 'flex';
    document.body.style.overflow = "hidden";

    modal.getElementsByTagName('img')[0].src = `${roomType.imageUrl}`;
    modal.getElementsByTagName('img')[0].alt = `${roomType.name}`;
    document.getElementById('room-detail-name').innerHTML = `${roomType.name}`;
    document.getElementById('room-detail-pax').innerHTML = `${roomType.paxNo}`;
    document.getElementById('room-detail-price').innerHTML = `${roomType.price}`;
    document.getElementById('room-detail-desc').innerHTML = `${roomType.description}`;

    const amenitiesTable = document.getElementById('detail-amenities').getElementsByTagName('table')[0];
    const amenities = Object.entries(roomType.roomAmenities || {}).map(([label, value]) => ({ label, value }));
    amenitiesTable.innerHTML = "";

    var row = null;
    for (let i = 0; i < amenities.length; i++) {

        if (i % 2 == 0) {
            row = document.createElement('tr');
        }
        const labelTd = document.createElement('td');
        const valueTd = document.createElement('td');

        labelTd.classList.add('detail-amenities-label');
        labelTd.innerHTML = `${amenities[i].label}`;
        valueTd.innerHTML = `${amenities[i].value}`;
        row.appendChild(labelTd);
        row.appendChild(valueTd);

        if (i % 2 != 0) {
            amenitiesTable.appendChild(row);
            row = null;
        }
    }

    if (row !== null) {
        amenitiesTable.appendChild(row);
    }

    modal.getElementsByTagName('button')[0].onclick = function (event) {
        event.preventDefault();
        addToCart(roomType.id, this);
    };
}

// Close modal 
function closeDetailModal() {
    const modal = document.getElementById('room-detail-modal');
    modal.style.display = 'none';
    document.body.style.overflow = "auto";
}

// Reset room list display
function resetRooms() {
    document.getElementById('room-list').style.display = 'none';
    document.getElementById('no-room-msg').style.display = 'none';
    const bookingScene = document.getElementById('bookingscene');
    bookingScene.style.scrollMarginTop = "75px";
    bookingScene.scrollIntoView({ behavior: 'smooth' });
}

// Fetch an available room based on roomTypeId and selected dates
async function getRoomByRoomType(roomTypeId, startDate, endDate) {
    try {
        const roomResponse = await fetch(`/api/room/availableRoom?roomTypeId=${roomTypeId}&startDate=${startDate}&endDate=${endDate}`);
        if (!roomResponse.ok) {
            console.error(`Error ${roomResponse.status}: ${roomResponse.statusText}`);
            showSnackbar("Something went wrong. Please try again");
            return;
        }

        const response = await roomResponse.json();

        if (response.status !== 'success' || !response.room) {
            showSnackbar("Room not found. Please try again");
            return null;
        }

        return response.room;
    } catch (error) {
        console.error('Failed to get room:', error);
        showSnackbar("Failed to get room. Please try again");
    }
}

// Handle adding room to cart
async function addToCart(roomTypeId, button) {
    const startDate = formatDateToYMD(fp.selectedDates[0]);
    const endDate = formatDateToYMD(fp.selectedDates[1]);
    const paxNo = parseInt(document.getElementById('adultno').value) + parseInt(document.getElementById('childno').value);

    try {
        const room = await getRoomByRoomType(roomTypeId, startDate, endDate);
        if (room === null || room === undefined) {
            showSnackbar("Room not found. Please try again");
            location.reload();
            return;
        }
        else {
            const form = button.parentElement;
            form.querySelector('input[name="startDate"]').value = startDate;
            form.querySelector('input[name="endDate"]').value = endDate;
            form.querySelector('input[name="paxNo"]').value = paxNo;
            form.querySelector('input[name="room_id"]').value = room.id;
            form.submit();
        }
    } catch (error) {
        console.error('Failed to add to cart:', error);
        showSnackbar("Failed to add to cart. Please try again");
    }
}