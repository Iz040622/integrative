@if ($errors->any())
    <div id="error-modal-overlay" 
         style="position:fixed; inset:0; z-index:2000; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.6);">
        
        <div id="error-modal-box" 
             style="background:#fff; border-radius:16px; padding:24px 20px; width:90%; max-width:420px; text-align:center; position:relative; box-shadow:0 10px 40px rgba(0,0,0,0.3); transform:scale(0.9); opacity:0; transition:all 0.25s ease;">
            
            <!-- Icon -->
            <div style="width:60px; height:60px; margin:0 auto 12px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center;">
                <span style="font-size:32px; color:#dc2626;">&#9888;</span>
            </div>
            
            <!-- Title -->
            <h2 style="color:#dc2626; font-size:20px; font-weight:600; margin-bottom:12px;">Something went wrong</h2>
            
            <!-- Error messages -->
            <div style="color:#f60606; font-size:14px; text-align:left; max-height:160px; overflow:auto; margin-bottom:16px; line-height:1.5;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
            
            <!-- Close button -->
            <button onclick="closeErrorModal()" 
                    style="padding:10px 18px; background:#dc2626; color:#fff; border:none; border-radius:10px; font-size:14px; font-weight:500; cursor:pointer; transition:background 0.2s;">
                Back
            </button>
        </div>
    </div>

    <script>
        function closeErrorModal() {
            const modal = document.getElementById('error-modal-overlay');
            if (modal) modal.style.display = 'none';
        }

        // Animate modal on load
        window.onload = () => {
            const modalBox = document.getElementById('error-modal-box');
            if (modalBox) {
                setTimeout(() => {
                    modalBox.style.transform = 'scale(1)';
                    modalBox.style.opacity = '1';
                }, 50);
            }
        };
    </script>
@endif
