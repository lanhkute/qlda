@extends('template.user')

@section('css')
{{-- Css code --}}
<link rel="stylesheet" href="{{ asset('css/user/login.css') }}">
@stop

@section('title')
Đăng nhập
@stop

@section('content')
<div class="grid" style="width: 100%;">
    <div class="container">
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
                <div class="login-form">
                    <div class="title">Login</div>
                    <form method="post" id="form" action="{{ route('user.loginProcessing') }}">
                        <div class="input-boxes">
                            @csrf
                            @if(Session::has('success'))
                            <h2 style="color:rgb(0, 51, 218)">
                                {{ Session::get('success') }}
                            </h2>
                            @endif
                            @if(Session::has('error-login'))
                            <h2 style="color:red">
                                {{ Session::get('error-login') }}
                            </h2>
                            @endif
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input name="email" type="text" placeholder="Enter your email" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input name="password" type="password" placeholder="Enter your password" required>
                            </div>
                            <!-- <div class="text"><a href="#">Forgot password?</a></div> -->
                            <div class="text"><a href="{{ route('user.forgotpw') }}">Forgot password?</a></div>
                            <div class="button input-box">
                                <input type="submit" value="Submit">
                            </div>
                            <div class="text sign-up-text">Don't have an account? <label for="flip">Sigup now</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="signup-form">
                    <div class="title">Signup</div>
                    <form method="post" action="{{ route('user.register') }}">
                        <div class="input-boxes">
                            @csrf
                            @if(Session::has('error'))
                            <h2 style="color:red">
                                {{ Session::get('error') }}
                            </h2>
                            @endif
                            <div class="input-box">
                                <i class="fas fa-user"></i>
                                <input type="text" name="name" placeholder="Enter your name" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <div class="button input-box">
                                <input type="submit" value="Submit">
                            </div>
                            <div class="text sign-up-text">Already have an account? <label for="flip">Login now</label>
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
<!-- bắt sự kiện submit form -->
<script>
document.getElementById('form').addEventListener('submit', function(e) {
    e.preventDefault(); // Ngăn trình duyệt gửi form mặc định

    const email = document.querySelector('input[name="email"]').value;
    const password = document.querySelector('input[name="password"]').value;

    if (!email || !password) {
        alert('Vui lòng nhập đầy đủ thông tin');
        return;
    }

    // Gửi dữ liệu đến server qua fetch
    fetch('/dang-nhap/xu-ly-dang-nhap', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value // Thêm CSRF token
            },
            body: JSON.stringify({
                email: email,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message); // Hiển thị thông báo thành công
                window.location.href = '/'; // Điều hướng về trang chủ
            } else {
                alert('Tài khoản hoặc mật khẩu không đúng'); // Hiển thị lỗi
            }
        })
        .catch(error => {
            console.error('Đã xảy ra lỗi:', error);
            alert('Tài khoản hoặc mật khẩu không đúng');
        });
});
</script>


@stop