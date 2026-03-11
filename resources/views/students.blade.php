{{-- 
<h1>Welcome to Student Page</h1>
<a href="/" >welcome page</a></br>
<a href="/get" >hello page</a></br>
<a href="/about/awanti" >about page</a> --}}

<div>
    <h1>Users List</h1>
    
    <table>
        <tr>
            <td>Name</td>
            <td>Email</td>
            <td>Batch</td>
        </tr>
        @foreach ($data as $students)
          <tr>
            <td>{{ $students->name }}</td>
            <td>{{ $students->email }}</td>
            <td>{{ $students->batch }}</td>
          </tr>    
        @endforeach
    </table>        
    <!-- Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Maria Skłodowska-Curie -->
</div>

