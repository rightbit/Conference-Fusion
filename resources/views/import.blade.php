
<form action="{{ route('import.users') }}" method="POST" enctype="multipart/form-data">
@csrf
<label for="csv_file">Upload CSV file:</label>
<input type="file" name="csv_file" id="csv_file" accept=".csv" required>
<button type="submit">Import Users</button>
</form>


@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
