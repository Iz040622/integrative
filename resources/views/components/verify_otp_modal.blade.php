<div id="verifyOtpModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeVerifyOtpModal()">&times;</span>
        <h1>Verify OTP</h1>
        <p>We sent a 6-digit code to {{ session('email') }}</p>
        <form method="POST" action="{{ route('password.verify-otp.post') }}">
            @csrf
            <input type="hidden" name="email" value="{{ session('email') }}">
            <input type="text" name="otp" maxlength="6" placeholder="Enter OTP" required>
            @error('otp')
                <span class="error-message">{{ $message }}</span>
            @enderror
            <button type="submit">Verify OTP</button>
        </form>
        <a href="{{ route('login') }}" class="back-btn">Back</a>
    </div>
</div>

<script>
function showVerifyOtpModal() {
    document.getElementById('verifyOtpModal').style.display = 'block';
}
function closeVerifyOtpModal() {
    document.getElementById('verifyOtpModal').style.display = 'none';
}
</script>
