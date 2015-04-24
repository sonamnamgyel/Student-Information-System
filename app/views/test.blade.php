<script  src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>

{{ Form::open() }}
<select id="cat" name="category" data-url="{{ url('api/dropdown')}}">
  <option>Select Car Make</option>
  <option value="1">Toyota</option>
  <option value="2">Honda</option>
  <option value="3">Mercedes</option>
</select>
<br>
<select id="subcat" name="programme_name">
  <option>Please choose car make first</option>
</select>
{{ Form::close();}}
<script>
	jQuery(document).ready(function($){
  	$('#cat').change(function(){
       $.get($(this).data('url'),
       { option: $(this).val() },
         function(data) {
           var subcat = $('#subcat');
           subcat.empty();
           $.each(data, function(index, element) {
          subcat.append("<option value='"+ element.id +"'>" + element.course_id + "</option>");
        });
      });
    });
	});
</script>
