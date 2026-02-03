@if($records->count()>0)
    @foreach($records as $key => $row)
    @php
        $count = $records->count();
        $last = $records->lastItem();
        $page = $records->currentPage();
        $sr = $key+1;
        if($page > 1){
            $sr = ($last-$count)+$key+1;
        }
    @endphp
    <tr>
        <td>{{ $sr }}</td>
        <td>{{ isset($row->department) ? $row->department->name : '-' }}</td>
        <td>{{ $row->bed_no }}</td>
        <td>{{ $row->gender ? ($row->gender == 'M' ? 'Male' : 'Female') : '-' }}</td>
        <td><span class="badge bg-secondary">{{ ucfirst($row->bed_status) }}</span></td>
        <td>
            @if($row->status == 1)
            <a href="javascript:void(0);" onclick="changeStatus('bed_distributions','{{ $row->id }}','{{ $row->status }}');" class="badge bg-success">Active</a>
            @else
            <a href="javascript:void(0);" onclick="changeStatus('bed_distributions','{{ $row->id }}','{{ $row->status }}');" class="badge bg-danger">In-Active</a>
            @endif
        </td>
        <td>
            <span class="text-muted fw-bold d-block fs-7">{{ isset($row->created_date) ? date('d M, Y h:i A', strtotime($row->created_date)) : '-' }}</span>
        </td>
        <td>
            <a href="{{ url('/admin/edit-bed-distribution', base64_encode($row->id)) }}" class="btn btn-sm btn-primary" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <a href="javascript:void(0);" onclick="deleteData('bed_distributions','{{ $row->id }}');" class="btn btn-sm btn-danger" title="Delete">
                <i class="bi bi-trash"></i>
            </a>
        </td>
    </tr>
    @endforeach
@else
    <tr>
        <td align="center" colspan="10">Record not found</td>
    </tr>
@endif
<tr>
    <td align="center" colspan="10">
        <div id="pagination">{{ $records->appends(request()->except('page'))->links('vendor.pagination.custom') }}</div>
    </td>
</tr>
