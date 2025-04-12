<div class="container">
    <h1>Admin Requests</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($requests) && count($requests) > 0)
                @foreach($requests as $request)
                <tr>
                    <td>{{ $request->id ?? '' }}</td>
                    <td>{{ $request->title ?? '' }}</td>
                    <td>{{ $request->status ?? '' }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center">No requests found</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
