const images = document.getElementsByClassName("slideshow");
const dots = document.getElementsByClassName('dot');
var currentIndex = 0;
var timer;
const roomsContainer = document.getElementById('rooms');
var totalRoom;
var roomCount = 2;

function updateImage(newIndex) {
    images[currentIndex].classList.remove("active");
    dots[currentIndex].style.backgroundColor = "lightgrey";

    currentIndex = newIndex;

    images[currentIndex].classList.add("active");
    dots[currentIndex].style.backgroundColor = "white";
}

function changeImage() {
    updateImage((currentIndex + 1) % images.length);
}

function chooseImage(index) {
    clearInterval(timer);
    updateImage(index);
    timer = setInterval(changeImage, 2000);
}

for (let i = 0; i < dots.length; i++) {
    dots[i].addEventListener("click", () => chooseImage(i));
}

function displayRooms(rooms) {

    roomsContainer.innerHTML = "";

    rooms.forEach(room => {
        const roomDisplay = document.createElement('a');
        roomDisplay.href = '/booking/create';
        roomDisplay.classList.add('room-link');

        roomDisplay.innerHTML = `
            <div class="room-desc-box">
                <img src="${room.imageUrl}" alt="${room.name}" class="room-image" />
                <div class="room-desc">
                    <h2>${room.name}</h2>
                    <table>
                        <tr>
                            <td>${room.roomAmenities['Bed']}</td>
                            <td>${room.roomAmenities['Size']}</td>
                            <td>${room.paxNo} <span class="fas fa-user"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        `;

        roomsContainer.appendChild(roomDisplay);
    });

    totalRoom = document.getElementsByClassName("room-image").length;
}

async function getDisplayRooms() {
    try {
        const response = await fetch('/api/roomTypes');
        if (!response.ok) {
            alert(`Error ${response.status}: ${response.statusText}`);
            return;
        }
        
        const rooms = await response.json();
        displayRooms(rooms);
    } catch (error) {
        console.error('Failed to load rooms:', error);
    }
}

function changeRoom() {
    roomsContainer.style.transition = 'transform 0.5s ease-in-out';
    roomsContainer.style.transform = `translateX(${-1150 * (roomCount - 2) / 2}px)`;
}

function nextRoom() {
    if (roomCount >= totalRoom) {
        roomCount = 2;
    }
    else {
        roomCount += 2;
    }
    changeRoom();
}

function previousRoom() {
    if (roomCount == 2) {
        roomCount = totalRoom;
    }
    else {
        roomCount -= 2;
    }
    changeRoom();
}

document.addEventListener('DOMContentLoaded', () => {
    timer = setInterval(changeImage, 2000);
    getDisplayRooms();
});