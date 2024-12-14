@extends('template.user')

@section('css')
{{-- Css code --}}
<link rel="stylesheet" href="{{ asset('css/user/login.css') }}">
@stop

@section('title')
Quên mật khẩu
@stop

<style>
.send-otp-btn {
    background-color: #3969d5;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;
    margin-left: 10px;
}
</style>

@section('content')
<div class="grid" style="width: 100%">
    <div class="container" style="height: 500px;">
        <!-- Checkbox to toggle between login and signup forms -->
        <input type="checkbox" id="flip" @if(Session::has('error')) checked="checked" @endif>

        <div class="cover">
            <div class="front">
                <img class="backImg" src="{{ asset('images/avatar/avatar.jpg') }}" alt="">
            </div>
            <div class="back">
                <img class="backImg" src="{{ asset('images/avatar/avatar.jpg') }}" alt="">
                <div class="text">
                    <span class="text-1">Complete miles of journey <br> with one step</span>
                    <span class="text-2">Let's get started</span>
                </div>
            </div>
        </div>

        <div class="forms">
            <div class="form-content">
                <!-- Reset Password Form -->
                <div class="login-form">
                    <div class="title">Lấy lại mật khẩu</div>
                    <form id="form">
                        @csrf
                        @if(Session::has('success'))
                        <h2 class="success-message" style="color:rgb(0, 51, 218)">
                            {{ Session::get('success') }}
                        </h2>
                        @endif

                        @if(Session::has('error-login'))
                        <h2 class="error-message" style="color:red">
                            {{ Session::get('error-login') }}
                        </h2>
                        @endif

                        <div class="input-boxes">
                            <!-- Email input with Send OTP button -->
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input name="email" type="email" placeholder="Enter your email" required>
                                <button type="button" class="send-otp-btn" onclick="sendOtp()">Gửi OTP</button>
                            </div>

                            <!-- OTP input -->
                            <div class="input-box">
                                <i class="fas fa-key"></i>
                                <input name="otp" type="text" placeholder="Enter OTP" required>
                            </div>

                            <!-- Password inputs -->
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input name="password1" type="password" placeholder="Enter your new password" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input name="password2" type="password" placeholder="Confirm your new password"
                                    required>
                            </div>

                            <!-- Submit button -->
                            <div class="button input-box">
                                <input type="submit" value="Reset Password">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
function sendOtp() {
    const emailInput = document.querySelector('input[name="email"]');
    const email = emailInput.value;

    if (!email) {
        alert('Please enter your email before requesting an OTP.');
        return;
    }

    fetch('/send-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Gửi OTP thành công. Vui lòng kiểm tra email của bạn.');
            } else {
                alert('Gửi OTP thất bại. Vui lòng thử lại sau.');
            }
        })
        .catch(error => {
            console.error('Error sending OTP:', error);
            alert('Gửi OTP thất bại. Vui lòng thử lại sau.');
        });
}

document.getElementById('form').addEventListener('submit', function(e) {
    e.preventDefault();

    const email = document.querySelector('input[name="email"]').value;
    const otp = document.querySelector('input[name="otp"]').value;
    const password1 = document.querySelector('input[name="password1"]').value;
    const password2 = document.querySelector('input[name="password2"]').value;

    if (!email || !otp || !password1 || !password2) {
        alert('Vui lòng điền đầy đủ thông tin.');
        return;
    }

    if (password1 !== password2) {
        alert('Mật khẩu không khớp. Vui lòng thử lại.');
        return;
    }

    fetch('/reset-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email,
                otp,
                password: password1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Đặt lại mật khẩu thành công.');
                window.location.href = '/dang-nhap';
            } else {
                alert('Đặt lại mật khẩu thất bại. Vui lòng thử lại kiểm tra lại thông tin.');
            }
        })
        .catch(error => {
            console.error('Error resetting password:', error);
            alert('Đặt lại mật khẩu thất bại. Vui lòng thử lại sau.');
        });
});
</script>
@stop