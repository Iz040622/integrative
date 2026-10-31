<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.monthlySales = @json($monthlySales);
        window.roomTypeSales = @json($roomTypeSales);
        // Debug output
        console.log('monthlySales:', window.monthlySales);
        console.log('roomTypeSales:', window.roomTypeSales);
    </script>
    <script src="{{ asset('js/admin.js') }}" defer></script>

</head>

<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <button id="menu-toggle" class="menu-btn">&#9776;</button>
        <form method="POST" action="{{ route('logout') }}" style="margin-left:auto;">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </form>
    </nav>

    <!-- Layout -->
    <div class="layout-wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <a href="#dashboard" onclick="showSection('dashboard')">Dashboard</a>
            <a href="#room-section" onclick="showSection('room-section')">Manage Room</a>
            <a href="#promocode" onclick="showSection('promocode')">Create Promo Code</a>
            <a href="#admin-section" onclick="showSection('admin-section')">Manage Admin</a>
        </div>

        @if (session('success'))
            <script>
                alert("{{ session('success') }}");
            </script>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <!-- Dashboard -->
            <div id="dashboard" class="section active">
                <h2>Sales Report</h2>
                <div class="selector-inline">
                    <label for="yearSelector">Select Year:</label>
                    <select id="yearSelector">
                        @foreach (collect($monthlySales)->pluck('year')->unique()->sort() as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Sales Table -->
                <div class="report-table">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody">
                        </tbody>
                    </table>
                </div>

                <!-- Sales Charts -->
                <div class="sales-section">
                    <div class="chart-container">
                        <h3>Total Sales of the Year</h3>
                        <canvas id="yearlySalesChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <div class="selector-inline">
                            <label for="monthSelector">Select Month:</label>
                            @php
                                $availableMonths = collect($roomTypeSales)->pluck('month')->unique()->sort();
                                $currentMonth = now()->month;
                            @endphp

                            <select id="monthSelector">
                                @foreach ($availableMonths as $month)
                                    <option value="{{ $month }}" {{ $month == $currentMonth ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        <canvas id="monthlyRoomChart" width="400" height="400"></canvas>
                    </div>
                </div>

            </div>

            <!-- Manage Room -->
            <div id="room-section"
                class="section {{ ($activeSection ?? 'dashboard') == 'room-section' ? 'active' : '' }}">
                <h2>Manage Rooms</h2>

                <div class="room-section-wrapper">
                    <!-- Room Creation Form -->
                    <div class="room-form card">
                        <h3>Create Room</h3>

                        @if ($errors->any())
                            <div class="error-message">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ url('/admin/room') }}">
                            @csrf
                            <div class="form-group">
                                <label>Room No:</label>
                                <input type="text" name="roomNo" value="{{ old('roomNo') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Room Type:</label>
                                <select name="room_type_id" required>
                                    @foreach ($roomTypes as $name)
                                        @if ($name->availability == 1)
                                            <option value="{{ $name->id }}"
                                                {{ old('room_type_id') == $name->id ? 'selected' : '' }}>
                                                {{ $name->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn-primary">Create Room</button>
                        </form>
                    </div>

                    <!-- Room Table -->
                    <div class="room-table-wrapper card">
                        <h3>Room List</h3>
                        <table class="room-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Room No</th>
                                    <th>Availability</th>
                                    <th>Room Type</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms as $room)
                                    <tr class="room-row" data-id="{{ $room->id }}"
                                        data-roomno="{{ $room->roomNo }}"
                                        data-availability="{{ $room->availability }}"
                                        data-roomtypeid="{{ $room->room_type_id }}">
                                        <td>{{ $room->id }}</td>
                                        <td>{{ $room->roomNo }}</td>
                                        <td>{{ $room->availability == 1 ? 'Available' : 'Unavailable' }}</td>
                                        <td>{{ $room->roomType->description ?? 'N/A' }}</td>
                                        <td>{{ optional($room->created_at)->format('Y-m-d H:i') }}</td>
                                        <td>{{ optional($room->updated_at)->format('Y-m-d H:i') }}</td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6">No rooms found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Pagination Links -->
                        <div class="pagination-links">
                            {{ $rooms->links('custom') }}
                        </div>

                    </div>

                    <!-- Edit Room Modal -->
                    <div id="editRoomModal" class="modal">
                        <div class="modal-content room-form">
                            <span class="close-btn">&times;</span>
                            <h2>Edit Room</h2>

                            <form id="editRoomForm" method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="id" id="edit-room-id">

                                <div class="form-group">
                                    <label>Room No:</label>
                                    <input type="text" name="roomNo" id="edit-roomNo" required>
                                </div>

                                <div class="form-group">
                                    <label>Availability:</label>
                                    <select name="availability" id="edit-room-availability" required>
                                        <option value="1">Available</option>
                                        <option value="0">Unavailable</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Room Type:</label>
                                    <select name="room_type_id" id="edit-room-type" required>
                                        @foreach ($roomTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->description }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit">Save Changes</button>
                            </form>
                        </div>
                    </div>
                    <!-- Manage Room Types -->
                    <div class="room-type-section-wrapper card">
                        <h2>Manage Room Types</h2>

                        <!-- Create Room Type Form -->
                        <div class="room-form">
                            <form id="createRoomTypeForm" method="POST" action="{{ url('/admin/roomType') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Type:</label>
                                    <input type="text" name="name" value="{{ old('name') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Description:</label>
                                    <textarea name="description" required>{{ old('description') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Room Amenities (comma separated):</label>
                                    <input type="text" name="roomAmenities" value="{{ old('roomAmenities') }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label>Max Guests:</label>
                                    <input type="number" name="paxNo" min="1" value="{{ old('paxNo') }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label>Price (RM):</label>
                                    <input type="number" name="price" min="0" step="0.01"
                                        value="{{ old('price') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Image:</label>
                                    <input type="file" name="imageUrl" accept="image/*">
                                </div>

                                <button type="submit" class="btn-primary">Create Room Type</button>
                            </form>
                        </div>

                        <!-- Room Type Table -->
                        <table class="room-type-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Room Amenities</th>
                                    <th>Pax</th>
                                    <th>Price</th>
                                    <th>Image</th>
                                    <th>Availability</th>
                                </tr>
                            </thead>
                            <tbody id="roomTypeTableBody">
                                @foreach ($roomTypes as $type)
                                    <tr class="room-type-row" data-id="{{ $type->id }}"
                                        data-type="{{ $type->name }}" data-description="{{ $type->description }}"
                                        data-amenities="{{ is_array($type->roomAmenities) ? implode(',', $type->roomAmenities) : '' }}"
                                        data-pax="{{ $type->paxNo }}" data-price="{{ $type->price }}"
                                        data-image="{{ $type->imageUrl }}"
                                        data-availability="{{ $type->availability }}">
                                        <td>{{ $type->id }}</td>
                                        <td>{{ $type->name }}</td>
                                        <td>{{ $type->description }}</td>
                                        <td>{{ is_array($type->roomAmenities) ? implode(', ', $type->roomAmenities) : '' }}
                                        </td>
                                        <td>{{ $type->paxNo }}</td>
                                        <td>{{ number_format($type->price, 2) }}</td>
                                        <td>
                                            @if ($type->imageUrl)
                                                <img src="{{ asset('storage/' . $type->imageUrl) }}" width="60">
                                            @else
                                                No image
                                            @endif
                                        </td>
                                        <td>{{ $type->availability == 1 ? 'Available' : 'Unavailable' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    <!-- Edit Room Type Modal -->
                    <div id="editRoomTypeModal" class="modal">
                        <div class="modal-content room-form">
                            <span class="close-btn">&times;</span>
                            <h2>Edit Room Type</h2>
                            <form id="editRoomTypeForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id" id="edit-roomtype-id">

                                <div class="form-group">
                                    <label>Type:</label>
                                    <input type="text" name="name" id="edit-roomtype-type" required>
                                </div>

                                <div class="form-group">
                                    <label>Description:</label>
                                    <textarea name="description" id="edit-roomtype-description" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Amenities:</label>
                                    <input type="text" name="roomAmenities" id="edit-roomtype-amenities">
                                </div>

                                <div class="form-group">
                                    <label>Max Guests:</label>
                                    <input type="number" name="paxNo" id="edit-roomtype-pax" min="1"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label>Price:</label>
                                    <input type="number" name="price" id="edit-roomtype-price" min="0"
                                        step="0.01" required>
                                </div>

                                <div class="form-group">
                                    <label>Image:</label>
                                    <input type="file" name="imageUrl" id="edit-roomtype-image" accept="image/*">
                                </div>

                                <div class="form-group">
                                    <label>Availability:</label>
                                    <select name="availability" id="edit-roomtype-availability" required>
                                        <option value="1">Available</option>
                                        <option value="0">Unavailable</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promo Code -->
            <div id="promocode" class="section {{ ($activeSection ?? 'dashboard') == 'promocode' ? 'active' : '' }}">
                <div class="promo-form">
                    <h2>Create Promo Code</h2>

                    @if ($errors->any())
                        <div class="error-message">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/admin/promocode') }}">
                        @csrf

                        <div class="form-group">
                            <label>Promo Code:</label>
                            <input type="text" name="promoteCode" value="{{ old('promoteCode') }}"
                                oninput="this.value = this.value.toUpperCase();" required>
                        </div>

                        <div class="form-group">
                            <label>Description:</label>
                            <textarea name="description" class="auto-resize" rows="1" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Discount (%):</label>
                            <input type="number" name="discount" min="0" max="100"
                                value="{{ old('discount') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Start Date:</label>
                            <input type="date" name="startDate" value="{{ old('startDate') }}" required>
                        </div>

                        <div class="form-group">
                            <label>End Date:</label>
                            <input type="date" name="endDate" value="{{ old('endDate') }}" required>
                        </div>


                        <button type="submit">Create</button>
                    </form>
                </div>

                <div class="promo-table-wrapper">
                    <table class="promo-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Promo Code</th>
                                <th>Description</th>
                                <th>Discount (%)</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promoCodes as $promo)
                                <tr class="promo-row" data-id="{{ $promo->id }}"
                                    data-code="{{ $promo->promoteCode }}"
                                    data-description="{{ $promo->description }}"
                                    data-discount="{{ $promo->discount }}" data-start="{{ $promo->startDate }}"
                                    data-end="{{ $promo->endDate }}">
                                    <td>{{ $promo->id }}</td>
                                    <td>{{ $promo->promoteCode }}</td>
                                    <td>{{ $promo->description }}</td>
                                    <td>{{ $promo->discount }}%</td>
                                    <td>{{ $promo->startDate }}</td>
                                    <td>{{ $promo->endDate }}</td>
                                    <td>{{ $promo->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $promo->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">No promo codes found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Edit Promo Modal -->
                <div id="editModal" class="modal">
                    <div class="modal-content promo-form">
                        <span class="close-btn">&times;</span>
                        <h2>Edit Promo Code</h2>

                        <form id="editPromoForm" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="id" id="edit-id">

                            <div class="form-group">
                                <label>Promo Code:</label>
                                <input type="text" name="promoteCode" id="edit-code"
                                    oninput="this.value = this.value.toUpperCase();" required>

                            </div>

                            <div class="form-group">
                                <label>Description:</label>
                                <textarea name="description" id="edit-promo-description" class="auto-resize" rows="1" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Discount (%):</label>
                                <input type="number" name="discount" id="edit-discount" min="0"
                                    max="100"required>
                            </div>

                            <div class="form-group">
                                <label>Start Date:</label>
                                <input type="date" name="startDate" id="edit-start" required>
                            </div>

                            <div class="form-group">
                                <label>End Date:</label>
                                <input type="date" name="endDate" id="edit-end" required>
                            </div>

                            <button type="submit">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Manage Admin -->
            <div id="admin-section"
                class="section {{ ($activeSection ?? 'dashboard') == 'admin-section' ? 'active' : '' }}">
                <div class="admin-section-wrapper">
                    <!-- Admin Creation Form -->
                    <div class="admin-form card">
                        <h2>Create Admin</h2>

                        @if ($errors->any())
                            <div class="error-message">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ url('/admin/admin') }}">
                            @csrf
                            <div class="form-group">
                                <label>IC No:</label>
                                <input type="text" name="ic_no" value="{{ old('ic_no') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Phone No:</label>
                                <input type="text" name="phone_no" value="{{ old('phone_no') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="text" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" name="password" value="{{ old('password') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password:</label>
                                <input type="password" name="password_confirmation"
                                    value="{{ old('password_confirmation') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="position">Position:</label>
                                <div class="radio-group">
                                    <input type="radio" id="admin" name="position" value="Admin"
                                        {{ old('position') == 'Admin' ? 'checked' : '' }}>
                                    <label for="admin">Admin</label>

                                    <input type="radio" id="superadmin" name="position" value="Super Admin"
                                        {{ old('position') == 'Super Admin' ? 'checked' : '' }}>
                                    <label for="superadmin">Super Admin</label>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary">Create Admin</button>
                        </form>
                    </div>

                    <!-- Admin Table -->
                    <div class="admin-table-wrapper card">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>IC No</th>
                                    <th>Phone No</th>
                                    <th>Email</th>
                                    <th>Position</th>
                                    <th>Availability</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                    <tr class="admin-row" data-id="{{ $admin->id }}"
                                        data-icno="{{ $admin->ic_no }}" data-phoneno="{{ $admin->phone_no }}"
                                        data-email="{{ $admin->user->email ?? '' }}"
                                        data-position="{{ $admin->position }}"
                                        data-availability="{{ $admin->availability }}">
                                        <td>{{ $admin->id }}</td>
                                        <td>{{ $admin->ic_no }}</td>
                                        <td>{{ $admin->phone_no }}</td>
                                        <td>{{ $admin->user->email ?? 'N/A' }}</td>
                                        <td>{{ $admin->position }}</td>
                                        <td>{{ $admin->availability == 1 ? 'Available' : 'Unavailable' }}</td>
                                        <td>{{ optional($admin->created_at)->format('Y-m-d H:i') }}</td>
                                        <td>{{ optional($admin->updated_at)->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty

                                    <tr>
                                        <td colspan="8">No admin found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>

                    <!-- Edit admin Modal -->
                    <div id="editAdminModal" class="modal">
                        <div class="modal-content admin-form">
                            <span class="close-btn">&times;</span>
                            <h2>Edit Admin</h2>

                            <form id="editAdminForm" method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="id" id="edit-admin-id">

                                <div class="form-group">
                                    <label>Ic No:</label>
                                    <input type="text" name="ic_no" id="edit-icNo" required>
                                </div>

                                <div class="form-group">
                                    <label>Phone No:</label>
                                    <input type="text" name="phone_no" id="edit-phoneNo" required>
                                </div>

                                <div class="form-group">
                                    <label>Email:</label>
                                    <input type="text" name="email" id="edit-email"
                                        value="{{ old('email') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="position">Position:</label>
                                    <div class="radio-group">
                                        <input type="radio" id="edit-admin" name="position" value="Admin">
                                        <label for="edit-admin">Admin</label>

                                        <input type="radio" id="edit-superadmin" name="position"
                                            value="Super Admin">
                                        <label for="edit-superadmin">Super Admin</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Availability:</label>
                                    <select name="availability" id="edit-admin-availability" required>
                                        <option value="1">Available</option>
                                        <option value="0">Unavailable</option>
                                    </select>
                                </div>
                                <button type="button" id="showChangePassword" class="btn-secondary">Change
                                    Password</button>
                                <div id="change-password-fields" style="display: none;">
                                    <div class="form-group">
                                        <label>New Password:</label>
                                        <input type="password" name="password" id="edit-password">
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password:</label>
                                        <input type="password" name="password_confirmation"
                                            id="edit-password-confirmation">
                                    </div>

                                    <button type="button" id="cancelChangePassword"
                                        class="btn-secondary">Cancel</button>
                                </div>
                                <button type="submit">Save Changes</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
</body>

</html>
