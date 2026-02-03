@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Bed Distributions</h3>
                <p class="text-subtitle text-muted">Manage beds by department.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{url('/admin/bed-distributions')}}">Bed Distributions</a></li>
                        <li class="breadcrumb-item active" aria-current="page">List</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body">
                <form id="searchForm" name="searchForm" class="float-start">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <div class="position-relative w-md-200px">
                            <select id="department_id" name="department_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="position-relative w-md-120px">
                            <input id="bed_no" name="bed_no" type="text" class="form-control" placeholder="Bed No." maxlength="50">
                        </div>
                        <div class="position-relative w-md-150px">
                            <select id="bed_status" name="bed_status" class="form-select">
                                <option value="">All Status</option>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="reserved">Reserved</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center">
                            <button type="button" id="searchbuttons" onclick="filterData('search');" class="btn btn-sm btn-primary me-2">Search</button>
                            <button type="reset" class="btn btn-sm btn-dark" onclick="resetFilterForm();">Reset</button>
                        </div>
                    </div>
                </form>
                <a href="{{url('/admin/add-bed-distribution')}}" class="btn icon btn-sm btn-outline-success float-end">Add New Bed</a>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="row" id="table-head">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>DEPARTMENT</th>
                                        <th>BED NO.</th>
                                        <th>GENDER</th>
                                        <th>BED STATUS</th>
                                        <th>STATUS</th>
                                        <th>CREATED</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody id="replaceHtml">
                                    <tr>
                                        <td colspan="10" class="text-center"><img src="{{ asset('public/admin/images/svg/oval.svg') }}" class="me-4" style="width: 3rem" alt="loading"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
$(document).ready(function(){
    filterData('simple');
});
function filterData(type){
    if(type == 'search'){$('#searchbuttons').html('Searching..');}
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        data: $('#searchForm').serialize(),
        url: "{{ url('/admin/bed-distributions_paginate') }}",
        success: function(response){
            $('#replaceHtml').html(response);
            $('#searchbuttons').html('Search');
        }
    });
}
</script>

@endsection
