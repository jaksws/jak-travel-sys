<div class="container">
    <h1>Agent Requests</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>{{ $request->id ?? '' }}</td>
                <td>{{ $request->title ?? '' }}</td>
                <td>{{ $request->status ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
