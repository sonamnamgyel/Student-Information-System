<?php

class AdminCoursesController extends AdminController {


    /**
     * Courses Model
     * @var course
     */
    protected $course;
    /**
     * Programme Model
     * @var programme
     */
    protected $programme;

    /**
     * Inject the models.
     * @param Course $course
     * @param Programme $programme
     */
    public function __construct(Course $course, Programme $programme)
    {
        parent::__construct();
        $this->course = $course;
        $this->programme = $programme;
    }

    /**
     * Display a listing of the courses.
     *
     * @return Response
     */
    public function getIndex($programme=null)
    {
        // Title
        $title = Lang::get('admin/courses/title.course_management');
        // Grab all the courses
        $courses = $this->course;
    	//$programme = $this->programme;

        // Show the page
        return View::make('admin/courses/index', compact('courses', 'title', 'programme'));
    }

    /**
     * Show the form for adding a new course.
     *
     * @return Response
     */

    public function getAdd($programme=null)
    {
        //title
        $title = Lang::get('admin/courses/title.add_a_new_course');

        // Mode
        $mode = 'add';
        $course = new Course;
        //$course_programme = DB::table('course_programme')->where('course_id','=', $course->id)->get();
        $programme_lists = Programme::lists('programme_code', 'id');
        if($programme!=null) 
            $pid=$programme->id;
        else 
            $pid=null;

        $staffs = Staff::leftJoin('departments', 'staffs.department_id', '=', 'departments.id')
                            ->select('staffs.id','staffs.name', 'departments.department_name')->get();
        // Show the page
        return View::make('admin/courses/add_edit', compact('course','title', 'mode', 'programme_lists', 'pid', 'staffs'));
    }
    
    /**
     * Store a newly added course in storage.
     *
     * @return Response
     */
    public function postAdd()
    {
        if(Input::get('programme_id')==null){
            return Redirect::to('admin/courses/add')
                ->with('error', "Please select a programme")->withInput();
        }
        $rules = array(
            'module_code' => 'required|unique:courses,module_code',
            'module_name' => 'required|unique:courses,module_name',
            'credits' => 'required|numeric'
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules,[]);
        // Validate the array of inputs
        $validator->each('programme_id', ['required']);
        $validator->each('semester_taken', ['required']);
        $validator->each('elective', ['required']);
        $validator->each('selected', ['required']);

        //transaction begins from here
        DB::beginTransaction();
        // Validate the inputs
        if ($validator->passes())
        {
            // add a new course
            $user = Auth::user();

            //add the course information
            $this->course->module_code = Input::get('module_code');
            $this->course->module_name = Input::get('module_name');
            $this->course->credits = Input::get('credits');

            $programme_id = Input::get('programme_id');
            $semester = Input::get('semester_taken');
            $elective = Input::get('elective');
            $selected = Input::get('selected');
            $staff_id = Input::get('staff_id');

            try {
                // add new course
                $this->course->save();
                $i=0;
                foreach ($programme_id as $pi) {
                    if($staff_id[$i]==null) 
                        $staff_id[$i]=null;
                    $this->course->programmes()->attach([$programme_id[$i] => array('semester_taken'=>$semester[$i],'elective'=>$elective[$i], 'selected'=>$selected[$i], 'staff_id'=>$staff_id[$i] )]);
                    $i++;
                }
                //finally commit if it reaches here without error
                DB::commit();
                // Redirect to the new course edit page
                return Redirect::to('admin/courses/'.$this->course->id.'/edit')
                    ->with('success', Lang::get('admin/courses/messages.add.success'));
            } catch(Exception $e){
                DB::rollback();
                return Redirect::to('admin/courses/add')->withInput()
                    ->with('error', Lang::get('admin/courses/messages.add.error'));
            }
        }
        else {
            // Form validation failed
            return Redirect::to('admin/courses/add')->withInput()->withErrors($validator);
        }
    }
    
    /**
     * Show the form for editing a course.
     *
     * @return Response
     */
    public function getEdit($course)
    {
        if( $course->id) {
            $title = Lang::get('admin/courses/title.course_update');
            $mode = 'edit';
            $programme_lists = Programme::lists('programme_code', 'id');
            $staffs = Staff::leftJoin('departments', 'staffs.department_id', '=', 'departments.id')
                            ->select('staffs.id','staffs.name', 'departments.department_name')->get();
            
            return View::make('admin/courses/add_edit', compact('course', 'title', 'mode', 'programme_lists', 'staffs'));
        }
        else
        {
            return Redirect::to('admin/courses')->with('error', Lang::get('admin/courses/messages.does_not_exist'));
        }
    }
    /**
     * Update the specified course in storage.
     *
     * @return Response
     */
    
    public function postEdit($course)
    {
        if(Input::get('programme_id')==null){
            return Redirect::to('admin/courses/' . $course->id . '/edit')
                ->with('error', "Please select a programme");
        }
        $rules = array(
            'module_code' => 'required|unique:courses,module_code,'.$course->id,
            'module_name' => 'required|unique:courses,module_name,'.$course->id,
            'credits' => 'required|numeric'
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules, []);
        // Validate inputs array
        $validator->each('programme_id', ['required']);
        $validator->each('semester_taken', ['required']);
        $validator->each('elective', ['required']);
        $validator->each('selected', ['required']);

        DB::beginTransaction();
        
        // Validation one testing
        if ($validator->passes())
        {
            $user = Auth::user();
            //update the course information
            $course->module_code = Input::get('module_code');
            $course->module_name = Input::get('module_name');
            $course->credits = Input::get('credits');

            $pivot_id = Input::get('pivot_id');
            $programme_id = Input::get('programme_id');
            $semester = Input::get('semester_taken');
            $elective = Input::get('elective');
            $selected = Input::get('selected');

            $staff_id = Input::get('staff_id');

            try {
                 // Save if valid
                $course->save();

                // Operation on course_programme table2
                $oldpivot= array();
                foreach($course->programmes as $cp){
                    $oldpivot[]=$cp->pivot->pivot_id;
                }

                $results=array_diff($oldpivot, $pivot_id);
                if($results!=null) {
                    foreach ($results as $value) {
                        CourseProgramme::where('pivot_id', $value)->delete();
                    }
                }
                // if(!empty($programme_id))
                //     $course->programmes()->sync(Input::get('programme_id'));
                // else
                //     $course->programmes()->detach();
                
                $j=0;
                
                foreach ($programme_id as $pi) {
                    if($staff_id[$j]==null) $staff_id[$j] = null;
                     // $course->programmes()->updateExistingPivot()
                        if($pivot_id[$j]!=null) {
                            CourseProgramme::where('pivot_id', $pivot_id[$j])
                                ->update(array('course_id'=>$course->id,'programme_id'=>$programme_id[$j],'semester_taken'=>$semester[$j],'elective'=>$elective[$j], 'selected'=>$selected[$j], 'staff_id'=>$staff_id[$j]));
                        } else {
                            $course->programmes()->attach($programme_id[$j], array('semester_taken'=>$semester[$j],'elective'=>$elective[$j], 'selected'=>$selected[$j], 'staff_id'=>$staff_id[$j]));
                        }
                    $j++;
                }
                
                //finally commit if it reaches here without error
                DB::commit();
                // Redirect to the edit page with success message
                return Redirect::to('admin/courses/' . $course->id . '/edit')
                    ->with('success', Lang::get('admin/courses/messages.edit.success'));

            } catch(Exception $ex){
               // DB::rollback();
                // Redirect to the edit page with failure message
                return Redirect::to('admin/courses/' . $course->id . '/edit')
                    ->with('error', Lang::get('admin/courses/messages.edit.error'));
            }
        } else {
            return Redirect::to('admin/courses/' . $course->id . '/edit')->withErrors($validator);
        }
    }
    /**
     * Remove course page.
     *
     * @param $course
     * @return Response
     */
    public function getDelete($pivot_id)
    {
        // Title
        $title = Lang::get('admin/courses/title.course_delete');
        $coupro = DB::table('course_programme')->where('pivot_id',$pivot_id)->first();
        //$temp = $course_programme[0];
        $course = Course::find($coupro->course_id);
        // Show the page

        return View::make('admin/courses/delete', compact('pivot_id','course','title'));
    }

    /**
     * Remove the specified course from storage.
     *
     * @param $course
     * @return Response
     */
    public function postDelete()
    {
        $pivot_id = Input::get('pivot_id');
        $course_id = Input::get('course_id');
        // To check the count of this course in pivot table
        $courses = DB::table('course_programme')->where('course_id',$course_id)->get();
        $count = count($courses);
        // If count is only 1, delete this course both from course_programme pivot as well as from course table
        if($count==1) {
            Course::find($course_id)->delete();
            //Was the course deleted?
            $course = Course::find($course_id);
            if ( empty($course) ) {
                // if empty, show success message
                return Redirect::to('admin/courses')->with('success', Lang::get('admin/courses/messages.delete.success'));
            } else {
                // There was a problem deleting the course
                return Redirect::to('admin/course')->with('error', Lang::get('admin/courses/messages.delete.error'));
            }
        }
        elseif ($count>1) {
            // If this course is shared by other programmes, delete it only from the pivot table
            DB::table('course_programme')->where('pivot_id', $pivot_id)->delete();
            // Was the course deleted?
            $cp = DB::table('course_programme')->where('pivot_id', $pivot_id)->first();
            if ( empty($cp) ) {
                // Show success message if it is not present
                return Redirect::to('admin/courses')->with('success', Lang::get('admin/courses/messages.delete.success'));
            } else {
                // There was a problem deleting the course
                return Redirect::to('admin/course')->with('error', Lang::get('admin/courses/messages.delete.error'));
            }
        }
    }

    /**
     * Show a list of all the courses formatted for Datatables.
     *
     * @return Datatables JSON
     */
    public function getData($programme=null)
    {
        $courses = Course::leftJoin('course_programme', 'courses.id', '=', 'course_programme.course_id')
            ->leftJoin('programmes', 'course_programme.programme_id' ,'=', 'programmes.id')
            ->leftJoin('staffs', 'course_programme.staff_id', '=', 'staffs.id')
            ->leftJoin('semesters', 'course_programme.semester_taken', '=', 'semesters.number')
            ->select(array('course_programme.pivot_id as pivot_id','courses.id', 'courses.module_code', 'courses.module_name',
                'courses.credits','programmes.programme_code','semesters.roman','course_programme.elective','course_programme.selected','staffs.name', 'course_programme.pivot_id as student'))
                ->orderBy('programme_id')
                ->orderBy('semester_taken')
                ->orderBy('module_name')
                ->where(function($query) use ($programme){
                    if($programme)
                        $query->where('course_programme.programme_id', $programme->id);
                });

        return Datatables::of($courses)
            ->edit_column('elective','@if($elective=="No")
                                        No
                                    @elseif($elective=="Yes")
                                        <span class="label label-info">Yes</span>
                                    @endif')
            ->edit_column('selected','@if($selected=="No")
                                        <span class="label label-warning">No</span>
                                    @elseif($selected=="Yes")
                                        Yes
                                    @endif')
            ->edit_column('student', '<span class="label label-success"> {{{ DB::table(\'course_student\')->where(\'course_programme_id\', \'=\', $pivot_id)->count()  }}}</span>')
            ->add_column('actions', '
            <div class="btn-group">
            <a href="{{{ URL::to(\'admin/courses/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs btn-primary"><i class="fa fa-pencil"></i> {{{ Lang::get(\'button.edit\') }}}</a>
            <a href="{{{ URL::to(\'admin/courses/\' . $pivot_id . \'/delete\' ) }}}" class="iframe btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> {{{ Lang::get(\'button.delete\') }}}</a>
            </div>
            ')
            ->remove_column('id')
            ->remove_column('pivot_id')
            ->make();
    }
}
