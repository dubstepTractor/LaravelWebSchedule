<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

use App\Objects\Base\Group;
use App\Objects\Base\Teacher;
use App\Objects\Base\Subject;
use App\Objects\Util\WeekDay;
use App\Objects\Schedule\Day\GroupDaySchedule;
use App\Objects\Schedule\Week\GroupWeekSchedule;

class HomeGroupController extends HomeController
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
        return view('admin.edit.group', [
            'alert'  => $alert,
            'groups' => Group::all()
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
        if($request->input('group-add') !== null) {
            $number = $request->input('number');
            $letter = $request->input('letter');

            if(!isset($number)) {
                return $this->index("Для создания группы необходимо указать номер");
            }

            if(!isset($letter)) {
                return $this->index("Для создания группы необходимо указать букву");
            }

            if(!is_numeric($number)) {
                return $this->index("Номер группы задан неверно");
            }

            $number = intval($number);

            if(!is_string($letter)) {
                return $this->index("Буква группы задана неверно");
            }

            $letter = strtoupper($letter);
            $data   = [
                Group::INDEX_NUMBER => $number,
                Group::INDEX_LETTER => $letter
            ];

            DB::table(Group::TABLE)->updateOrInsert($data, $data);

            return $this->index("Добавлена группа $number $letter");
        }

        $remove_id = $request->input('group-remove');

        if(isset($remove_id)) {
            $remove_id = intval($remove_id);
            $group     = Group::byId($remove_id);

            if(!isset($group)) {
                return $this->index("Указанная группа не найдена");
            }

            DB::table(Group::TABLE)->where(Group::INDEX_ID, $remove_id)->delete();

            $number = $group->getNumber();
            $letter = $group->getLetter();

            return $this->index("Группа $number $letter Удалена");
        }

        return $this->index();
    }
}
