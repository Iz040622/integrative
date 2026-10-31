<div id="forgotPasswordModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeForgotPasswordModalComponent()">&times;</span>
        <h1>Reset Password</h1>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <input type="email" name="email" placeholder="Enter your email" required value="{{ session('email') }}">
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
            <button type="submit">Send OTP</button>
        </form>
    </div>
</div>

<script>
function showForgotPasswordModalComponent() {
    document.getElementById('forgotPasswordModal').style.display = 'block';
}
function closeForgotPasswordModalComponent() {
    document.getElementById('forgotPasswordModal').style.display = 'none';
}
</script>
