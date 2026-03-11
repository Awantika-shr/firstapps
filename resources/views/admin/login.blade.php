<x-message/>
<h1>Admin</h1>
<h1>{{ $user }}</h1>
<h1>{{ $users[1]}}</h1>
@if($user=='Annu')
<h2>this is annu</h2>
@elseif ($user=='Aaru')
<h2>this is aaru</h2>
@else
<h2>other user</h2>
@endif 

<div>
    @foreach($users as $user)
    <h5>{{ $user }}</h5>
    @endforeach 
</div>

<div>
    @for($i=0;$i<10;$i++)
    <h3>{{ $i }}</h3>
    @endfor 
</div>
