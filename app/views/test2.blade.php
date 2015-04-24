<select>
	@foreach($coursesbyprogramme as $c)
	<optgroup label="{{ $c->programme_name }}">
		@foreach($student->coursesbysemester($c->programme_id) as $sem)
			<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;{{ $sem->roman}}">
				@foreach($student->courses($c->programme_id,$sem->semester_taken) as $c)
				<option>&nbsp;&nbsp;&nbsp;&nbsp; {{ $c->course_name }}</option>
				@endforeach
			</optgroup>
		@endforeach
	</optgroup>
	@endforeach
</select>

<tbody>
@foreach($semesters as $semester)
<tr>
    <td>{{ $semester->semester_taken }}</td>
    <td colspan="2">
        <div><ol>
            @foreach($student->getCoursesBySemester($semester->semester_taken, $student) as $course)
            
                <li><span class="md-col-4">{{ $student->getCourseName($course->course_id)->course_name }}</span>
                <span>{{ $course->type }}</span></li>
            @endforeach 
        </ol></div>
    </td>
</tr>
@endforeach
</tbody>