<div>
    <h1>Add New Student</h1>
    <form action='' method='post'>
        @csrf
    <input type="text" placeholder="Enter user name" name="name">
    </br>
    </br>
    <input type="text" placeholder="Enter user email" name="email">
    <br/>
    </br>
    <input type="text" placeholder="Enter user phone no:" name="phone">
    </br>
    </br>
    <button>Add Student</button>
    
    @foreach($students as $student)
    <input {{ $student->name }}/>
     <input{{ $student->email }}/>
      <input{{ $student->phone }}/>
@endforeach
      </form>
</div>
