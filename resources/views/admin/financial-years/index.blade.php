@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Financial Years Management</h3>
                <p class="text-subtitle text-muted">Financial years list.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{url('/admin/financial-years')}}">Financial Years</a></li>
                        <li class="breadcrumb-item active" aria-current="page">List</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
           <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Compact form-->
                <form id="searchForm" name="searchForm" class="float-start">
                    <div class="d-flex align-items-center  w-md-800px">
                        <!--begin::Input group-->
                        <div class="position-relative w-md-200px me-md-2">
                            <input id="name" name="name" confirmation="false" class="form-control" placeholder="Search By Year">
                        </div>
                        <div class="position-relative w-md-200px me-md-2">
                            <input id="opd_number" name="opd_number" confirmation="false" class="form-control numberonly" placeholder="Search By OPD Number">
                        </div>
                        <!--end::Input group-->
                        <!--begin:Action-->
                        <div class="d-flex align-items-center">
                            <button type="button" id="searchbuttons" onclick="filterData('search');" style="margin-right:10px;" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Search</button>
                            <button type="reset" class="btn btn-sm btn-dark btn-active-light-primary me-5" data-kt-menu-dismiss="true"  onclick="resetFilterForm();">Reset</button>
                        </div>
                        <!--end:Action-->
                    </div>
                </form>
                <a href="{{url('/admin/add-financial-year')}}" class="btn icon btn-sm btn-outline-success float-end">Add New Financial Year</a>
            </div>
            <!--end::Card body-->
        </div>
    </section>
    <!-- Table head options start -->
    <section class="section">
        <div class="row" id="table-head">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <!-- table head dark -->
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>YEAR</th>
                                        <th>OPD NUMBER</th>
                                        <th>STATUS</th>
                                        <th>CREATED</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody id="replaceHtml">
                                    <tr>
                                        <td colspan="10" class="text-center"><img src="{{ asset('public/admin/images/svg/oval.svg') }}" class="me-4" style="width: 3rem" alt="audio"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Table head options end -->
</div>
<script>
$(document).ready(function(){
    filterData('simple');
    $('.numberonly').keypress(function(e){
        var charCode = (e.which) ? e.which : event.keyCode;
        if (String.fromCharCode(charCode).match(/[^0-9]/g)) return false;
    });
});
function filterData(type = null){
    if(type =='search'){$('#searchbuttons').html('Searching..');}
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: $('#searchForm').serialize(),
		url: "{{ url('/admin/financial-years_paginate') }}",
		success: function(response){
			$('#replaceHtml').html(response);
            $('#searchbuttons').html('Search');
		}
	});
}
</script>

@endsection
