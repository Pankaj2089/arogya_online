@if($records->count()>0)
    @foreach($records as $key => $row)
    <tr>
    	 @php
            $count = $records->count();
            $last = $records->lastItem();
            $page = $records->currentPage();
            $sr = $key+1;
            if($page > 1){
                $sr = ($last-$count)+$key+1;
            }
        @endphp
        <td>
            <div class="d-flex align-items-center">
                {!! $sr !!}
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                {!! $row->name !!}
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                {!! $row->email !!}
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                {!! $row->mobile !!}
            </div>
        </td>
        <td>
            @if($row->status == 1)
            <a href="javascript:void(0);" onclick="changeStatus('users','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-success ">Active</a>
            @else
            <a href="javascript:void(0);"  onclick="changeStatus('users','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-danger">In-Active</a>
            @endif
        </td>
        <td>
            <span class="text-muted fw-bold text-muted d-block fs-7">{!! !empty($row->last_login) ? date('d M, Y h:i A',strtotime($row->last_login)):'N/A' !!}</span>
        </td>
        <td>
            <span class="text-muted fw-bold text-muted d-block fs-7">{!! date('d M, Y h:i A',strtotime($row->created_at)) !!}</span>
        </td>
        <td>
            <a href="{{ url('/admin/edit-account',base64_encode($row->id)) }}" class="btn btn-sm btn-primary" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <a href="javascript:void(0);" onclick="deleteData('users','{{ $row->id }}');" class="btn btn-sm btn-danger"  title="Delete">
                <i class="bi bi-trash"></i>
            </a>
        </td>
    </tr>

    @endforeach
    @else
    <tr>
        <td align="center" colspan="9">Record not found</td>
    </tr>
    @endif
    <tr>
        <td align="center" colspan="9">
            <div id="pagination">{{ $records->appends(request()->except('page'))->links('vendor.pagination.custom') }}</div>
        </td>
    </tr>


