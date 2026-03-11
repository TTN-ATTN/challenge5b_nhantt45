// --- TOAST UI ---
function showToast(message, type) {
    var container = document.getElementById("toast-container");
    if (!container) return;

    var toast = document.createElement("div");
    var baseClasses = "px-4 py-3 rounded-lg shadow-lg text-white font-medium text-sm transition-all duration-300 transform translate-y-4 opacity-0 flex items-center gap-3";
    var typeClasses = type === 'error' ? "bg-red-500" : "bg-green-500";
    toast.className = baseClasses + " " + typeClasses;
    
    var icon = document.createElement("span");
    icon.innerHTML = type === 'error' 
        ? '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        : '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        
    var textNode = document.createElement("div");
    textNode.className = "flex-1 break-words max-w-xs";
    textNode.innerText = message;
    
    toast.appendChild(icon);
    toast.appendChild(textNode);
    container.appendChild(toast);

    requestAnimationFrame(function() {
        toast.classList.remove("translate-y-4", "opacity-0");
        toast.classList.add("translate-y-0", "opacity-100");
    });

    setTimeout(function() { 
        toast.classList.remove("translate-y-0", "opacity-100");
        toast.classList.add("translate-y-4", "opacity-0");
        setTimeout(function() {
            if (container.contains(toast)) {
                container.removeChild(toast); 
            }
        }, 300);
    }, 3000);
}

// --- DYNAMIC MODAL CONTROLLER ---
function closeModal() {
    document.getElementById('passwordModal').style.display = 'none';
    document.getElementById('passwordModal').classList.add('hidden');
    document.getElementById('modal_password').value = '';
}

function requirePasswordAndSubmit(formId) {
    // 1. Mở modal
    const modal = document.getElementById('passwordModal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
    
    const passInput = document.getElementById('modal_password');
    passInput.value = '';
    passInput.focus();

    // 2. Gắn sự kiện khi bấm nút Xác nhận
    document.getElementById('modalConfirmBtn').onclick = function() {
        const password = passInput.value;
        if (!password.trim()) {
            showToast("Vui lòng nhập mật khẩu xác nhận!", "error");
            return;
        }

        const form = document.getElementById(formId);
        
        // Tự động tạo thẻ input ẩn chứa mật khẩu nhét vào form
        let hiddenPass = form.querySelector('input[name="current_password"]');
        if (!hiddenPass) {
            hiddenPass = document.createElement('input');
            hiddenPass.type = 'hidden';
            hiddenPass.name = 'current_password';
            form.appendChild(hiddenPass);
        }
        hiddenPass.value = password;

        form.submit();
    };
}

// Lắng nghe sự kiện bấm Enter ở ô nhập mật khẩu
document.addEventListener('DOMContentLoaded', function() {
    const passInput = document.getElementById('modal_password');
    if (passInput) {
        passInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); 
                document.getElementById('modalConfirmBtn').click();
            }
        });
    }
});