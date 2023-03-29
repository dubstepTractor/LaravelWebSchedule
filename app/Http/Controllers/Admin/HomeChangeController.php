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
use App\Objects\Schedule\Day\Change\ScheduleChange;
use App\Objects\Schedule\Week\GroupWeekSchedule;

use DateTime;

class HomeChangeController extends HomeController
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
        return view('admin.edit.change', [
            'alert'        => $alert,
            'groups'       => Group::all(true),
            'teacher_list' => Teacher::all(),
            'date'         => new DateTime('today')
        ]);
    }

    /**
     * Show the schedule changes for certain group.
     *
     * @param  mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($id = null) {

        // Validate id and get selected Group.

        if($id === null or !is_numeric($id)) {
            return $this->index('Выберите группу из списка');
        }

        $id    = intval(trim($id));
        $group = Group::byId($id);

        if(empty($group)) {
            return $this->index('Указанная группа не найдена');
        }

        // Get GroupWeekSchedule for selected Group.

        $week = GroupWeekSchedule::byTargetCurrent($group->getId());

        if(empty($week)) {
            return $this->index('Группа '.$group->getNumber(). ' '. $group->getLetter(). ' не имеет расписания');
        }

        // Get ISO start of the week to get all changes.

        $date = (new DateTime())->setTimestamp(strtotime('last monday', strtotime('tomorrow')));

        $days     = new Collection();
        $subjects = new Collection();
        $teachers = new Collection();

        foreach(ScheduleChange::byTargetAndDate($week->getId(), $date, true) as $day) {
            if(!isset($day) or $day->getId() === null) {
                continue;
            }

            $days[]                  = $day;
            $subjects[$day->getId()] = new Collection();

            $subject_i = 0;

            foreach(Subject::byDay($day) as $subject) {
                $subjects[$day->getId()][] = $subject;

                if(!isset($subject) or $subject->getId() === null) {
                    $subject_i--;

                    continue;
                }

                $subject_i = 0;

                $teachers[$subject->getId()] = new Collection();

                foreach(Teacher::bySubject($subject) as $teacher) {
                    if(!isset($teacher) or $teacher->getId() === null) {
                        continue;
                    }

                    $teachers[$subject->getId()][] = $teacher;
                }
            }

            // Remove last unused subjects.

            if($subject_i !== 0) {
                $subjects[$day->getId()] = $subjects[$day->getId()]->slice(0, $subject_i);
            }
        }

        if($days->isEmpty()) {
            return $this->index('Группа '.$group->getNumber(). ' '. $group->getLetter(). ' не имеет никаких замен на текущую неделю');
        }

        return view('admin.edit.change', [
            'groups'       => Group::all(true),
            'teacher_list' => Teacher::all(),
            'date'         => new DateTime('today'),
            'days'         => $days,
            'subjects'     => $subjects,
            'teachers'     => $teachers
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
        $id = $request->input('group-select');

        // Show ScheduleChanges for selected Group.

        if($request->input('change-show') !== null) {
            return redirect('/home/change/'.$id);
        }

        // Remove ScheduleChange.

        $remove_id = $request->input('change-remove');

        if(isset($remove_id)) {
            $remove_id = intval($remove_id);
            $change    = ScheduleChange::byId($remove_id);

            if(!isset($change)) {
                return $this->index("Указанная замена не найдена");
            }

            DB::table(ScheduleChange::TABLE)->where(ScheduleChange::INDEX_ID, $remove_id)->delete();

            $date = $change->getDate()->format(ScheduleChange::FORMAT_DATE);
            $week = GroupWeekSchedule::byId($change->getId());

            if(!isset($week)) {
                return $this->index("Удалена замена расписания на $date");
            }

            $group = Group::byId($week->getTargetId());

            return $this->index('Удалена замена расписания на '.$date.' для группы '.$group->getNumber().' '.$group->getLetter());
        }

        // Add ScheduleChange to Group.

        if($request->input('change-add') !== null) {

            // Validate id and get selected Group.

            if($id === null or !is_numeric($id)) {
                return $this->index('Указанная группа не найдена');
            }

            $id    = intval(trim($id));
            $group = Group::byId($id);

            if(!isset($group)) {
                return $this->index('Указанная группа не найдена');
            }

            // Get GroupWeekSchedule for selected Group.

            $week = GroupWeekSchedule::byTargetCurrent($group->getId());

            if(empty($week)) {
                return $this->index('Группа '.$group->getNumber(). ' '. $group->getLetter(). ' не имеет расписания');
            }

            // Get DateTime from POST.

            $date = new DateTime('today');

            $day   = $request->input('day')   ?? $date->format('d');;
            $month = $request->input('month') ?? $date->format('m');;
            $year  = $request->input('year')  ?? $date->format('Y');;

            if(!is_numeric($day) or !is_numeric($month) or !is_numeric($year)) {
                return $this->index('Дата замены указана неверно');
            }

            // Get all Subject data from POST.

            $subjects = new Collection();
            $indexes  = new Collection([
                'subject'    => Subject::INDEX_NAME,
                'classroom'  => Subject::INDEX_CLASSROOM,
                'teacher'    => Subject::INDEX_TEACHER_ID,
                'second'     => Subject::INDEX_SECOND_TEACHER_ID,
                'commentary' => Subject::INDEX_COMMENTARY
            ]);

            foreach($request->post() as $post_name => $value) {
                $part = explode('-', $post_name);

                if(count($part) < 2) {
                    continue;
                }

                if(!is_numeric($part[0])) {
                    continue;
                }

                $subject_i = intval($part[0]);
                $param     = $part[1];

                if(!isset($subjects[$subject_i])) {
                    $subjects[$subject_i] = new Collection();
                }

                if(!isset($indexes[$param])) {
                    continue;
                }

                $subjects[$subject_i][$indexes[$param]] = $value;
            }

            // Insert all Subject data to database if not exists.

            $subject_ids = new Collection();

            foreach($subjects as $subject_i => $data) {
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

            // Insert ScheduleChange data to database.

            $date = DateTime::createFromFormat(ScheduleChange::FORMAT_DATE, $year.'-'.$month.'-'.$day)->setTime(0, 0)->format(ScheduleChange::FORMAT_DATE);

            DB::table(ScheduleChange::TABLE)->insert([
                ScheduleChange::INDEX_TARGET_ID          => $week->getId(),
                ScheduleChange::INDEX_FIRST_SUBJECT_ID   => $subject_ids[0] ?? null,
                ScheduleChange::INDEX_SECOND_SUBJECT_ID  => $subject_ids[1] ?? null,
                ScheduleChange::INDEX_THIRD_SUBJECT_ID   => $subject_ids[2] ?? null,
                ScheduleChange::INDEX_FOURTH_SUBJECT_ID  => $subject_ids[3] ?? null,
                ScheduleChange::INDEX_FIFTH_SUBJECT_ID   => $subject_ids[4] ?? null,
                ScheduleChange::INDEX_SIXTH_SUBJECT_ID   => $subject_ids[5] ?? null,
                ScheduleChange::INDEX_SEVENTH_SUBJECT_ID => $subject_ids[6] ?? null,
                ScheduleChange::INDEX_EIGHTH_SUBJECT_ID  => $subject_ids[7] ?? null,
                ScheduleChange::INDEX_NINTH_SUBJECT_ID   => $subject_ids[8] ?? null,
                ScheduleChange::INDEX_TENTH_SUBJECT_ID   => $subject_ids[9] ?? null,
                ScheduleChange::INDEX_DATE               => $date
            ]);

            return $this->index('Создана замена расписания на '.$date.' для группы '.$group->getNumber().' '.$group->getLetter());
        }

        return $this->index();
    }
}
