@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Admin Management</h3>
                    <p class="text-subtitle text-muted">Admin list.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Admin</li>
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
                                <input id="name" name="name" confirmation="false" class="form-control" placeholder="Search By Name">
                            </div>
                            <div class="position-relative w-md-200px me-md-2">
                                <input id="email" name="email" confirmation="false" class="form-control" placeholder="Search By Email">
                            </div>
                            <div class="position-relative w-md-200px me-md-2">
                                <input id="mobile" name="mobile" confirmation="false" class="form-control" placeholder="Search By Phone">
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
                    <!--<a data-bs-toggle="modal" data-bs-target="#uploadCsvModal" class="btn icon btn-sm btn-outline-warning float-end" style="margin-left:10px">Import CSV</a>-->
                    <a href="{{url('/admin/add-admin')}}" class="btn icon btn-sm btn-outline-success float-end">Add New Admin</a>
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
                                            <th>NAME</th>
                                            <th>TYPE</th>
                                            <th>DEPARTMENT</th>
                                            <th>EMAIL</th>
                                            <th>MOBILE</th>
                                            <th>STATUS</th>
                                            <th>LAST LOGIN</th>
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
    <div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Import CSV</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
            <label for="basicInput">Choose File</label>
            <input type="file" class="form-control" name="upload_csv_file" id="upload_csv_file">
            </div>
          </div>
          <div class="modal-footer">
          <a href="{{url('/storage/sample-csv/customers.csv')}}" target="_blank" class="btn btn-success">Download Sample CSV</a>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" onclick="UploadCSV();" id="uploadCsvBtn" class="btn btn-primary">Upload</button>
          </div>
        </div>
      </div>
    </div>
<script>
$(document).ready(function(){
    filterData('simple');
});
function filterData(type = null){
    if(type =='search'){$('#searchbuttons').html('Searching..');}
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: $('#searchForm').serialize(),
		url: "{{ url('/admin/admins_paginate') }}",
		success: function(response){
			$('#replaceHtml').html(response);
            $('#searchbuttons').html('Search');
		}
	});
}
function UploadCSV(){
	if($.trim($('#upload_csv_file').val()) != ''){
		var file_data = $('#upload_csv_file').prop('files')[0];
		var form_data = new FormData();
		form_data.append('file', file_data);
		
		$('#uploadCsvBtn').html('......');
		$.ajax({
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: "{{route('imports.customer')}}",
			dataType: 'text',  // <-- what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			success: function(msg){				
				$('#uploadCsvBtn').html('Upload');				
				if(msg == 'Success'){
					$('#uploadCsvModal').modal('hide');
					swal("", "CSV Uploaded successfully", "success").then((value) => {});
					resetFilterForm();
				}else if(msg == 'InvalidFileType'){
					swal("Error!", 'Please select valid file format', "error");
				}else if(msg == 'ChoseFile'){
					swal("Error!", 'Please choose file', "error");
				}
				
			},error: function(ts){
				$('#error500').modal('show');
			}
		});
	}
}
</script>

@endsection