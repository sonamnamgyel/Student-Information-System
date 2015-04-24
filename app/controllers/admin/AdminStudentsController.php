<?php

class AdminStudentsController extends AdminController {


    /**
     * Student Model
     * @var Student
     */
    protected $student;
    /**
     * Programme Model
     * @var Programme
     */
    protected $programme;

    /**
     * Inject the models.
     * @param Student $student
     * @param Programme $programme
     */
    public function __construct(Student $student, Programme $programme)
    {
        parent::__construct();
        $this->student = $student;
        $this->programme = $programme;
    }

    /**
     * Display a listing of the students.
     *
     * @return Response
     */
    public function getIndex()
    {
        // Title
        $title = Lang::get('admin/students/title.student_management');
        // Show the page with list of all students
        return View::make('admin/students/index', compact('title'));
    }

    /**
     * Show the form for adding a new student.
     *
     * @return Response
     */

    public function getAdd()
    {
        //title
        $title = Lang::get('admin/students/title.add_a_new_student');

        // Mode
        $mode = 'add';
        $programme_lists = Programme::lists('programme_name', 'id');
        $school_lists = DB::table('schools')->lists('name', 'id');
        $dzongkhag_lists = DB::table('dzongkhags')->lists('name', 'id');
        $courses = Course::leftJoin('course_programme', 'course_programme.course_id', '=', 'courses.id')
                                ->leftJoin('programmes', 'course_programme.programme_id', '=', 'programmes.id')
                                ->select('course_programme.pivot_id', 'programmes.programme_name', 'courses.module_name', 'course_programme.semester_taken')
                                ->where('course_programme.selected', 'Yes')
                                ->orderBy('course_programme.semester_taken')
                                ->get();
        $student = new Student;

        // Show the page
        return View::make('admin/students/add_edit', compact('student','title', 'mode', 'programme_lists','dzongkhag_lists','school_lists', 'courses'));
    }
    
    /**
     * Store a newly added student in storage.
     *
     * @return Response
     */
    public function postAdd()
    {
        $rules = array(
            'stdno' => 'required|min:7|unique:students,stdno',
            'fname' => 'required|alpha|min:4',
            'mname' => 'alpha',
            'lname' => 'required|alpha|min:4',
            'cidno' => 'required|digits:11|unique:students,cidno',
            'gender' => 'required|in:Male,Female',
            'dob' => 'required',
            'stdtype' => 'required',
            'current_semester' => 'required',
            'programme_id' => 'required',
            'bloodgroup' => 'required',
            'address' => 'required|min:5',
            'dzongkhag_id' => 'required',
            'phone' => 'required|digits:8',
            'school_id' => 'required',
            'roomno' => 'required',
            'registered' =>'required|in:Yes,No',
            'enrolled' => 'required'
        );
        
        $customMessages = array(
           'stdno.unique' => 'This stdno is already is registered.',
           'programme_id.required' => 'The programme field is required.',
           'dzongkhag_id.required' => 'The dzongkhag field is required.',
           'school_id.required' => 'The previous school field is required.',
           'dob.required' => 'The date of birth field is required.',
           'bloodgroup.required' => 'The blood group field is required'
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules, $customMessages);

        if ($validator->passes())
        {
            // add a new student
            $user = Auth::user();

            //add the student information
            $this->student->stdno = Input::get('stdno');
            $this->student->fname = Input::get('fname');
            $mname = Input::get('mname');
            if($mname!=null)
                $this->student->mname = $mname;
            else
                $this->student->mname = null;
            $this->student->lname = Input::get('lname');
            $this->student->cidno = Input::get('cidno');
            $this->student->gender = Input::get('gender');
            $this->student->dob = Input::get('dob');
            $this->student->stdtype = Input::get('stdtype');
            $this->student->current_semester = Input::get('current_semester');
            $this->student->programme_id = Input::get('programme_id');
            $this->student->bloodgroup = Input::get('bloodgroup');
            $this->student->address = Input::get('address');
            $this->student->dzongkhag_id = Input::get('dzongkhag_id');
            $this->student->phone = Input::get('phone');
            $this->student->school_id = Input::get('school_id');
            $this->student->roomno = Input::get('roomno');
            $this->student->enrolled = Input::get('enrolled');

            
            $back_course = Input::get('back_course');
            $superback_course = Input::get('superback_course');

            // Transaction starts from here
            DB::beginTransaction();
            try {

                // Save if valid
                $this->student->save();

                // Insert regular courses
                if($this->student->stdtype != "In-Service"){
                    $courses = CourseProgramme::select('pivot_id')
                                        ->where('programme_id', $this->student->programme_id)
                                        ->where('semester_taken', $this->student->current_semester)
                                        ->where('selected', 'Yes')->get();
                    foreach($courses as $c){
                        $this->student->courseprogrammes()->attach($c->pivot_id, array('type'=>'Regular'));
                    }
                }

                // Insert the backcourses
                if($back_course!=null){
                    foreach($back_course as $bc){
                        $validbackcourse = $this->student->validCourse($bc, $this->student->programme_id);
                        if(count($validbackcourse)==1){
                            $this->student->courseprogrammes()->attach($bc, array('type'=>'Back'));
                        }
                    }
                }

                // Insert the superback courses
                if($superback_course!=null){
                    foreach($superback_course as $sbc){
                        $validsuperback = $this->student->validCourse($sbc, $this->student->programme_id);
                        if(count($validsuperback)==1){
                            $this->student->courseprogrammes()->attach($sbc, array('type'=>'Superback'));
                        }
                    }
                }

                // Finally commit if it reaches here without error
                DB::commit();
                // Redirect to the edit page with success message
                return Redirect::to('admin/students/' . $this->student->id . '/edit')
                    ->with('success', Lang::get('admin/students/messages.add.success'));
            } catch(Exception $e){
                DB::rollback();
                // Redirect to the edit page with failure message
                return Redirect::to('admin/students/add')
                    ->withInput()->with('error', Lang::get('admin/students/messages.add.error'));
            }

        }
        // Form validation failed
        return Redirect::to('admin/students/add')->withInput()->withErrors($validator);
    }
    
    /**
     * Show the table for viewing a student details.
     *
     * @return Response
     */
    public function getView($student)
    {
        if( $student->id) {
            //$title = Lang::get('admin/students/title.student_view');
            $title = "Student : <span class='label label-default'>".$student->stdno."</span>";
            $mode = 'view';
            $prog = $student->programme;
            if($prog) {
                $programme_name = $prog->programme_name;
                // $department_name = $student->programme->department->department_name;
                if($prog->department) $department_name = $prog->department->department_name;
                else $department_name = "--";
            } else {
                $programme_name = "--";
                $department_name = "--";
            }

            $semesters = DB::table('course_student')
                            ->leftJoin('course_programme','course_student.course_programme_id', '=', 'course_programme.pivot_id')
                            ->select('course_programme.semester_taken')
                            ->where('course_student.student_id', '=', $student->id)
                            ->groupBy('semester_taken')
                            ->orderBy('semester_taken', 'desc')
                            ->get();

            return View::make('admin/students/view', compact('student', 'title', 'mode', 'programme_name', 'department_name', 'semesters'));
        }
        else
        {
            return Redirect::to('admin/students')->with('error', Lang::get('admin/students/messages.does_not_exist'));
        }
    }
    /**
     * Show the form for editing a new student.
     *
     * @return Response
     */
    public function getEdit($student)
    {
        if( $student->id) {
            $title = Lang::get('admin/students/title.student_update');
            $mode = 'edit';
            $programme_lists = Programme::lists('programme_name', 'id');
            $school_lists = DB::table('schools')->lists('name', 'id');
            $dzongkhag_lists = DB::table('dzongkhags')->lists('name', 'id');

            //$config = Configuration::select('semester')->first();
            //$semester_lists = $config->semesterList($config->semester);
            $courses = Course::leftJoin('course_programme', 'course_programme.course_id', '=', 'courses.id')
                                ->leftJoin('programmes', 'course_programme.programme_id', '=', 'programmes.id')
                                ->select('course_programme.pivot_id', 'programmes.programme_name', 'courses.module_name', 'course_programme.semester_taken')
                                ->where('course_programme.selected', 'Yes')
                                //->where('course_programme.programme_id', $student->programme_id)
                                //->where('course_programme.semester_taken', '<=', $student->current_semester)
                                ->orderBy('course_programme.semester_taken')
                                ->get();
            // Listing of subscribed back paper courses
            $backcourse = DB::table('course_student')->select('course_programme_id')
                            ->where('student_id', $student->id)->where('type', 'Back')->get();
            $back_courses = array();
            foreach ($backcourse as $value) {
                $back_courses[] = $value->course_programme_id;
            }
            // Listing of subscribed superback courses
            $superbackcourse = DB::table('course_student')->select('course_programme_id')
                            ->where('student_id', $student->id)->where('type', 'Superback')->get();
            $superback_courses = array();
            foreach ($superbackcourse as $value) {
                $superback_courses[] = $value->course_programme_id;
            }
            
            return View::make('admin/students/add_edit', compact('student', 'title', 'mode', 'programme_lists','school_lists','dzongkhag_lists' ,'courses', 'back_courses', 'superback_courses'));
        }
        else
        {
            return Redirect::to('admin/students')->with('error', Lang::get('admin/students/messages.does_not_exist'));
        }
    }
    
    /**
     * Update the specified student in storage.
     *
     * @return Response
     */
    
    public function postEdit($student)
    {
        $rules = array(
            'stdno' => 'required|min:7|unique:students,stdno,'.$student->id,
            'fname' => 'required|alpha|min:4',
            'mname' => 'alpha',
            'lname' => 'required|alpha|min:4',
            'cidno' => 'required|digits:11|unique:students,cidno,'.$student->id,
            'gender' => 'required|in:Male,Female',
            'dob' => 'required',
            'stdtype' => 'required',
            'current_semester' => 'required',
            'programme_id' => 'required',
            //'email' => 'required|email|unique:students,email,'.$student->id,
            'bloodgroup' => 'required',
            'address' => 'required|min:5',
            'dzongkhag_id' => 'required',
            'phone' => 'required|digits:8',
            'school_id' => 'required',
            'roomno' => 'required',
            'registered' =>'required|in:Yes,No',
            'enrolled' => 'required'
        );
        $customMessages = array(
           'dob.required' => 'The date of birth field is required.',
           'programme_id.required' => 'The Programme field is required.',
           'school_id.required' => 'The previous school field is required.',
           'dzongkhag_id.required' => 'The dzongkhag field is required.',
           'bloodgroup.required' => 'The Blood group field is required.',
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules, $customMessages);

        if ($validator->passes())
        {
            $user = Auth::user();

            $student->stdno = Input::get('stdno');
            $student->fname = Input::get('fname');
            $mname = Input::get('mname');
            if($mname!=null) {
                $student->mname = $mname;
            } else {
                $student->mname = null;
            }
            $student->lname = Input::get('lname');
            $student->cidno = Input::get('cidno');
            $student->gender = Input::get('gender');
            $student->dob = Input::get('dob');
            $student->stdtype = Input::get('stdtype');
            $student->current_semester = Input::get('current_semester');
            $student->programme_id = Input::get('programme_id');
            $student->bloodgroup = Input::get('bloodgroup');
            $student->address = Input::get('address');
            $student->dzongkhag_id = Input::get('dzongkhag_id');
            $student->phone = Input::get('phone');
            $student->school_id = Input::get('school_id');
            $student->roomno = Input::get('roomno');
            $student->enrolled = Input::get('enrolled');
            $student->registered = Input::get('registered');

            $back_course = Input::get('back_course');
            $superback_course = Input::get('superback_course');

            $remarks = Input::get('remark');

            // Transaction starts from here
            DB::beginTransaction();
            try {

                // Save if valid
                $student->save();
                // Remove all the courses for this students before updating
                $student->courseprogrammes()->detach();

                // Insert regular courses
                if($student->stdtype != "In-Service"){
                    $courses = CourseProgramme::select('pivot_id')
                                        ->where('programme_id', $student->programme_id)
                                        ->where('semester_taken', $student->current_semester)
                                        ->where('selected', 'Yes')->get();
                    foreach($courses as $c){
                        $student->courseprogrammes()->attach($c->pivot_id, array('type'=>'Regular'));
                    }
                }

                // Insert the backcourses
                if($back_course!=null){
                    foreach($back_course as $bc){
                        $validbackcourse = $student->validCourse($bc, $student->programme_id);
                        if(count($validbackcourse)==1){
                            $student->courseprogrammes()->attach($bc, array('type'=>'Back'));
                        }
                    }
                }

                // Insert the superback courses
                if($superback_course!=null){
                    foreach($superback_course as $sbc){
                        $validsuperback = $student->validCourse($sbc, $student->programme_id);
                        if(count($validsuperback)==1){
                            $student->courseprogrammes()->attach($sbc, array('type'=>'Superback'));
                        }
                    }
                }

                // Deleting remarks
                if($remarks == null) {
                    $student->remarks()->delete();
                } else {
                    $oldremarks = array();
                    foreach($student->remarks as $value){
                        $oldremarks[] = $value->id;
                    }

                    $results=array_diff($oldremarks, $remarks);
                    
                    if($results!=null) {
                        foreach ($results as $value) {
                            Remark::find($value)->delete();
                        }
                    }
                }
                
                
                // Finally commit if it reaches here without error
                DB::commit();
                // Redirect to the edit page with success message
                return Redirect::to('admin/students/' . $student->id . '/edit')
                    ->with('success', Lang::get('admin/students/messages.edit.success'));
            } catch(Exception $e){
                DB::rollback();
                // Redirect to the edit page with failure message
                return Redirect::to('admin/students/' . $student->id . '/edit')
                    ->with('error', Lang::get('admin/students/messages.edit.error'));
            }

        } else {
            return Redirect::to('admin/students/' . $student->id . '/edit')->withErrors($validator);
        }       
    }

    /**
     * Remove student page.
     *
     * @param $student
     * @return Response
     */
    public function getDelete($student)
    {
        // Title
        $title = Lang::get('admin/students/title.student_delete');

        // Show the page
        return View::make('admin/students/delete', compact('student', 'title'));
    }

    /**
     * Remove the specified student from storage.
     *
     * @param $student
     * @return Response
     */
    public function postDelete($student)
    {
        $id = $student->id;
        $student->delete();

        // Was the student deleted?
        $student = Student::find($id);
        if ( empty($student) )
        {
            // TODO needs to delete all of that student's content
            return Redirect::to('admin/students')->with('success', Lang::get('admin/students/messages.delete.success'));
        }
        else
        {
            // There was a problem deleting the student
            return Redirect::to('admin/students')->with('error', Lang::get('admin/students/messages.delete.error'));
        }
    }


    /**
     * Show a list of all the students formatted for Datatables.
     *
     * @return Datatables JSON
     */
    public function getData()
    {
        $students = Student::leftJoin('programmes','students.programme_id', '=', 'programmes.id')
            ->leftJoin('semesters', 'students.current_semester', '=', 'semesters.number')
            ->select(array('students.id','students.stdno',DB::raw('CONCAT_WS(\' \',students.fname,students.mname, students.lname) as name'),
            'students.gender', 'students.stdtype', 'semesters.roman', 'programmes.programme_code', 'students.registered', 'students.id as actions'))
            ->orderBy('students.programme_id')
            ->orderBy('students.current_semester','desc')
            ->orderBy('students.fname');

        return Datatables::of($students)
            ->edit_column('registered','@if($registered=="No")
                                        <span class="label label-danger">No</span>
                                    @elseif($registered=="Yes")
                                        Yes
                                    @endif')
            ->edit_column('actions', '
            <div class="btn-group">
            <a href="{{{ URL::to(\'admin/students/\' . $id . \'/view\' ) }}}" class="iframe btn btn-xs btn-primary"><i class="fa fa-eye"></i> {{{ Lang::get(\'button.details\') }}}</a>
            <a href="{{{ URL::to(\'admin/students/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs btn-primary"><i class="fa fa-pencil"></i> {{{ Lang::get(\'button.edit\') }}}</a>
            <a href="{{{ URL::to(\'admin/students/\' . $id . \'/delete\' ) }}}" class="iframe btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> {{{ Lang::get(\'button.delete\') }}}</a>
            </div>
            ')
            ->make();
    }

    /**
     * Show a form for advanced student search.
     *
     * @return Response
     */

    public function getSearch()
    {
        // Title
        $title = Lang::get('admin/students/title.student_management');

        $student = $this->student;
        $departments = Department::lists('department_name', 'id');
        $programmes = Programme::lists('programme_code', 'id');
        $schools = DB::table('schools')->lists('name', 'id');
        $dzongkhags = DB::table('dzongkhags')->lists('name', 'id');
        $data = null;
        // Show the page with list of all students
        return View::make('admin/students/search', compact('title','student', 'departments' ,'programmes','schools','dzongkhags' ,'data'));
    }

    /**
     * Displays an advanced student search results.
     *
     * @return Response
     */
    public function postSearch()
    {
        // Title
        $title = Lang::get('admin/students/title.student_management');
        $student = $this->student;
        $programmes = Programme::lists('programme_code', 'id');
        $departments = Department::lists('department_name', 'id');
        $schools = DB::table('schools')->lists('name', 'id');
        $dzongkhags = DB::table('dzongkhags')->lists('name', 'id');
        
        // The following order is maintained in the route
        // This order should not break!!!
        $data['department'] = Input::get('department');
        $data['programme'] = Input::get('programme');
        $data['stdtype'] = Input::get('stdtype');
        $data['semester'] = Input::get('semester');
        $data['gender'] = Input::get('gender');
        $data['registered'] = Input::get('registered');
        $data['school'] = Input::get('school');
        $data['dzongkhag'] = Input::get('dzongkhag');
        $data['fee'] = Input::get('fee');
        // Show the page with list of all students
        return View::make('admin/students/search', compact('title','student', 'departments', 'programmes','schools','dzongkhags', 'data'));
    }

    /**
     * Show a list of student search results categorized by different fields and is formatted for Datatables.
     *
     * @return Datatables JSON
     */
    public function getDetails($dep=null, $prog=null, $type=null, $sem=null, $gender=null, $reg=null, $school=null, $dzongkhag=null, $fee_paid=null)
    {
        $searchResults = Student::leftJoin('programmes','students.programme_id','=','programmes.id')
            ->leftJoin('semesters', 'students.current_semester', '=', 'semesters.number')
            ->leftJoin('departments', 'programmes.department_id','=', 'departments.id')
            ->leftJoin('fees', 'students.id', '=', 'fees.student_id')
            ->select(array('students.id','students.stdno',DB::raw('CONCAT_WS(\' \',students.fname,students.mname, students.lname) as name'),
                'students.gender', 'students.stdtype', 'semesters.roman', 'programmes.programme_code', 'students.registered', 'students.id as actions'))
            ->orderBy('students.programme_id')
            ->orderBy('students.current_semester','desc')
            ->orderBy('students.fname')
            ->where(function($query) use ($dep, $prog, $type, $sem, $gender, $reg, $school, $dzongkhag, $fee_paid) {
                if($dep)
                    $query->where('departments.id', $dep);

                if($prog)
                    $query->where('students.programme_id', $prog);

                if($type)
                    $query->where('students.stdtype', $type);

                if($sem)
                    $query->where('students.current_semester', $sem);

                if($gender)
                    $query->where('students.gender', $gender);

                if($reg=='No'){
                    $query->where(function($query2) use ($reg) {
                        $query2->where('registered', 'No')
                            ->orWhere('registered', '')
                            ->orWhereNull('registered');
                   });
                }

                if($reg=='Yes')
                    $query->where('students.registered', 'Yes');
                
                if($school)
                    $query->where('students.school_id', $school);

                if($dzongkhag)
                    $query->where('students.dzongkhag_id', $dzongkhag);

                if($fee_paid=='No'){
                    $query->where(function($query2) use ($fee_paid) {
                        $query2->where('fees.paid', 'No')
                            ->orWhere('fees.paid', '')
                            ->orWhereNull('fees.paid');
                   });
                }

                if($fee_paid=='Yes')
                    $query->where('fees.paid', 'Yes');

            });

        return Datatables::of($searchResults)
            ->edit_column('registered','@if($registered=="No")
                                        <span class="label label-danger">No</span>
                                    @elseif($registered=="Yes")
                                        Yes
                                    @endif')
            ->edit_column('actions', '
            <div class="btn-group">
            <a href="{{{ URL::to(\'admin/students/\' . $id . \'/view\' ) }}}" class="iframe btn btn-xs btn-primary"><i class="fa fa-eye"></i> {{{ Lang::get(\'button.details\') }}}</a>
            <a href="{{{ URL::to(\'admin/students/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs btn-primary"><i class="fa fa-pencil"></i> {{{ Lang::get(\'button.edit\') }}}</a>
            <a href="{{{ URL::to(\'admin/students/\' . $id . \'/delete\' ) }}}" class="iframe btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> {{{ Lang::get(\'button.delete\') }}}</a>
            </div>
            ')
            ->make();
    }
}