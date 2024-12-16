@extends('template.user')

@section('css')
{{-- Css code --}}
<style>
#course {
    background-color: rgba(0, 0, 0, .2);
}
</style>
@stop

@section('title')
Khóa học
@stop

@section('content')
<!-- <img class="main-img" width="100%"
    src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Finsieutoc.vn%2Fwp-content%2Fuploads%2F2021%2F02%2Fmau-banner-quang-cao-khuyen-mai.jpg&f=1&nofb=1&ipt=0c5706b48aa362c5254944609e9bafe44cda76618b1b90c133a60db626560cb6&ipo=images"
    alt=""> -->
<div class="banner-container">
    <div class="banner-slider">
        <?php
        $banners = [
            "https://firebasestorage.googleapis.com/v0/b/notion-6958d.appspot.com/o/1dcad280-6139-4667-bf2e-f015d8a8fb12.jpg?alt=media&token=d4d5e562-ac56-4e11-baf7-ba314718a886",
            "https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Finsieutoc.vn%2Fwp-content%2Fuploads%2F2021%2F02%2Fmau-banner-quang-cao-khuyen-mai.jpg&f=1&nofb=1",
            "https://cmavn.org/wp-content/uploads/Cover-illustration.jpg",
            "https://cmavn.org/wp-content/uploads/Banner-1-1-1536x657.jpg",
           
        ];
        foreach ($banners as $banner) {
            echo "<img class='main-img' width='100%' src='$banner' alt='Banner'>";
        }
        ?>
    </div>
    <button class="btn-prev">&lt;</button>
    <button class="btn-next">&gt;</button>
</div>

<style>
.banner-container {
    position: relative;
    width: 100%;
    overflow: hidden;
}

.banner-slider {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.main-img {
    width: 100%;
    height: 450px;
    flex: 0 0 100%;
    object-fit: fit;
    border-radius: 20px;
}

.btn-prev,
.btn-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    z-index: 10;
}

.btn-prev {
    left: 10px;
}

.btn-next {
    right: 10px;
}

.cont {
    display: flex;
    justify-content: space-evenly;
    margin: 0;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const slider = document.querySelector(".banner-slider");
    const banners = document.querySelectorAll(".main-img");
    let index = 0;

    function updateSlider() {
        slider.style.transform = `translateX(-${index * 100}%)`;
    }

    document.querySelector(".btn-next").addEventListener("click", () => {
        index = (index + 1) % banners.length;
        updateSlider();
    });

    document.querySelector(".btn-prev").addEventListener("click", () => {
        index = (index - 1 + banners.length) % banners.length;
        updateSlider();
    });
});
</script>


<div class="grid">
    <div class="grid__row cont">
        @foreach ($courses as $course)
        <div class="grid__column-3">
            <div class="product-item">
                <a href="{{ route('home.viewCourse', $course->id) }}">
                    <div class="product-item-img"
                        style="background-image: url({{ $course->image }}); background-position: center;">
                    </div>
                    <br>
                    <div class="product-item-name">{{ $course->name }}</div>
                </a>
                <div class="product-des">
                    <span><i class="fa-brands fa-youtube"></i>Tổng số bài học: {{ $course->number_lesson }}</span>
                    <span><i class="fa-solid fa-user"></i>Tác giả: {{ $course->name_admin }}</span>
                    <span><i class="fa-solid fa-id-card"></i>Giá: {{ $course->price }} VND</span>
                </div>
                <button class="btn-click"><a href="{{ route('home.viewCourse', $course->id) }}">Xem chi
                        tiết</a></button>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{ $courses->links() }}
@stop

@section('js')
@stop