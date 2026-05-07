function togglePass(id, el) {
    let input = document.getElementById(id);
    const eyeOpen = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
    const eyeClosed = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;

    if (input.type === "password") {
        input.type = "text";
        el.innerHTML = eyeOpen;
    } else {
        input.type = "password";
        el.innerHTML = eyeClosed;
    }
}

function moveFocus(el, p) {
    if (el.value.length >= 1 && p < 6) {
        let next = document.getElementById('otp' + (p + 1));
        if (next) next.focus();
    }
}

function initOTPExpiry(remainingTime, realCode) {
    let timeLeft = remainingTime;
    let timerElement = document.getElementById('timer');
    let otpCodeElement = document.getElementById('otpCode');
    let btnResend = document.getElementById('btnResend');

    if (!timerElement || !otpCodeElement) return;

    window.revealCode = function() {
        if (timeLeft > 0) {
            otpCodeElement.innerText = realCode;
            otpCodeElement.style.color = "#0369a1";
        }
    };

    // Vô hiệu hóa nút trong thời hạn
    if (timeLeft > 0 && btnResend) {
        btnResend.style.opacity = "0.5";
        btnResend.style.pointerEvents = "none";
        btnResend.innerText = "Chờ hết hạn để gửi lại";
    }

    let countdown = setInterval(function () {
        if (timeLeft > 0) {
            timeLeft--;
            timerElement.innerText = "Mã có hiệu lực trong: " + timeLeft + "s";
        }

        if (timeLeft <= 0) {
            clearInterval(countdown);
            timerElement.innerText = "MÃ ĐÃ HẾT HẠN!";
            otpCodeElement.style.color = "#94a3b8";
            otpCodeElement.innerText = "******";

            // Kích hoạt lại nút gửi lại
            if (btnResend) {
                btnResend.style.opacity = "1";
                btnResend.style.pointerEvents = "auto";
                btnResend.innerText = "Gửi lại mã mới";
                btnResend.style.background = "#3b82f6";
            }
        }
    }, 1000);
}
