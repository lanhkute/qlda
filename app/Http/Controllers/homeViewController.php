<?php

namespace App\Http\Controllers;

use App\Models\course;
use App\Models\View_history;
use App\Models\lesson;
use App\Models\order;
use App\Models\question;
use App\Models\result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Models\user;


class homeViewController extends Controller
{
    public function course(Request $request){
        $search = $request->get('keyword');
        $course = course::query()
            ->select('courses.*','admins.name as name_admin',DB::raw('COUNT(lessons.courses_id) as number_lesson'))
            ->join('admins', 'courses.id_admin', '=', 'admins.id')
            ->leftJoin('lessons' , 'courses.id', '=', 'lessons.courses_id')
            ->where('courses.type', '=', '2')
            ->where('courses.name', 'like', '%'. $search .'%')
            ->groupBy('courses.id')
            ->paginate(12);;
        return view('content.user.course',[
            'courses' => $course,
        ]);
    }

    public function viewCourse($course_id){
        $course = course::query()
            ->select('courses.*','admins.name as name_admin','admins.image as avatar',DB::raw('COUNT(lessons.courses_id) as number_lesson'), DB::raw('AVG(`orders`.`rate`) AS rate_course'))
            ->join('admins', 'courses.id_admin', '=', 'admins.id')
            ->leftJoin('orders' , 'courses.id', '=', 'orders.courses_id')
            ->leftJoin('lessons' , 'courses.id', '=', 'lessons.courses_id')
            ->where('courses.type', '=', '2')
            ->where('courses.id', '=', $course_id)
            ->groupBy('courses.id')
            ->firstOrFail();

        $lessons = lesson::query()
            ->select('*')
            ->where('courses_id', '=', $course_id)
            ->get();

        $order_rate = order::query()
            ->select('orders.rate', 'orders.comment', 'orders.updated_at', 'users.name', 'users.image', 'users.id as id_user', "orders.id as id_order")
            ->join('users', 'orders.users_id', '=', 'users.id')
            ->where('orders.courses_id', '=', $course_id)
            ->where('orders.rate', '!=', 'null')
            ->orderBy('orders.updated_at', 'desc')
            ->paginate(10);

        $my_order = order::query()
            ->select('*')
            ->where('users_id', '=', session()->get('id'))
            ->where('courses_id', '=', $course_id)
            ->first();
        
        $check = 1;
        if(session()->has('id_course')){
            foreach (session()->get('id_course') as $cour){
                if ($cour == $course_id){
                    $check = 2;
                }
            }
        }

        if (isset($my_order->id)){
            $check = 3;
        }

        return view('content.user.viewCourse',[
            'courses'    => $course,
            'lessons'    => $lessons,
            'check'      => $check,
            'order_rate' => $order_rate,
            'my_order'   => $my_order,
        ]);
    }

    public function orderCourse($course_id){

        $course = course::query()
            ->select('courses.*','admins.name as author')
            ->join('admins', 'courses.id_admin', '=', 'admins.id')
            ->where('courses.type', '=', '2')
            ->where('courses.id', '=', $course_id)
            ->firstOrFail();

        session()->push('id_course', $course->id);
        session()->push('name_course', $course->name);
        session()->push('price_course', $course->price);
        session()->push('author_course', $course->author);

        return response()->json(['status' => 'success', 'message' => 'Đã thêm khóa học vào giỏ hàng']);
    
    }

    public function unOrderCourse($course_id) {
        $id = session()->get('id_course') == null ? [] : session()->get('id_course');
        $name = session()->get('name_course') == null ? [] : session()->get('name_course');
        $price = session()->get('price_course') == null ? [] : session()->get('price_course');
        $author = session()->get('author_course') == null ? [] : session()->get('author_course');

        session()->forget('id_course');
        session()->forget('name_course');
        session()->forget('price_course');
        session()->forget('author_course');

        for ($i = 0; $i < count($id) ; $i++) {
            if($id[$i] != $course_id){
                session()->push('id_course', $id[$i]);
                session()->push('name_course', $name[$i]);
                session()->push('price_course', $price[$i]);
                session()->push('author_course', $author[$i]);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Đã xóa khóa học khỏi giỏ hàng']);
    }

    public function buyCourse(Request $request){
        $id_course = explode(",", $request->get('id-buy'));
        $id_oder = explode(",", $request->get('id-not-buy'));
        for ($i = 0; $i < count($id_course); $i++){
            $course = course::find($id_course[$i]);
            $order = order::query()
            ->create([
                'users_id'  => session()->get('id'),
                'courses_id' => $course->id,
                'price_buy'  => $course->price,
            ]);
            View_history::query()
            ->create([
                'users_id'  => session()->get('id'),
                'courses_id' => $course->id,
                'number_view'  => 1,
            ]);
        }

        $id = session()->get('id_course') == null ? [] : session()->get('id_course');
        $name = session()->get('name_course') == null ? [] : session()->get('name_course');
        $price = session()->get('price_course') == null ? [] : session()->get('price_course');
        $author = session()->get('author_course') == null ? [] : session()->get('author_course');

        session()->forget('id_course');
        session()->forget('name_course');
        session()->forget('price_course');
        session()->forget('author_course');

        // for ($i = 0; $i < count($id) ; $i++) {
        //     if(in_array($id[$i], $id_oder)){
        //         session()->push('id_course', $id[$i]);
        //         session()->push('name_course', $name[$i]);
        //         session()->push('price_course', $price[$i]);
        //         session()->push('author_course', $author[$i]);
        //     }
        // }


        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://127.0.0.1:8000/khoa-hoc-cua-toi";
        $vnp_TmnCode = config('services.vnpay.tmncode');
        $vnp_HashSecret = config('services.vnpay.hashsecret');
        $vnp_TxnRef = date("YmdHis");
        $vnp_OrderInfo = 'Thanh toán đơn hàng';
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $price[0] * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'VNBANK';
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_IpAddr" => $vnp_IpAddr
        );


        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect()->to($vnp_Url);
    }

    public function myCourse(){
        $course = course::query()
            ->select('courses.*','admins.name as name_admin',DB::raw('COUNT(lessons.courses_id) as number_lesson'))
            ->join('admins', 'courses.id_admin', '=', 'admins.id')
            ->join('orders', 'courses.id', '=', 'orders.courses_id')
            ->leftJoin('lessons' , 'courses.id', '=', 'lessons.courses_id')
            ->where('courses.type', '=', '2')
            ->where('orders.users_id', '=', session()->get('id'))
            ->groupBy('courses.id')
            ->paginate(12);

        return view('content.user.myCourse',[
            'courses' => $course,
        ]);
    }

    public function ratingCourse(Request $request, $course_id){
        DB::table('orders')
        ->where('users_id', '=', session()->get('id'))
        ->where('courses_id', '=', $course_id)
        ->update([
            'rate' => $request->get('rating'),
            'comment' => $request->get('comment'),
            'updated_at' => now()
        ]);
        return redirect()->route('home.viewCourse', $course_id);
    }
    
    public function deleteRatingCourse(Request $request, $course_id){
        DB::table('orders')
        ->where('users_id', '=', session()->get('id'))
        ->where('courses_id', '=', $course_id)
        ->update([
            'rate' => null,
            'comment' => null
        ]);
        return redirect()->route('home.viewCourse', $course_id);
    }

    public function myCart(){
        return view('content.user.myCart');
    }

    public function updateViewHistory($course, $number_update){
        $number_lesson = course::query()
            ->select(DB::raw('COUNT(lessons.id) as number_lesson'))
            ->leftJoin('lessons' , 'courses.id', 'lessons.courses_id')
            ->where('courses.id', '=', $course)
            ->groupBy('courses.id')
            ->first();
        if ($number_lesson->number_lesson >= $number_update){
            $view =  DB::table('view_histories')
                ->where('users_id', session()->get('id'))
                ->where('courses_id', $course)
                ->update(['number_view' => $number_update]);
            return true;
        }else{
            return false;
        }
    }

    public function updateResult($question_id,$true_number,$false_number){
        $result = result::find($question_id);
        $result->number_true += $true_number;
        $result->number_false += $false_number;
        $result->save();
    }

    public function learnCourse($course_id, $lesson_id){
        $course_check = View_history::query()
                ->select('number_view')
                ->where('users_id', '=', session()->get('id'))
                ->where('courses_id', '=', $course_id)
                ->first();
        if (!isset($course_check)){
            return redirect()->route('home.viewCourse', $course_id);
        }elseif ($course_check->number_view < $lesson_id){
            return redirect()->route('home.learnCourse', [$course_id, $course_check->number_view]);
        }
        $lesson_id -= 1;
        $lessons = lesson::query()
            ->select('lessons.*', DB::raw('COUNT(questions.id) as number_question'))
            ->leftJoin('questions', 'lessons.id', '=', 'questions.lessons_id')
            ->where('lessons.courses_id', '=', $course_id)
            ->groupBy('lessons.id')
            ->get();

            $order_rate = order::query()
            ->select('orders.rate', 'orders.comment', 'orders.updated_at', 'users.name', 'users.image', 'users.id as id_user', "orders.id as id_order")
            ->join('users', 'orders.users_id', '=', 'users.id')
            ->where('orders.courses_id', '=', $course_id)
            ->where('orders.rate', '!=', 'null')
            ->orderBy('orders.updated_at', 'desc')
            ->paginate(10);

        $my_order = order::query()
            ->select('*')
            ->where('users_id', '=', session()->get('id'))
            ->where('courses_id', '=', $course_id)
            ->first();
        
        $check = 1;
        if(session()->has('id_course')){
            foreach (session()->get('id_course') as $cour){
                if ($cour == $course_id){
                    $check = 2;
                }
            }
        }

        if (isset($my_order->id)){
            $check = 3;
        }

        return view('content.user.learnCourse',[
            'lessons'       => $lessons,
            'course_id'     => $course_id,
            'lesson_id'     => $lesson_id,
            'number_learn'  => $course_check->number_view,
            'check'      => $check,
            'order_rate' => $order_rate,
            'my_order'   => $my_order,
        ]);
    }

    public function next_lesson($course_id, $lesson_id){
        $course_check = View_history::query()
                ->select('number_view')
                ->where('users_id', '=', session()->get('id'))
                ->where('courses_id', '=', $course_id)
                ->first();
        if (!isset($course_check->number_view)){
            return redirect()->route('home.viewCourse', $course_id);
        }elseif ($course_check->number_view < $lesson_id){
            return redirect()->route('home.learnCourse', [$course_id, $course_check->number_view]);
        }elseif ($course_check->number_view > $lesson_id){
            return redirect()->route('home.learnCourse', [$course_id, $lesson_id+1]);
        }
        $lessons = lesson::query()
                ->where('lessons.courses_id', '=', $course_id)
                ->groupBy('lessons.id')
                ->get();
        $questions = question::query()
                ->select('questions.id', 'questions.question', 'questions.type', 'answers.answer', 'answers.id as id_answer')
                ->leftJoin('answers', 'questions.id', 'answers.questions_id')
                ->where('lessons_id', '=', $lessons[$lesson_id - 1]->id)
                ->get();
        if (count($questions)==0){
            if ($this->updateViewHistory($course_id, $lesson_id+1) == false){
                return redirect()->route('home.doneCourse', $course_id);
            }
            return redirect()->route('home.learnCourse', [$course_id, $lesson_id+2]);
        }
        return view('content.user.answerLesson',[
            'course_id' => $course_id,
            'lesson_id' => $lesson_id,
            'lesson'    => $lessons[$lesson_id - 1],
            'questions' => $questions,
        ]);
    }

    public function check_answer(Request $request,$course_id, $lesson_id){
        $questions = question::query()
                ->select('questions.id', 'questions.question', 'questions.type', 'answers.answer', 'answers.id as id_answer', 'answers.check')
                ->leftJoin('answers', 'questions.id', 'answers.questions_id')
                ->where('lessons_id', '=', $request->get('id_lesson'))
                ->get();
        $id_question = 0;
        $number_question = 0;
        $number_false = 0;
        $check_false = true;
        foreach ($questions as $index=>$question) {
            if ($id_question != $question->id) {
                $id_question = $question->id;
                $number_question++;
                if ($check_false == false) {
                    $number_false ++;
                    $this->updateResult($questions[$index-1]->id, 0, 1);
                }elseif($index > 0){
                    $this->updateResult($questions[$index-1]->id, 1, 0);
                }
                $check_false = true;
            }
            if ($question->type == 1){
                if (($request->get("a$question->id_answer") == '' && $question->check == 1) || ($request->get("a$question->id_answer") == 'on' && $question->check == 0)){
                    $check_false = false;
                }
            }
            if ($question->type == 2){
                if (($request->get("$question->id") == $question->id_answer) && ($question->check == 0)){
                    $check_false = false;
                }
            }
        }
        if ($check_false == false) {
            $number_false ++;
            $this->updateResult($questions[count($questions)-1]->id, 0, 1);
        }else{
            $this->updateResult($questions[count($questions)-1]->id, 1, 0);
        }
        if ($number_false < $number_question - $number_false){
            if ($this->updateViewHistory($course_id, $lesson_id+1) == false){
                return redirect()->route('home.doneCourse', $course_id);
            }
        }
        return view('content.user.resultAnswer',[
            'course_id'     => $course_id,
            'lesson_id'     => $lesson_id,
            'true_number'   => $number_question - $number_false,
            'false_number'  => $number_false,
        ]);
    }

    public function done_course($course_id){
        $courses = course::query()
            ->select('courses.*','admins.name as name_admin',DB::raw('COUNT(lessons.id) as number_lesson'))
            ->join('admins', 'courses.id_admin', '=', 'admins.id')
            ->leftJoin('lessons' , 'courses.id', 'lessons.courses_id')
            ->where('courses.id', '=', $course_id)
            ->groupBy('courses.id')
            ->first();
        $view =  View_history::query()
            ->where('users_id', session()->get('id'))
            ->where('courses_id', $course_id)
            ->first();
        if ($courses->number_lesson <  $view->number_view){
            return redirect()->route('home.learnCourse',[$course_id, $courses->number_lesson]);
        }
        return view('content.user.doneCourse',[
            'course' => $courses,
        ]);
    }

    public function forgotPassword() {
        return view("content.user.forgotpw");
    }

    public function sendOtp(Request $request) {
        $otp = rand(1000, 9999);
        // save in db
        $email = $request->get('email');
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->otp = $otp;
            $user->save();
        } else {
            return response()->json(['status' => 'error', 'message' => 'Người dùng không tồn tại']);
        }

        try { 
            Mail::to($email)->send(new OtpMail($user->name, $otp));
        } catch (Exception $e) { 
            return  $e->getMessage();
        }

        return response()->json(['status' => 'success', 'message' => 'Gửi mã OTP thành công']);
    }

    public function resetPassword(Request $request) {
        $email = $request->get('email');
        $otp = $request->get('otp');
        $password = $request->get('password');
        $user = User::where('email', $email)->where('otp', $otp)->first();
        if ($user) {
            $user->password = bcrypt($password);
            $user->otp = null;
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'Đổi mật khẩu thành công']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Mã OTP không chính xác']);
        }
    }
}