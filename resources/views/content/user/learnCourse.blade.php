@extends('template.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/learn_course.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/view_course.css') }}">

    <style>
        #my-course {
            background-color: rgba(0, 0, 0, .2);
        }
    </style>
@stop

@section('title')
    {{ $lessons[$lesson_id]->name }}
@stop

@section('content')

<div class="grid">
    <div class="grid__row">
        <div class="lesson">
            <div class="video_lesson">
                <iframe width="100%" height="100%" src="{{ $lessons[$lesson_id]->link }}" ></iframe>
            </div>
            <div class="list_lesson">
                <a href="{{ route('home.nextLesson',[$course_id,$lesson_id+1]) }}">
                    <button class="btn-next-lesson">
                        Bài kế tiếp
                    </button>
                </a>
                <ul class="list_lesson_view">
                    @for ($i = 0; $i < count($lessons); $i++)
                        <li class="lessons">
                            @if ($i <  $number_learn)
                                <a href="{{ route('home.learnCourse', [$course_id,$i+1]) }}" style="text-decoration: none; color: black;">
                                    Bài {{$i + 1}}: {{ $lessons[$i]->name }}
                                </a>
                            @else
                                <a style="cursor: not-allowed;">
                                    Bài {{$i + 1}}: {{ $lessons[$i]->name }}
                                </a>
                            @endif
                            <span class="icon-check">
                                @if ($i ==  $lesson_id)
                                    <i class="fa-solid fa-eye"></i>
                                @elseif ($i + 1 <=  $number_learn)
                                    <i class="fa-solid fa-check"></i>    
                                @elseif ($i + 1 >  $number_learn)
                                    <i class="fa-solid fa-exclamation"></i>
                                @endif
                            </span>
                            <br>
                            @for ($j = 1 ; $j <= $lessons[$i]->number_question ; $j++)
                                <span class="icon-question">
                                    {{ $j }}
                                </span>
                            @endfor
                        </li>
                    @endfor
                </ul>
            </div>
        </div>
        <div>
            <div class="name-lesson">
                Bài học: {{ $lessons[$lesson_id]->name }}
            </div>
            <br>
            <div class="description-lesson">
                <h3 style="margin-bottom: 10px">
                    # Mô tả
                </h3>
                {{ $lessons[$lesson_id]->description }}
            </div>
        </div>
    </div>
    {{-- Đánh giá --}}
        <div class="rating-course" style="margin-top: 15px">
            <br>
            <div class="name-author" style="display: block">
                # Đánh giá sản phẩm
            </div>
            @if ($check == 3)
            <form class="form-rating_h" method="post" id="form" action="{{ route('home.ratingCourse', $course_id) }}">
                @csrf
                <div class="form-rating">
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
                    <div class="comment-course" style="border: 1px solid #ccc;">
                        <textarea class="comment-course" name="comment" cols="60" rows="6"
                            placeholder="Viết ra bình luận của bạn">{{ $my_order->comment }}</textarea>
                    </div>
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
                <form id="delete_rating" method="POST" action="{{ route('home.deleteRatingCourse', $course_id) }}">
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
@stop

@section('js')
{{-- js here --}}
<script>
// Kiểm tra nếu $my_order không null, tự động đánh dấu sao tương ứng
@if($my_order-> rate != null)
document.getElementById("star{{ $my_order->rate }}").checked = true;
// Xử lý sự kiện submit form xóa đánh giá
document.getElementById("delete_rating").addEventListener("submit", function(event) {
    if (!confirm("Bạn có chắc chắn muốn xóa đánh giá này?")) {
        event.preventDefault();
    }
});
@endif

// Xử lý sự kiện submit form đánh giá
document.getElementById("form").addEventListener("submit", function(event) {
    // Kiểm tra nếu người dùng chưa chọn sao
    if (document.querySelector('input[name="rating"]:checked') == null) {
        alert("Vui lòng đánh giá sao");
        event.preventDefault();
        return;
    }

    // Kiểm tra nếu phần bình luận rỗng
    if (document.querySelector("textarea.comment-course").value.trim() == "") {
        alert("Vui lòng viết đánh giá");
        event.preventDefault();
        return;
    }
});

</script>
@stop
