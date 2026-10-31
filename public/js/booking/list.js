// Chin Jia Jun

// Fetch and display booking room details
document.addEventListener("DOMContentLoaded", async function () {
    const bookingRooms = document.querySelectorAll(".booking-room");

    for (const item of bookingRooms) {
        const room_id = item.dataset.roomId;
        const booking_id = item.dataset.bookingId;
        const index = item.dataset.index;

        const checkIn = new Date(item.dataset.checkin);
        const checkOut = new Date(item.dataset.checkout);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));

        try {
            const roomTypeResponse = await fetch(`/api/room/${room_id}/roomType`);
            if (!roomTypeResponse.ok) {
                console.error(`Error ${roomTypeResponse.status}: ${roomTypeResponse.statusText}`);
                showSnackbar("Something went wrong. Please try again");
                return;
            }

            const roomType = await roomTypeResponse.json();

            document.getElementById(`room-image-${booking_id}-${index}`).src = roomType.imageUrl;
            document.getElementById(`room-name-${booking_id}-${index}`).innerText = roomType.name;
            document.getElementById(`room-price-${booking_id}-${index}`).innerText = `Price: RM${roomType.price * nights}`;
        } catch (error) {
            console.error("Fetch error:", error);
            showSnackbar("Something went wrong. Please try again");
        }


        try {
            const roomResponse = await fetch(`/api/room/${room_id}`);
            if (!roomResponse.ok) {
                console.error(`Error ${roomResponse.status}: ${roomResponse.statusText}`);
                showSnackbar("Something went wrong. Please try again");
                return;
            }

            const room = await roomResponse.json();
            document.getElementById(`room-no-${booking_id}-${index}`).innerText = `${room.roomNo}`;
        } catch (error) {
            console.error("Fetch error:", error);
            showSnackbar("Something went wrong. Please try again");
        }
    }

    // Add confirmation dialog for cancelling booking
    const cancelForms = document.querySelectorAll('.cancel-form');

    cancelForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Do you want to cancel booking this room?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Yes, cancel it",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

function formatDateToYMD(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
