<div id="resetPasswordModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeResetPasswordModal()">&times;</span>
        <h1>Set New Password</h1>
        <p>Reset password for {{ session('email') }}</p>
        <form method="POST" action="{{ route('password.update.otp') }}">
            @csrf
            <input type="hidden" name="email" value="{{ session('email') }}">
            <input type="hidden" name="otp" value="{{ session('otp') }}">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            @error('password')
                <span class="error-message">{{ $message }}</span>
            @enderror
            <button type="submit">Reset Password</button>
        </form>
        <a href="{{ route('password.verify-otp', ['email' => session('email')]) }}" class="back-btn">Back</a>
    </div>
</div>

<script>
function showResetPasswordModal() {
    document.getElementById('resetPasswordModal').style.display = 'block';
}
function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').style.display = 'none';
}
</script>
