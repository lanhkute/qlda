

@extends('template.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/login.css') }}">
@stop

@section('title', 'Đăng nhập')

@section('content')
    <div class="grid" style="width: 100%;">
        <div class="container">
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
                    <!-- Login Form -->
                    <div class="login-form">
                        <div class="title"> Reset Password</div>
                        <form method="POST" action="{{ route('user.loginProcessing') }}">
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
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input name="email" type="email" placeholder="Enter your email" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input name="password1" type="password" placeholder="Enter your new password" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input name="password2" type="password" placeholder="Confirm your new password" required>
                            </div>
                            
                            <div class="button input-box">
                                <input type="submit" value="Reset Password">
                            </div>
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
@stop
