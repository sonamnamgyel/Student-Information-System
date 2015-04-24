<?php

class AdminDepartmentsController extends AdminController {


    /**
     * Department Model
     * @var department
     */
    protected $department;

    /**
     * Inject the models.
     * @param Department $department
     */
    public function __construct(Department $department)
    {
        parent::__construct();
        $this->department = $department;
    }

    /**
     * Display a listing of the department.
     *
     * @return Response
     */
    public function getIndex()
    {
        // Title
        $title = Lang::get('admin/departments/title.department_management');
        // Grab all the department
    	$departments = $this->department;
        // Show the page
        return View::make('admin/departments/index', compact('departments', 'title'));
    }

    /**
     * Show the form for adding a new department.
     *
     * @return Response
     */

    public function getAdd()
    {
        //title
        $title = Lang::get('admin/departments/title.add_a_new_department');

        // Mode
        $mode = 'add';
        $department = new Department;

        // Show the page
        return View::make('admin/departments/add_edit', compact('department','title', 'mode'));
    }
    
    /**
     * Store a newly added department in storage.
     *
     * @return Response
     */
    public function postAdd()
    {
        $rules = array(
            'department_code' => 'required|unique:departments,department_code',
            'department_name' => 'required|unique:departments,department_name',
            'hod_id' => 'unique:departments,hod_id'
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes())
        {
            // add a new department
            $user = Auth::user();

            //add the department information
            $this->department->department_code = Input::get('department_code');
            $this->department->department_name = Input::get('department_name');
            
            $hod = Input::get('hod_id');
            if($hod!=null)
                $this->department->hod_id = $hod;
            else
                $this->department->hod_id = null;
            // was the new department added?
            if($this->department->save())
            {
                // Redirect to the new department edit page if successful
                return Redirect::to('admin/departments/' . $this->department->id . '/edit')
                    ->with('success', Lang::get('admin/departments/messages.add.success'));
            }

            // Redirect to the new department add page if failed
            return Redirect::to('admin/departments/add')
                ->with('error', Lang::get('admin/departments/messages.add.error'));
        }
        // Form validation failed
        return Redirect::to('admin/departments/add')->withInput()->withErrors($validator);
    }
    
    /**
     * Show the form for editing a department.
     *
     * @return Response
     */
    public function getEdit($department)
    {
        if( $department->id) {
            $title = Lang::get('admin/departments/title.department_update');
            $mode = 'edit';

            return View::make('admin/departments/add_edit', compact('department', 'title', 'mode'));
        }
        else
        {
            return Redirect::to('admin/departments')->with('error', Lang::get('admin/departments/messages.does_not_exist'));
        }
    }
    /**
     * Update the specified department in storage.
     *
     * @return Response
     */
    
    public function postEdit($department)
    {
        $rules = array(
            'department_code' => 'required|unique:departments,department_code,'.$department->id,
            'department_name' => 'required|unique:departments,department_name,'.$department->id,
            'hod_id' => 'unique:departments,hod_id,'.$department->id,
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes())
        {
            $user = Auth::user();
            //update the department information
            $department->department_code = Input::get('department_code');
            $department->department_name = Input::get('department_name');
            $hod = Input::get('hod_id');
            if($hod!=null)
                $department->hod_id = $hod;
            else
                $department->hod_id = null;

            // Save if valid
            $department->save();

        } else {
            return Redirect::to('admin/departments/' . $department->id . '/edit')->withErrors($validator);
        }

        // if no error on the model
        if(empty($department->errors)) {
            // Redirect to the edit page with success message
            return Redirect::to('admin/departments/' . $department->id . '/edit')
                ->with('success', Lang::get('admin/departments/messages.edit.success'));
        }
        // if error found on model
        else {
            // Redirect to the edit page with failure message
            return Redirect::to('admin/departments/' . $department->id . '/edit')
                ->withInput()
                ->withErrors($department->errors);
        }
    }
    /**
     * Remove department page.
     *
     * @param $department
     * @return Response
     */
    public function getDelete($department)
    {
        // Title
        $title = Lang::get('admin/departments/title.department_delete');

        // Show the page
        return View::make('admin/departments/delete', compact('department', 'title'));
    }

    /**
     * Remove the specified deparment from storage.
     *
     * @param $department
     * @return Response
     */
    public function postDelete($department)
    {
        $id = $department->id;
        
        try {
            $department->delete();
        }
        catch(Exception $ex) {
            return Redirect::to('admin/departments/' . $id . '/delete')
                ->with('error', 'Cannot delete! This '. $department->department_name .' department is linked');
        }
        // Was the department deleted?
        $department = Department::find($id);
        if ( empty($department) )
        {
            // TODO needs to delete all of that department's content
            return Redirect::to('admin/departments')->with('success', Lang::get('admin/departments/messages.delete.success'));
        }
        else
        {
            // There was a problem deleting the department
            return Redirect::to('admin/departments')->with('error', Lang::get('admin/departments/messages.delete.error'));
        }
    }

    /**
     * Show a list of all the departments formatted for Datatables.
     *
     * @return Datatables JSON
     */
    public function getData()
    {
        /*
        * $departments = Department::leftJoin('staffs', 'departments.hod_id','=','staffs.id')
        *    ->select(array('departments.id', 'departments.department_code', 'departments.department_name', 'departments.hod_id','staffs.name as staffname' ));
        */
        
        $departments = Department::select(array('departments.id', 'departments.department_code', 'departments.department_name', 
            'departments.hod_id', 'departments.id as programme', 'departments.id as staff', 'departments.id as student', 'departments.id as actions' ))
            ->orderBy('department_name');

        return Datatables::of($departments)
            ->add_column('slno', '', 1)
            ->edit_column('programme','@foreach(Department::find($id)->programmes as $a)
                    <span class=\'label label-primary\'>{{ $a->programme_code}}</span>
                    @endforeach')
            ->edit_column('staff', '<span class="label label-success"> {{{ Staff::where(\'department_id\', $id)->count()  }}}</span>')
            ->edit_column('student', '<span class="label label-success"> {{{ Department::find($id)->students()->where(\'students.registered\',\'Yes\')->count() }}}</span>')
            ->add_column('actions', '
            <div class="btn-group">
            <a href="{{{ URL::to(\'admin/departments/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs btn-primary"><i class="fa fa-pencil"></i> {{{ Lang::get(\'button.edit\') }}}</a>
            <a href="{{{ URL::to(\'admin/departments/\' . $id . \'/delete\' ) }}}" class="iframe btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> {{{ Lang::get(\'button.delete\') }}}</a>
            </div>
            ')
            ->remove_column('id')
            ->make();
    }
}
