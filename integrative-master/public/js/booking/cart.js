// Chin Jia Jun

document.addEventListener("DOMContentLoaded", async function () {
    const cartItems = document.querySelectorAll(".cart-item");

    // Fetch and display room details for each cart item
    for (const [index, item] of cartItems.entries()) {
        const room_id = item.dataset.roomId;

        try {
            const response = await fetch(`/api/room/${room_id}/roomType`);
            if (!response.ok) {
                console.error(`Error ${response.status}: ${response.statusText}`);
                showSnackbar("Something went wrong. Please try again");
                return;
            }

            const room = await response.json();

            document.getElementById(`room-image-${index}`).src = room.imageUrl;
            document.getElementById(`room-name-${index}`).innerText = room.name;
            document.getElementById(`room-price-${index}`).innerText = `RM${room.price}`;
        } catch (error) {
            console.error("Fetch error:", error);
            showSnackbar("Something went wrong. Please try again");
        }
    }

    const removeForms = document.querySelectorAll('.remove-form');

    // Add confirmation dialog for removing items
    removeForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Do you want to remove this item?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Yes, remove it",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
