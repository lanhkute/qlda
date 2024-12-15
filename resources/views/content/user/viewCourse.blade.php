@extends('template.user')

@section('css')
<style>
#course {
    background-color: rgba(0, 0, 0, .2);
}

table,
td,
th {
    border: 1px solid;
    text-align: center;
    height: 30px;
}

table {
    width: calc(100% - 20px);
    font-size: 16px;
    padding: 10px;
    border-collapse: collapse;
}
</style>
<link rel="stylesheet" href="{{ asset('css/user/view_course.css') }}">
@stop

@section('title')
{{ $courses->name }}
@stop

@section('content')

<div class="grid">
    <div class="grid__row">
        {{-- Tên tác giả --}}
        <div class="info-author">
            <img class="avatar-author" src="{{ asset("images/avatar/" . $courses->avatar) }}"
                alt="{{ $courses->name }}">
            <div class="name-author">
                Tác giả: {{ $courses->name_admin }}
            </div>
        </div>
        {{-- Tên tác giả --}}

        {{-- Thông tin khóa học --}}
        <div class="my_course_view">
            <img src="{{  $courses->image }}" alt="{{ $courses->name }}">
            <div class="my_course_view_info">
                <div class="name_course">
                    <i class="fa-solid fa-book-open"></i>
                    Tên khóa học: {{ $courses->name }}
                </div>
                <p>
                    <i class="fa-solid fa-person-chalkboard"></i>
                    Số lượng bài học: {{ count($lessons) }} bài
                </p>
                <p>
                    <i class="fa-solid fa-money-bill-1-wave"></i>
                    Giá tiền: {{ $courses->price }} VND
                </p>
                <p>
                    <i class="fa-solid fa-pen-to-square"></i>
                    Ngày Tạo: {{ date('d-m-Y', strtotime($courses->created_at)) }}
                </p>
                <p>
                    <i class="fa-solid fa-pen-to-square"></i>
                    Cập nhập lần cuối: {{ date('d-m-Y', strtotime($courses->updated_at)) }}
                </p>
                <p>
                    <i class="fa-solid fa-user-pen"></i>
                    Đánh giá:
                    @if ($courses->rate_course != 0)
                    {{ round($courses->rate_course,2) }} <i class="fa-solid fa-star"
                        style="color: rgb(230, 83, 39);"></i>
                    @else
                    Chưa có lượt đánh giá
                    @endif
                </p>
                <p>
                    @if ($check == 1)
                    <a href="{{ route('home.orderCourse', $courses->id) }}">
                        <button class="btn-action-course">
                            Đặt khóa học
                            <i class="fa-solid fa-cart-plus"></i>
                        </button>
                    </a>
                    @elseif ($check == 2)
                    <a href="{{ route('home.myCart') }}">
                        <button class="btn-action-course">
                            Mua khóa học
                            <i class="fa-solid fa-cart-plus"></i>
                        </button>
                    </a>
                    @elseif ($check == 3)
                    <a href="{{ route('home.learnCourse', [$courses->id,1]) }}">
                        <button class="btn-action-course">
                            Học khóa học
                            <i class="fa-solid fa-tv"></i>
                        </button>
                    </a>
                    @endif
                </p>
            </div>
        </div>
        <div class="description-course" style="font-size: 16px">
            <div class="name-author" style="display: block">
                # Mô tả:
            </div>
            {!! $courses->description !!}
        </div>
        {{-- Thông tin khóa học --}}

        {{-- list bài học --}}
        <table style="margin-top: 10px">
            <tr>
                <th>
                    #
                </th>
                <th>
                    Tên bài học
                </th>
                @if ($check == 3)
                <th>
                    Xem ngay
                </th>
                @endif
            </tr>
            @foreach ($lessons as $index => $lesson)
            <tr>
                <td>
                    {{ $index + 1 }}
                </td>
                <td>
                    {{ $lesson->name }}
                </td>
                @if ($check == 3)
                <th>
                    <a href="{{ route('home.learnCourse',[$courses->id, $index + 1]) }}">
                        Xem ngay
                    </a>
                </th>
                @endif
            </tr>
            @endforeach
        </table>
        {{-- list bài học --}}

        {{-- Đánh giá --}}
        <div class="rating-course" style="margin-top: 15px">
            <br>
            <div class="name-author" style="display: block">
                # Đánh giá sản phẩm
            </div>
            @if ($check == 3)
            <form method="post" id="form" action="{{ route('home.ratingCourse', $courses->id) }}">
                @csrf
                <fieldset class="rating">
                    <input type="radio" id="star5" name="rating" value="5" /><label class="full" for="star5"
                        title="Awesome - 5 stars"></label>
                    {{-- <input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label> --}}
                    <input type="radio" id="star4" name="rating" value="4" /><label class="full" for="star4"
                        title="Pretty good - 4 stars"></label>
                    {{-- <input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label> --}}
                    <input type="radio" id="star3" name="rating" value="3" /><label class="full" for="star3"
                        title="Meh - 3 stars"></label>
                    {{-- <input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label> --}}
                    <input type="radio" id="star2" name="rating" value="2" /><label class="full" for="star2"
                        title="Kinda bad - 2 stars"></label>
                    {{-- <input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label> --}}
                    <input type="radio" id="star1" name="rating" value="1" /><label class="full" for="star1"
                        title="Sucks big time - 1 star"></label>
                    {{-- <input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label> --}}
                </fieldset>
                <br>
                <div>
                    <textarea class="comment-course" name="comment" cols="60" rows="6"
                        placeholder="Viết ra bình luận của bạn">{{ $my_order->comment }}</textarea>
                </div>
                <div>
                    <button class="btn-action-course submit-comment">
                        Bình Luận
                    </button>
                </div>
            </form>
            @else
            <div class="buy-course-alert">
                Mua ngay để đánh giá
            </div>
            @endif
        </div>
        {{-- Đánh giá --}}

        {{-- Đánh giá chi tiết --}}
        <div class="rating-course">
            <br><br>
            <div class="name-author" style="display: block">
                # Đánh giá của người đã mua
            </div>

            @foreach ($order_rate as $order)
            <div class="user-comment">
                <div class="">
                    <p style="font-size: 15px">
                        Ngày: {{ date('d-m-Y', strtotime($order->updated_at)) }}
                    </p>
                    <div class="rating-account" style="margin-top: 7px">
                        <img class="img-rating-account" src="{{ asset("images/avatar/avt.jpg") }}" alt="">
                        <div class="name-and-rating">
                            <p>
                                Tên: {{ $order->name }}
                            </p>
                            <p>
                                @for ($i = 0; $i < $order->rate; $i++)
                                    <i class="fa-solid fa-star" style="color: rgb(230, 83, 39);"></i>
                                    @endfor
                            </p>
                        </div>
                    </div>
                    <div style="font-size: 17px; margin-top: 10px">
                        {{ $order->comment }}
                    </div>
                </div>
                {{-- Xóa đánh giá --}}
                @if ($order->id_user == session('id'))
                <form id="delete_rating" method="POST" action="{{ route('home.deleteRatingCourse', $courses->id) }}">
                    @csrf
                    <button>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-trash-2">
                            <path d="M3 6h18" />
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                            <line x1="10" x2="10" y1="11" y2="17" />
                            <line x1="14" x2="14" y1="11" y2="17" />
                        </svg>
                    </button>
                </form>
                @endif
                {{-- Xóa đánh giá --}}
            </div>
            @endforeach
            <br>
            {{ $order_rate->links() }}
        </div>
        {{-- Đánh giá chi tiết --}}
    </div>
</div>
@stop

@section('js')
<script>
@if($my_order != null)
document.getElementById("star{{ $my_order->rate }}").checked = true;
@endif
document.getElementById("form").addEventListener("submit", function(event) {
    if (document.querySelector('input[name="rating"]:checked') == null) {
        alert("Vui lòng chọn số sao");
        event.preventDefault();
    }
});
document.getElementById("delete_rating").addEventListener("submit", function(event) {
    if (!confirm("Bạn có chắc chắn muốn xóa đánh giá này?")) {
        event.preventDefault();
    }
});
</script>
@stop