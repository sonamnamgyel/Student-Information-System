<?php

class AdminStaffsController extends AdminController {


    /**
     * Staff Model
     * @var Staff
     */
    protected $staff;

    /**
     * Inject the models.
     * @param Staff $staff
     */
    public function __construct(Staff $staff)
    {
        parent::__construct();
        $this->staff = $staff;
    }

    /**
     * Display a listing of the staffs.
     *
     * @return Response
     */
    public function getIndex($department=null)
    {
        // Title
        $title = Lang::get('admin/staffs/title.staff_management');
        // Grab all the staffs
    	$staffs = $this->staff;
        // Show the page
        return View::make('admin/staffs/index', compact('staffs', 'title','department'));
    }

    /**
     * Show the table for viewing a staff details.
     *
     * @return Response
     */
    public function getView($staff)
    {
        if( $staff->id) {
            //$title = Lang::get('admin/staffs/title.staff_details');
            $title = "Staff : <span class='label label-default'>".$staff->title.'. '.$staff->name."</span>";

            return View::make('admin/staffs/view', compact('staff', 'title'));
        }
        else
        {
            return Redirect::to('admin/staffs')->with('error', Lang::get('admin/staffs/messages.does_not_exist'));
        }
    }

    /**
     * Show the form for adding a new staff.
     *
     * @return Response
     */

    public function getAdd($department=null)
    {
        //title
        $title = Lang::get('admin/staffs/title.add_a_new_staff');

        // Mode
        $mode = 'add';
        
        if($department!=null) 
            $dep=$department->id;
        else 
            $dep=null;
        $department_lists = Department::lists('department_name', 'id');
        $courses = Course::leftJoin('course_programme', 'course_programme.course_id', '=', 'courses.id')
                                ->leftJoin('programmes', 'course_programme.programme_id', '=', 'programmes.id')
                                ->select('course_programme.pivot_id', 'programmes.programme_name', 'courses.module_name', 'course_programme.semester_taken')
                                ->where('course_programme.selected', 'Yes')
                                ->where('course_programme.staff_id', null)->get();
        $staff = new Staff;

        // Show the page
        return View::make('admin/staffs/add_edit', compact('staff', 'title', 'mode', 'department_lists', 'courses', 'dep'));
    }
    
    /**
     * Store a newly added staff
     *
     * @return Response
     */
    public function postAdd()
    {
        Validator::extend('alpha_spaces', function($attribute, $value)
        {
            return preg_match('/^[\pL\s]+$/u', $value);
        });
        $rules = array(
            'staff_id' => 'required|unique:staffs,staff_id',
            'title' => 'required',
            'name' => 'required|alpha_spaces|min:4',
            'position' => 'required',
            'gender' => 'required|in:Male,Female',
            'cidno' => 'required|digits:11|unique:staffs,cidno',
            'phone' => 'digits:8',
            'fax' => 'digits:8'
        );

        $customMessages = array(
           'cidno.required' => 'The CID Number field is required.',
           'alpha_spaces' => 'The :attribute may only contain letters.',
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules, $customMessages);

        if ($validator->passes())
        {
            $user = Auth::user();

            //add the staff informations
            $this->staff->staff_id = Input::get('staff_id');
            $this->staff->title = Input::get('title');
            $this->staff->name = Input::get('name');
            $this->staff->position = Input::get('position');
            $this->staff->gender = Input::get('gender');
            $this->staff->cidno = Input::get('cidno');

            $department_id = Input::get('department_id');
            if($department_id!=null){
                $this->staff->department_id = $department_id;
            } else {
                $this->staff->department_id = null;
            }

            $phone = Input::get('phone');
            if($phone!=null){
                $this->staff->phone = $phone;
            } else {
                $this->staff->phone = null;
            }            
            
            $fax = Input::get('fax');
            if($fax!=null){
                $this->staff->fax = $fax;
            } else {
                $this->staff->fax = null;
            }

            $insert = $this->staff->save();
            
            //get the array of all pivot_ids for course_programme table
            $pivot_id = Input::get('pivot_id');

            // was the new staff added?
            if($insert)
            {
                // now update the course_programme/pivot table
                if($pivot_id!=null){
                    foreach( $pivot_id as $pi ) {
                        if($pi!=null){
                            DB::table('course_programme')->where('pivot_id', $pi)->update(array('staff_id' => $this->staff->id));
                        }
                    }
                }

                // Redirect to the new staff edit page
                return Redirect::to('admin/staffs/' . $this->staff->id . '/edit')
                    ->with('success', Lang::get('admin/staffs/messages.add.success'));
            }

            // if error, redirect to the new staff add page
            return Redirect::to('admin/staffs/add')
                ->with('error', Lang::get('admin/staffs/messages.add.error'));
        }
        // Form validation failed
        return Redirect::to('admin/staffs/add')->withInput()->withErrors($validator);
    }
    
    /**
     * Show the form for editing a staff.
     *
     * @return Response
     */
    public function getEdit($staff)
    {
        if( $staff->id) {
            $title = Lang::get('admin/staffs/title.staff_update');
            $mode = 'edit';
            $department_lists = Department::lists('department_name', 'id');
            $courses = Course::leftJoin('course_programme', 'course_programme.course_id', '=', 'courses.id')
                                ->leftJoin('programmes', 'course_programme.programme_id', '=', 'programmes.id')
                                ->select('course_programme.pivot_id', 'programmes.programme_name','course_programme.semester_taken', 'module_name')
                                ->where('course_programme.selected', 'Yes')
                                ->where('course_programme.staff_id', null)
                                ->orWhere('course_programme.staff_id', $staff->id)
                                ->where('course_programme.selected', 'Yes')
                                ->get();

            $sc = DB::table('course_programme')->select('pivot_id')->where('staff_id', $staff->id)->get();
            $staff_courses = array();
            foreach ($sc as $value) {
                $staff_courses[] = $value->pivot_id;
            }
            
            return View::make('admin/staffs/add_edit', compact('staff', 'title', 'mode', 'department_lists', 'courses', 'staff_courses'));
        }
        else
        {
            return Redirect::to('admin/staffs')->with('error', Lang::get('admin/staffs/messages.does_not_exist'));
        }
    }
    
    /**
     * Update the specified staff in storage.
     *
     * @return Response
     */
    
    public function postEdit($staff)
    {
        Validator::extend('alpha_spaces', function($attribute, $value)
        {
            return preg_match('/^[\pL\s]+$/u', $value);
        });
        $rules = array(
            'staff_id' => 'required|unique:staffs,staff_id,'.$staff->id,
            'title' => 'required',
            'name' => 'required|alpha_spaces|min:4',
            'position' => 'required',
            'gender' => 'required|in:Male,Female',
            'cidno' => 'required|digits:11|unique:staffs,cidno,'.$staff->id,
            'phone' => 'digits:8',
            'fax' => 'digits:8'
        );

        $customMessages = array(
           'cidno.required' => 'The CID Number field is required.',
           'alpha_spaces'     => 'The :attribute may only contain letters.',
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules, $customMessages);

        if ($validator->passes())
        {
            $user = Auth::user();

            $staff->staff_id = Input::get('staff_id');
            $staff->title = Input::get('title');
            $staff->name = Input::get('name');
            $staff->position = Input::get('position');
            $staff->gender = Input::get('gender');
            $staff->cidno = Input::get('cidno');
            
            $department_id = Input::get('department_id');
            if($department_id!=null){
                $staff->department_id = $department_id;
            } else {
                $staff->department_id = null;
            }

            $phone = Input::get('phone');
            if($phone!=null){
                $staff->phone = $phone;
            } else {
                $staff->phone = null;
            }            
            
            $fax = Input::get('fax');
            if($fax!=null){
                $staff->fax = $fax;
            } else {
                $staff->fax = null;
            }

            // Save if valid
            $staff->save();

            //get the array of all pivot_id from form
            $pivot_id = Input::get('pivot_id');
            //before updating, set all the course taught by this staff to null
            DB::table('course_programme')->where('staff_id', $staff->id)->update(array('staff_id' => null));
            // now update the course_programme/pivot table
            if($pivot_id!=null){
                foreach( $pivot_id as $pi ) {
                    if($pi!=null){
                        DB::table('course_programme')->where('pivot_id', $pi)->update(array('staff_id' => $staff->id));
                    }
                }
            }

        } else {
            return Redirect::to('admin/staffs/' . $staff->id . '/edit')->withErrors($validator);
        }

        // if no error on the model
        if(empty($staff->errors)) {
            // Redirect to the edit page with success message
            return Redirect::to('admin/staffs/' . $staff->id . '/edit')
                ->with('success', Lang::get('admin/staffs/messages.edit.success'));
        }
        // if error found on model
        else {
            // Redirect to the edit page with failure message
            return Redirect::to('admin/staffs/' . $staff->id . '/edit')
                ->withInput()
                ->withErrors($staff->errors);
        }
    }

    /**
     * Remove staff
     *
     * @param $staff
     * @return Response
     */
    public function getDelete($staff)
    {
        // Title
        $title = Lang::get('admin/staffs/title.staff_delete');

        // Show the page
        return View::make('admin/staffs/delete', compact('staff', 'title'));
    }

    /**
     * Remove the specified staff from storage.
     *
     * @param $staff
     * @return Response
     */
    public function postDelete($staff)
    {
        $id = $staff->id;
        $staff->delete();

        // Was the staff deleted?
        $staff = Staff::find($id);
        if ( empty($staff) )
        {
            // TODO needs to delete all of that staff's content
            return Redirect::to('admin/staffs')->with('success', Lang::get('admin/staffs/messages.delete.success'));
        }
        else
        {
            // There was a problem deleting the staff
            return Redirect::to('admin/staffs')->with('error', Lang::get('admin/staffs/messages.delete.error'));
        }
    }


    /**
     * Show a list of all the Staffs formatted for Datatables.
     *
     * @return Datatables JSON
     */
    public function getData($department=null)
    {
        $staffs = Staff::leftJoin('departments', 'staffs.department_id', '=', 'departments.id')
            ->select(array('staffs.id','staffs.staff_id', 'staffs.position', DB::raw('CONCAT(staffs.title,\'. \',staffs.name)'), 'staffs.gender','departments.department_name', 'staffs.id as course_no', 'staffs.id as actions'))
            ->orderBy('department_id')
            ->orderBy('name')
            ->where(function($query) use ($department) {
                if($department)
                    $query->where('departments.id', $department->id);
            });
            
        return Datatables::of($staffs)
            ->add_column('slno', '', 1)
            ->edit_column('course_no', '<span class="label label-success"> {{{ count(DB::table(\'course_programme\')->select(\'course_id\')->where(\'staff_id\', $id)->where(\'selected\',\'Yes\')->distinct()->get()) }}}</span>')
            ->edit_column('actions', '
            <div class="btn-group">
            <a href="{{{ URL::to(\'admin/staffs/\' . $id . \'/view\' ) }}}" class="iframe btn btn-xs btn-primary"><i class="fa fa-eye"></i> {{{ Lang::get(\'button.details\') }}}</a>
            <a href="{{{ URL::to(\'admin/staffs/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs btn-primary"><i class="fa fa-pencil"></i> {{{ Lang::get(\'button.edit\') }}}</a>
            <a href="{{{ URL::to(\'admin/staffs/\' . $id . \'/delete\' ) }}}" class="iframe btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> {{{ Lang::get(\'button.delete\') }}}</a>
            </div>
            ')
            ->remove_column('id')
            ->make();
    }
}