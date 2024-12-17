<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Forgot Password</title>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">
                            @if(Session::has('success'))
                            <h4 class="text-success">{{ Session::get('success') }}</h4>
                            @endif
                            @if(Session::has('error'))
                            <h4 class="text-danger">{{ Session::get('error') }}</h4>
                            @endif
                            <h6 class="font-weight-light">Lấy lại mật khẩu</h6>
                            <form class="pt-3" method="post" action={{ route('admin.processing.change-pw') }}>
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        id="exampleInputEmail1" placeholder="Email" required>
                                </div>
                                <button type="button"
                                    class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn send-otp">Gửi
                                    OTP</button>
                                <div class="form-group">
                                    <input type="password" name="otp" class="form-control form-control-lg" id="otp"
                                        placeholder="Nhập mã OTP">
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-control-lg"
                                        id="exampleInputPassword1" placeholder="Mật khẩu">
                                </div>
                                <div class="form-group">
                                    <input type="password" name="confirm-password" class="form-control form-control-lg"
                                        id="exampleInputPassword2" placeholder="Nhập lại mật khẩu">
                                </div>
                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">Gửi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('js/off-canvas.js') }}"></script>
    <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/misc.js') }}"></script>
    <!-- endinject -->
</body>

<script>
document.querySelector('.send-otp').addEventListener('click', function() {
    var email = document.querySelector('input[name="email"]').value;
    if (email == '') {
        alert('Vui lòng nhập email');
        return;
    }
    var data = {
        email: email
    };
    fetch('/admin/send-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Mã OTP đã được gửi đến email của bạn');
            } else {
                alert('Có lỗi xảy ra');
            }
        });
});
</script>

</html>