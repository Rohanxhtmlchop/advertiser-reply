@extends('layouts.default')
@section('title') {{'API Insert'}} @endsection
@section('content')
<style>

/*---------signup-step-------------*/

</style>
    <div class="main-content campaign-view-sec step-form-sec">
        <div class="section__content section__content--p10">
            <div class="container-fluid">
                <section class="signup-step-container">
                    <div class="container-fluid">
                        <div class="row d-flex justify-content-center align-items-center">
                            <div class="col-lg-12">
                                <div class="wizard">
                                    <div class="wizard-inner">
                                        <div class="connecting-line"></div>
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#upload" data-toggle="tab" aria-controls="upload" role="tab" aria-expanded="true"><span class="round-tab">1 </span> <i>Upload</i></a>
                                            </li>
                                            <li role="presentation" class="disabled">
                                                <a href="#fieldmapping" data-toggle="tab" aria-controls="fieldmapping" role="tab" aria-expanded="false"><span class="round-tab">2</span> <i>Field Mapping</i></a>
                                            </li>
                                            <li role="presentation" class="disabled">
                                                <a href="#preview" data-toggle="tab" aria-controls="preview" role="tab"><span class="round-tab">3</span> <i>Preview</i></a>
                                            </li>
                                        </ul>
                                    </div>
                                
                                    <form role="form" method="post" class="login-box" id="json_add_data_form">
                                        <div class="tab-content" id="main_form">
                                            <div class="tab-pane active" role="tabpanel" id="upload">
                                                <h4 class="text-center">Upload</h4>
                                                <div class="upload-fields row justify-content-center align-items-center">
                                                    <div class="col-md-7">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-4 form-group text-right">
                                                                <label for="upload_json">Upload json File</label>
                                                            </div>
                                                            <div class="col-md-8 form-group">
                                                            <input class="au-input au-input--full form-control" type="file" name="json_file" id="json_file" accept=".json">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="row justify-content-center align-items-center">
                                                            <div class="col-md-4 form-group text-right">
                                                                <label for="table_list">Table List</label>
                                                            </div>
                                                            <div class="col-md-8 form-group">
                                                                @if( count( $tableList ) > 0 )
                                                                    <select name="table_list" id="table_list"  class="au-input au-input--full valid form-control" aria-invalid="false">
                                                                        <option value="">Table Option</option>
                                                                        @foreach( $tableList as $tableListKey => $tableListVal )
                                                                            <option value="{{ $tableListVal }}">{{ ucwords($tableListVal) }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <ul class="list-inline pull-right">
                                                    <li>
                                                        <div class="text-center api-insert-sec">
                                                            <button type="button" class="default-btn next-step">Next step</button>
                                                            <span class="spinner spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane" role="tabpanel" id="fieldmapping">
                                                <h4 class="text-center">Field Mapping</h4>
                                                <div class="table-responsive" >
                                                    <table class="table custom-table table-borderless table-striped dataTable no-footer json-mapping-view-sec" id="json-mapping-view-sec" style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Field Name</th>
                                                                <th>Validation</th>
                                                                <th>Field Value</th>
                                                                <th>Field Mapping</th>
                                                                <th>Comment</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-field-list" id="db_fields_mapping"></tbody>
                                                    </table>
                                                </div>
                                                <ul class="list-inline pull-right">
                                                    <li><button type="button" class="default-btn prev-step">Back</button></li>
                                                    <li><div class="text-center api-insert-sec">
                                                            <button type="button" class="default-btn next-step">Next step</button>
                                                            <span class="spinner spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane" role="tabpanel" id="preview">
                                                <h4 class="text-center">Preview</h4>
                                                <div class="row justify-content-center">
                                                    <div class="col-md-7 new-campaign-table-box table-responsive">
                                                        <table  class="table custom-table table-borderless table-striped dataTable no-footer"  style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Field Name</th>
                                                                    <th>Field value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="table-field-list"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <ul class="list-inline pull-right">
                                                    <li><button type="button" class="default-btn prev-step">Back</button></li>
                                                    <li><div class="text-center api-insert-sec">
                                                            <button type="button" class="default-btn next-step">Finish</button>
                                                            <span class="spinner spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                                        </div>
                                                    </li>
                                                    <li><button type="button" class="default-btn next-step">Cancel</button></li>
                                                </ul>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

@stop