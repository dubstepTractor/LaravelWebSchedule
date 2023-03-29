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

use stdClass;

class HomeDefaultController extends HomeController
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
        $weekdays = new Collection();

        foreach(WeekDay::LIST as $id => $data) {
            $weekdays[$id] = WeekDay::get($id);
        }

        return view('admin.edit.default', [
            'alert'    => $alert,
            'groups'   => Group::all(true),
            'teachers' => Teacher::all(),
            'weekdays' => $weekdays
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
        // Validate id and get selected Group from POST.

        $id = $request->input('group-select');

        if($id === null or !is_numeric($id)) {
            return $this->index('Указанная группа не найдена');
        }

        $id    = intval(trim($id));
        $group = Group::byId($id);

        if(!isset($group)) {
            return $this->index('Указанная группа не найдена');
        }

        // Get all GroupDaySchedule and Subject data from POST.

        $days    = new Collection();
        $indexes = new Collection([
            'subject'    => Subject::INDEX_NAME,
            'classroom'  => Subject::INDEX_CLASSROOM,
            'teacher'    => Subject::INDEX_TEACHER_ID,
            'second'     => Subject::INDEX_SECOND_TEACHER_ID,
            'commentary' => Subject::INDEX_COMMENTARY
        ]);

        foreach($request->post() as $post_name => $value) {
            $part = explode('-', $post_name);

            if(count($part) < 3) {
                continue;
            }

            if(!is_numeric($part[0]) or !is_numeric($part[1])) {
                continue;
            }

            $day_i     = intval($part[0]);
            $subject_i = intval($part[1]);
            $param     = $part[2];

            if(!isset($days[$day_i])) {
                $days[$day_i] = new Collection();
            }

            if(!isset($days[$day_i][$subject_i])) {
                $days[$day_i][$subject_i] = new Collection();
            }

            if(!isset($indexes[$param])) {
                continue;
            }

            $days[$day_i][$subject_i][$indexes[$param]] = $value;
        }

        // Insert all GroupDaySchedule data to database if not exists.

        $day_ids = new Collection();

        foreach($days as $day_i => $day) {
            // Insert all Subject data to database if not exists.

            $subject_ids = new Collection();

            foreach($day as $subject_i => $data) {
                if(!isset($data[Subject::INDEX_NAME]) or $data[Subject::INDEX_NAME] === null) {
                    continue;
                }

                $subject_id = DB::table(Subject::TABLE)
                    ->where(Subject::INDEX_NAME,              $data[Subject::INDEX_NAME])
                    ->where(Subject::INDEX_CLASSROOM,         $data[Subject::INDEX_CLASSROOM] ?? null)
                    ->where(Subject::INDEX_TEACHER_ID,        $data[Subject::INDEX_TEACHER_ID] ?? null)
                    ->where(Subject::INDEX_SECOND_TEACHER_ID, $data[Subject::INDEX_SECOND_TEACHER_ID] ?? null)
                    ->where(Subject::INDEX_COMMENTARY,        $data[Subject::INDEX_COMMENTARY] ?? null)
                    ->value(Subject::INDEX_ID);

                if(!isset($subject_id)) {
                    $subject_id = DB::table(Subject::TABLE)->insertGetId($data->all());
                }

                $subject_ids[$subject_i] = $subject_id;
            }

            if($subject_ids->isEmpty()) {
                continue;
            }

            $day_id = DB::table(GroupDaySchedule::TABLE)
                ->where(GroupDaySchedule::INDEX_TARGET_ID,          $id)
                ->where(GroupDaySchedule::INDEX_FIRST_SUBJECT_ID,   $subject_ids[0] ?? null)
                ->where(GroupDaySchedule::INDEX_SECOND_SUBJECT_ID,  $subject_ids[1] ?? null)
                ->where(GroupDaySchedule::INDEX_THIRD_SUBJECT_ID,   $subject_ids[2] ?? null)
                ->where(GroupDaySchedule::INDEX_FOURTH_SUBJECT_ID,  $subject_ids[3] ?? null)
                ->where(GroupDaySchedule::INDEX_FIFTH_SUBJECT_ID,   $subject_ids[4] ?? null)
                ->where(GroupDaySchedule::INDEX_SIXTH_SUBJECT_ID,   $subject_ids[5] ?? null)
                ->where(GroupDaySchedule::INDEX_SEVENTH_SUBJECT_ID, $subject_ids[6] ?? null)
                ->where(GroupDaySchedule::INDEX_EIGHTH_SUBJECT_ID,  $subject_ids[7] ?? null)
                ->where(GroupDaySchedule::INDEX_NINTH_SUBJECT_ID,   $subject_ids[8] ?? null)
                ->where(GroupDaySchedule::INDEX_TENTH_SUBJECT_ID,   $subject_ids[9] ?? null)
                ->value(GroupDaySchedule::INDEX_ID);

            if(!isset($day_id)) {
                $day_id = DB::table(GroupDaySchedule::TABLE)->insertGetId([
                    GroupDaySchedule::INDEX_TARGET_ID          => $id,
                    GroupDaySchedule::INDEX_FIRST_SUBJECT_ID   => $subject_ids[0] ?? null,
                    GroupDaySchedule::INDEX_SECOND_SUBJECT_ID  => $subject_ids[1] ?? null,
                    GroupDaySchedule::INDEX_THIRD_SUBJECT_ID   => $subject_ids[2] ?? null,
                    GroupDaySchedule::INDEX_FOURTH_SUBJECT_ID  => $subject_ids[3] ?? null,
                    GroupDaySchedule::INDEX_FIFTH_SUBJECT_ID   => $subject_ids[4] ?? null,
                    GroupDaySchedule::INDEX_SIXTH_SUBJECT_ID   => $subject_ids[5] ?? null,
                    GroupDaySchedule::INDEX_SEVENTH_SUBJECT_ID => $subject_ids[6] ?? null,
                    GroupDaySchedule::INDEX_EIGHTH_SUBJECT_ID  => $subject_ids[7] ?? null,
                    GroupDaySchedule::INDEX_NINTH_SUBJECT_ID   => $subject_ids[8] ?? null,
                    GroupDaySchedule::INDEX_TENTH_SUBJECT_ID   => $subject_ids[9] ?? null
                ]);
            }

            $day_ids[$day_i] = $day_id;
        }

        // Insert GroupWeekSchedule data to database.

        if($day_ids->isEmpty()) {
            return $this->index('Нельзя добавить пустое расписание');
        }

        DB::table(GroupWeekSchedule::TABLE)->insert([
            GroupWeekSchedule::INDEX_TARGET_ID           => $id,
            GroupWeekSchedule::INDEX_FIRST_SCHEDULE_ID   => $day_ids[0] ?? null,
            GroupWeekSchedule::INDEX_SECOND_SCHEDULE_ID  => $day_ids[1] ?? null,
            GroupWeekSchedule::INDEX_THIRD_SCHEDULE_ID   => $day_ids[2] ?? null,
            GroupWeekSchedule::INDEX_FOURTH_SCHEDULE_ID  => $day_ids[3] ?? null,
            GroupWeekSchedule::INDEX_FIFTH_SCHEDULE_ID   => $day_ids[4] ?? null,
            GroupWeekSchedule::INDEX_SIXTH_SCHEDULE_ID   => $day_ids[5] ?? null,
            GroupWeekSchedule::INDEX_SEVENTH_SCHEDULE_ID => $day_ids[6] ?? null
        ]);

        return $this->index('Задано новое расписание на семестр для группы '.$group->getNumber().' '.$group->getLetter());
    }
}
