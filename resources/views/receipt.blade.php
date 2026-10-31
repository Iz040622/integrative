<!-- LEOW SHI HAO 2314003 G3 -->
<x-layout>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/receipt.css') }}">
    @endpush

    <div class="receipt-container">
        <h2>Payment Receipt</h2>
        @if (!empty($bookings))
            <h4>Booked By: {{ $bookings[0]['name'] ?? 'User' }}</h4>
            <h4>Booked Rooms:</h4>
            @foreach ($bookings as $booking)
                <div class="booking-item">
                    <span>{{ $booking['room_type'] ?? 'N/A' }}</span>
                    <span>From {{ $booking['startDate'] ?? 'N/A' }} to {{ $booking['endDate'] ?? 'N/A' }}</span>
                    <span>Pax: {{ $booking['number_of_pax'] ?? 'N/A' }}</span>
                    <span>RM {{ number_format($booking['price'] ?? 0, 2) }}</span>
                </div>
            @endforeach
        @else
            <p>No booking details available. Please contact support if this is unexpected.</p>
        @endif
        <p><strong>Transaction ID:</strong> {{ $transaction_id }}</p>
        <p><strong>Subtotal:</strong> RM {{ number_format($subtotal, 2) }}</p>
        <p><strong>Tax (6%):</strong> RM {{ number_format($tax, 2) }}</p>
        <p><strong>Discount:</strong> RM {{ number_format($discount, 2) }}</p>
        <p><strong>Total Amount:</strong> RM {{ number_format($total, 2) }}</p>
        <p><strong>Date:</strong> {{ $date }}</p>
        <p><strong>Payment Method:</strong> {{ $method }}</p>
        <div class="mt-4">
            <button class="btn btn-primary no-print" onclick="window.print()">Print Receipt</button>
            <button class="btn btn-success no-print" id="download-receipt">Download PDF</button>
            <a href="{{ route('cart') }}" class="btn btn-secondary no-print">Back to Cart</a>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script>
            window.receiptData = {!! json_encode(compact('transaction_id', 'subtotal', 'tax', 'discount', 'total', 'method', 'date', 'bookings')) !!};

            document.getElementById('download-receipt').addEventListener('click', () => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                let y = 10;
                doc.setFontSize(12);

                doc.text('Payment Receipt', 10, y); y += 10;

                if (window.receiptData.bookings && window.receiptData.bookings.length > 0) {
                    doc.setFontSize(14);
                    doc.text(`Booked By: ${window.receiptData.bookings[0].name || 'User'}`, 10, y); y += 10;
                    doc.setFontSize(12);
                    doc.text('Booked Rooms:', 10, y); y += 10;

                    window.receiptData.bookings.forEach(booking => {
                        doc.text(`- ${booking.room_type || 'N/A'}`, 15, y); y += 7;
                        doc.text(`  From: ${booking.startDate || 'N/A'} to ${booking.endDate || 'N/A'}`, 15, y); y += 7;
                        doc.text(`  Pax: ${booking.number_of_pax || 'N/A'} | Price: RM ${parseFloat(booking.price || 0).toFixed(2)}`, 15, y); y += 10;
                    });
                } else {
                    doc.setFont('helvetica', 'italic');
                    doc.text('No booking details available. Please contact support if this is unexpected.', 10, y); y += 10;
                    doc.setFont('helvetica', 'normal'); 
                }

                doc.text(`Transaction ID: ${window.receiptData.transaction_id}`, 10, y); y += 10;
                doc.text(`Subtotal: RM ${parseFloat(window.receiptData.subtotal).toFixed(2)}`, 10, y); y += 10;
                doc.text(`Tax (6%): RM ${parseFloat(window.receiptData.tax).toFixed(2)}`, 10, y); y += 10;
                doc.text(`Discount: RM ${parseFloat(window.receiptData.discount).toFixed(2)}`, 10, y); y += 10;
                doc.text(`Total Amount: RM ${parseFloat(window.receiptData.total).toFixed(2)}`, 10, y); y += 10;
                doc.text(`Date: ${window.receiptData.date}`, 10, y); y += 10;
                doc.text(`Payment Method: ${window.receiptData.method}`, 10, y);

                doc.save('receipt.pdf');
            });
        </script>
    @endpush
</x-layout>