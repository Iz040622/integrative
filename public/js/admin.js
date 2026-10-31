document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("menu-toggle");
    const layout = document.querySelector(".layout-wrapper");

    let lineChart;
    let pieChart = null;
    let chartsRendered = false;
    let firstLoad = true;

    // Sidebar toggle
    toggleBtn.addEventListener("click", function () {
        sidebar.classList.toggle("show");
        toggleBtn.classList.toggle("active");
        layout.classList.toggle("sidebar-open");
    });

    const yearSelector = document.getElementById("yearSelector");
    const monthSelector = document.getElementById("monthSelector");
    const defaultYear = yearSelector?.value;

    if (yearSelector) {
        yearSelector.addEventListener("change", function () {
            updateLineChart(this.value);
        });
    }

    if (monthSelector) {
        monthSelector.addEventListener("change", function () {
            const selectedYear = parseInt(yearSelector?.value);
            updatePieChart(this.value, selectedYear);
        });
    }

    if (defaultYear) renderTableAndChart(defaultYear);

    yearSelector?.addEventListener("change", (e) => {
        const selectedYear = parseInt(e.target.value);
        const currentYear = new Date().getFullYear();
        const currentMonth = new Date().getMonth() + 1;

        renderTableAndChart(selectedYear);

        //Get available months for the selected year from roomTypeSales
        const availableMonths = [
            ...new Set(
                window.roomTypeSales
                    .filter((item) => parseInt(item.year) === selectedYear)
                    .map((item) => parseInt(item.month))
            ),
        ].sort((a, b) => a - b);

        //Clear and rebuild the monthSelector options
        if (monthSelector) {
            monthSelector.innerHTML = "";
            availableMonths.forEach((month) => {
                const option = document.createElement("option");
                option.value = month;
                option.textContent = new Date(0, month - 1).toLocaleString(
                    "default",
                    { month: "long" }
                );
                monthSelector.appendChild(option);
            });

            //Choose default month
            let monthToShow;
            if (
                selectedYear === currentYear &&
                availableMonths.includes(currentMonth)
            ) {
                monthToShow = currentMonth;
            } else {
                monthToShow = availableMonths[0]; // first available month
            }

            monthSelector.value = monthToShow;

            //Update pie chart
            setTimeout(() => {
                updatePieChart(monthToShow, selectedYear);
            }, 50);
        }
    });

    // Function to render Chart.js charts
    function renderTableAndChart(selectedYear) {
        const sales = window.monthlySales.filter(
            (item) => parseInt(item.year) === parseInt(selectedYear)
        );

        // Render Table
        const tableBody = document.getElementById("salesTableBody");
        if (tableBody) {
            tableBody.innerHTML = "";
            sales.forEach((item) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                <td>${new Date(0, item.month - 1).toLocaleString("default", {
                    month: "long",
                })}</td>
                <td>RM ${parseFloat(item.total_sales).toFixed(2)}</td>
                `;

                tableBody.appendChild(row);
            });
        }

        // Render Line Chart
        const ctx = document
            .getElementById("yearlySalesChart")
            ?.getContext("2d");
        if (!ctx) return;

        const labels = sales.map((item) =>
            new Date(0, item.month - 1).toLocaleString("default", {
                month: "long",
            })
        );
        const data = sales.map((item) => item.total_sales);

        if (lineChart) lineChart.destroy();

        lineChart = new Chart(ctx, {
            type: "line",
            data: {
                labels,
                datasets: [
                    {
                        label: `Total Sales (RM) for ${selectedYear}`,
                        data,
                        backgroundColor: "#4CAF50",
                        borderColor: "#4CAF50",
                        fill: false,
                        tension: 0.3,
                    },
                ],
            },
        });
    }

    function renderCharts() {
        if (defaultYear) renderTableAndChart(defaultYear);

        const defaultMonth = monthSelector?.value;
        if (defaultMonth) {
            const selectedYear = parseInt(yearSelector?.value);
            setTimeout(() => {
                updatePieChart(defaultMonth, selectedYear);
            }, 50);
        }
    }

    function updateLineChart(selectedYear) {
        const ctxYear = document
            .getElementById("yearlySalesChart")
            ?.getContext("2d");
        if (!ctxYear || !window.monthlySales) return;

        const filtered = window.monthlySales.filter(
            (item) => parseInt(item.year) === parseInt(selectedYear)
        );

        const labels = filtered.map((item) =>
            new Date(0, item.month - 1).toLocaleString("default", {
                month: "long",
            })
        );
        const data = filtered.map((item) => item.total_sales);

        if (lineChart) lineChart.destroy();

        lineChart = new Chart(ctxYear, {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Total Sales (RM)",
                        data: data,
                        backgroundColor: "#4CAF50",
                        borderColor: "#4CAF50",
                        fill: false,
                        tension: 0.3,
                    },
                ],
            },
        });
    }

    function updatePieChart(selectedMonth, selectedYear) {
        const ctxMonth = document
            .getElementById("monthlyRoomChart")
            ?.getContext("2d");
        if (!ctxMonth || !window.roomTypeSales) return;

        const filtered = roomTypeSales.filter(
            (item) =>
                parseInt(item.month) === parseInt(selectedMonth) &&
                parseInt(item.year) === parseInt(selectedYear)
        );

        if (!filtered.length) {
            if (pieChart) pieChart.destroy();
            return;
        }

        const grouped = {};
        filtered.forEach((item) => {
            grouped[item.room_type] =
                (grouped[item.room_type] || 0) + parseInt(item.count);
        });

        const labels = Object.keys(grouped);
        const data = Object.values(grouped);

        if (pieChart) pieChart.destroy();

        pieChart = new Chart(ctxMonth, {
            type: "pie",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Room Bookings",
                        data: data,
                        backgroundColor: [
                            "#FF6384",
                            "#36A2EB",
                            "#FFCE56",
                            "#9C27B0",
                            "#4CAF50",
                            "#f44336",
                            "#3f51b5",
                        ],
                    },
                ],
            },
        });
    }

    // Function to switch visible section
    window.showSection = function (id) {
        document
            .querySelectorAll(".section")
            .forEach((section) => section.classList.remove("active"));

        const targetSection = document.getElementById(id);
        if (targetSection) {
            targetSection.classList.add("active");
            history.replaceState(null, null, "#" + id);

            // Only clear errors AFTER first load
            if (!firstLoad) {
                document
                    .querySelectorAll(
                        ".error-message, .alert, .invalid-feedback"
                    )
                    .forEach((el) => el.remove());

                document.querySelectorAll(".is-invalid").forEach((el) => {
                    el.classList.remove("is-invalid");
                });
                // Reset forms inside the section
                targetSection.querySelectorAll("form").forEach((form) => {
                    form.querySelectorAll("input, textarea, select").forEach(
                        (el) => {
                            if (el.type === "hidden") return;
                            if (el.type === "checkbox" || el.type === "radio") {
                                el.checked = false;
                            } else {
                                el.value = "";
                            }
                        }
                    );
                });
            }

            firstLoad = false;

            // Only render charts the first time dashboard is shown
            if (id === "dashboard" && !chartsRendered) {
                renderCharts();
                chartsRendered = true;

                // Trigger pie chart using the default selected value
                const defaultMonth = monthSelector?.value;
                const selectedYear = parseInt(yearSelector?.value);
                if (defaultMonth && selectedYear) {
                    setTimeout(() => {
                        updatePieChart(defaultMonth, selectedYear);
                    }, 50);
                }
            }
        }
    };

    // Hide sidebar on outside click
    document.addEventListener("click", function (event) {
        const isSidebarOpen = sidebar.classList.contains("show");
        const clickedOutside =
            !sidebar.contains(event.target) &&
            !toggleBtn.contains(event.target);

        if (isSidebarOpen && clickedOutside) {
            sidebar.classList.remove("show");
            toggleBtn.classList.remove("active");
            layout.classList.remove("sidebar-open");
        }
    });

    // Show section on load or default
    const hash = window.location.hash;
    showSection(hash ? hash.substring(1) : "dashboard");
});

// Hashchange support
window.addEventListener("hashchange", () => {
    const sectionId = window.location.hash.replace("#", "");
    showSection(sectionId);
});

// Auto-resize textareas
document.querySelectorAll("textarea.auto-resize").forEach((textarea) => {
    textarea.addEventListener("input", () => {
        textarea.style.height = "auto";
        textarea.style.height = textarea.scrollHeight + "px";
    });
    textarea.dispatchEvent(new Event("input"));
});

// Modal for editing promo codes
document.addEventListener("DOMContentLoaded", () => {
    const promoModal = document.getElementById("editModal");
    const closeBtn = promoModal.querySelector(".close-btn");

    document.querySelectorAll(".promo-row").forEach((row) => {
        row.addEventListener("click", () => {
            document.getElementById("edit-id").value = row.dataset.id;
            document.getElementById("edit-code").value = row.dataset.code;
            document.getElementById("edit-promo-description").value =
                row.dataset.description;
            document.getElementById("edit-discount").value =
                row.dataset.discount;
            document.getElementById("edit-start").value = row.dataset.start;
            document.getElementById("edit-end").value = row.dataset.end;

            document.getElementById(
                "editPromoForm"
            ).action = `/admin/promocode/${row.dataset.id}`;
            promoModal.style.display = "block";
        });
    });

    closeBtn.addEventListener("click", () => {
        promoModal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === promoModal) {
            promoModal.style.display = "none";
        }
    });
});

// Room Edit Modal
document.addEventListener("DOMContentLoaded", () => {
    const roomModal = document.getElementById("editRoomModal");
    const closeRoomBtn = roomModal?.querySelector(".close-btn");

    document.querySelectorAll(".room-row").forEach((row) => {
        row.addEventListener("click", () => {
            const roomModal = document.getElementById("editRoomModal");
            if (!roomModal) return;

            document.getElementById("edit-room-id").value = row.dataset.id;
            document.getElementById("edit-roomNo").value = row.dataset.roomno;
            document.getElementById("edit-room-availability").value =
                row.dataset.availability;
            document.getElementById("edit-room-type").value =
                row.dataset.roomtypeid || 0;

            document.getElementById(
                "editRoomForm"
            ).action = `/admin/room/${row.dataset.id}`;

            roomModal.style.display = "block";
        });
    });

    closeRoomBtn?.addEventListener("click", () => {
        roomModal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === roomModal) roomModal.style.display = "none";
    });
});

// Room Type Edit Modal
document.addEventListener("DOMContentLoaded", () => {
    const roomTypeModal = document.getElementById("editRoomTypeModal");
    const closeRoomTypeBtn = roomTypeModal?.querySelector(".close-btn");

    document.querySelectorAll(".room-type-row").forEach((row) => {
        row.addEventListener("click", () => {
            const editForm = document.getElementById("editRoomTypeModal");
            if (!editForm) return;

            document.getElementById("edit-roomtype-id").value = row.dataset.id;
            document.getElementById("edit-roomtype-type").value =
                row.dataset.type;
            document.getElementById("edit-roomtype-description").value =
                row.dataset.description;
            document.getElementById("edit-roomtype-amenities").value =
                row.dataset.amenities;
            document.getElementById("edit-roomtype-pax").value =
                row.dataset.pax;
            document.getElementById("edit-roomtype-price").value =
                row.dataset.price;
            document.getElementById("edit-roomtype-availability").value =
                row.dataset.availability;
            document.getElementById(
                "editRoomTypeForm"
            ).action = `/admin/roomType/${row.dataset.id}`;

            roomTypeModal.style.display = "block";
        });
    });

    closeRoomTypeBtn?.addEventListener("click", () => {
        roomTypeModal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === roomTypeModal) roomTypeModal.style.display = "none";
    });
});

// Admin Edit Modal
document.addEventListener("DOMContentLoaded", () => {
    const adminModal = document.getElementById("editAdminModal");
    const closeAdminBtn = adminModal?.querySelector(".close-btn");

    const changePasswordBtn = document.getElementById("showChangePassword");
    const cancelChangePasswordBtn = document.getElementById(
        "cancelChangePassword"
    );
    const passwordFields = document.getElementById("change-password-fields");

    document.querySelectorAll(".admin-row").forEach((row) => {
        row.addEventListener("click", () => {
            document.getElementById("edit-admin-id").value = row.dataset.id;
            document.getElementById("edit-icNo").value = row.dataset.icno;
            document.getElementById("edit-phoneNo").value = row.dataset.phoneno;
            document.getElementById("edit-email").value = row.dataset.email;
            document.getElementById("edit-admin-availability").value =
                row.dataset.availability;
            const positionInput = document.querySelector(
                `#editAdminModal input[name="position"][value="${row.dataset.position}"]`
            );
            if (positionInput) {
                positionInput.checked = true;
            }
            // Set form action dynamically
            document.getElementById(
                "editAdminForm"
            ).action = `/admin/admin/${row.dataset.id}`;

            // Hide password fields when modal opens
            passwordFields.style.display = "none";
            changePasswordBtn.style.display = "inline-block";

            adminModal.style.display = "block";
        });
    });

    closeAdminBtn?.addEventListener("click", () => {
        adminModal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === adminModal) adminModal.style.display = "none";
    });

    //Password toggle handlers
    if (changePasswordBtn && cancelChangePasswordBtn) {
        changePasswordBtn.addEventListener("click", () => {
            passwordFields.style.display = "block";
            changePasswordBtn.style.display = "none";
        });

        cancelChangePasswordBtn.addEventListener("click", () => {
            passwordFields.style.display = "none";
            changePasswordBtn.style.display = "inline-block";
        });
    }
});

document.addEventListener("DOMContentLoaded", () => {
    // Toggle jump form on ellipsis click
    document
        .querySelectorAll(".custom-pagination .ellipsis")
        .forEach((ellipsis) => {
            ellipsis.addEventListener("click", (e) => {
                e.stopPropagation();
                const form = ellipsis.querySelector(".jump-form");
                form.classList.toggle("show");
            });
        });

    // Handle jump form submission
    document
        .querySelectorAll(".custom-pagination .jump-form")
        .forEach((form) => {
            form.addEventListener("submit", (e) => {
                e.preventDefault();
                const page = parseInt(
                    form.querySelector("input[name='page']").value
                );
                if (!page) return;

                const baseUrl = window.location.href
                    .split("?")[0]
                    .split("#")[0];
                window.location.href = `${baseUrl}?page=${page}#room-section`;
            });
        });

    // Close jump form when clicking outside
    document.addEventListener("click", () => {
        document
            .querySelectorAll(".custom-pagination .jump-form.show")
            .forEach((form) => {
                form.classList.remove("show");
            });
    });

    //prevent closing when clicking inside the jump form
    document
        .querySelectorAll(".custom-pagination .jump-form")
        .forEach((form) => {
            form.addEventListener("click", (e) => e.stopPropagation());
        });
});
