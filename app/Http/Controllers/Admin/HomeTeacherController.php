<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

use App\Objects\Util\Util;
use App\Objects\Util\WeekDay;
use App\Objects\Base\Group;
use App\Objects\Base\Teacher;
use App\Objects\Base\Subject;
use App\Objects\Schedule\Day\GroupDaySchedule;
use App\Objects\Schedule\Week\GroupWeekSchedule;

class HomeTeacherController extends HomeController
{
    /**
     * Show the choosing form.
     *
     * @param  string|null $alert
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(?string $alert = null)
    {
        return view('admin.edit.teacher', [
            'alert'    => $alert,
            'teachers' => Teacher::all()
        ]);
    }

    /**
     * For POST request.
     *
     * @param  Request $request
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        if($request->input('teacher-add') !== null) {
            $name       = $request->input('name');
            $surname    = $request->input('surname');
            $patronymic = $request->input('patronymic');

            if(!isset($name)) {
                return $this->index("Для регистрации преподавателя необходимо указать имя");
            }

            if(!isset($surname)) {
                return $this->index("Для регистрации преподавателя необходимо указать фамилию");
            }

            if(!is_string($name)) {
                return $this->index("Имя преподавателя задано неверно");
            }

            $name = Util::mb_ucfirst(mb_strtolower($name));

            if(!is_string($surname)) {
                return $this->index("Фамилия преподавателя задано неверно");
            }

            $surname = Util::mb_ucfirst(mb_strtolower($surname));
            $data    = [
                Teacher::INDEX_NAME    => $name,
                Teacher::INDEX_SURNAME => $surname
            ];

            if(isset($patronymic)) {
                if(!is_string($patronymic)) {
                    return $this->index("Отчество преподавателя задано неверно");
                }

                $data[Teacher::INDEX_PATRONYMIC] = Util::mb_ucfirst(mb_strtolower($patronymic));
            }

            DB::table(Teacher::TABLE)->updateOrInsert($data, $data);

            return $this->index("Добавлен преподаватель $surname $name");
        }

        $remove_id = $request->input('teacher-remove');

        if(isset($remove_id)) {
            $remove_id = intval($remove_id);
            $teacher   = Teacher::byId($remove_id);

            if(!isset($teacher)) {
                return $this->index("Указанный преподаватель не найдена");
            }

            DB::table(Teacher::TABLE)->where(Teacher::INDEX_ID, $remove_id)->delete();

            $name    = $teacher->getName();
            $surname = $teacher->getSurname();

            return $this->index("Преподаватель $surname $name удален");
        }

        return $this->index();
    }
}
