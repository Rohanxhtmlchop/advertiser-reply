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
                            <div class="col-lg-8">
                                <div class="wizard">
                                    <div class="wizard-inner">
                                        <div class="connecting-line"></div>
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#upload" data-toggle="tab" aria-controls="upload" role="tab" aria-expanded="true"><span class="round-tab">1 </span> <i>Upload</i></a>
                                            </li>
                                            <li role="presentation" class="disabled">
                                                <a href="#fieldmapping" data-toggle="tab" aria-controls="fieldmapping" role="tab" aria-expanded="false"><span class="round-tab">2</span> <i>Column Mapping</i></a>
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
                                                <div class="upload-fields">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="upload_json">Upload json File</label>
                                                            </div>
                                                            <div class="col-md-8 form-group">
                                                            <input class="au-input au-input--full form-control" type="file" name="json_file" id="json_file" accept=".json">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="table_list">Table List</label>
                                                            </div>
                                                            <div class="col-md-8 form-group">
                                                            @if( count( $tableList ) > 0 )
                                                                <select name="table_list" id="table_list"  class="au-input au-input--full valid" aria-invalid="false">
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
                                                <h4 class="text-center">Column Mapping</h4>
                                                <div class="row" id="db_fields_mapping">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <p><strong>Column Name</strong></p>
                                                            </div>  
                                                            <div class="col-md-6 form-group">
                                                                <p><strong>Map Fields</strong></p>
                                                            </div>         
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <span><strong>Name</strong></span>
                                                                <p>Sample:Test Data</p>
                                                            </div>  
                                                            <div class="col-md-6 form-group">
                                                                <select name="table_field_list" id="table_field_list"  class="au-input au-input--full valid" aria-invalid="false">
                                                                    <option value="">Table Fields Option</option>
                                                                    <option value="name">Name</option>
                                                                    <option value="deal_unit">Deal Unit</option>
                                                                    <option value="demo">Demo</option>
                                                                    <option value="ae">AE</option>
                                                                </select>
                                                            </div>         
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <span><strong>Deal Unit</strong></span>
                                                                <p>Sample:123</p>
                                                            </div>  
                                                            <div class="col-md-6 form-group">
                                                                <select name="table_field_list" id="table_field_list"  class="au-input au-input--full valid" aria-invalid="false">
                                                                    <option value="">Table Fields Option</option>
                                                                    <option value="name">Name</option>
                                                                    <option value="deal_unit">Deal Unit</option>
                                                                    <option value="demo">Demo</option>
                                                                    <option value="ae">AE</option>
                                                                </select>
                                                            </div>         
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <span><strong>AE</strong></span>
                                                                <p>Sample:789</p>
                                                            </div>  
                                                            <div class="col-md-6 form-group">
                                                                <select name="table_field_list" id="table_field_list"  class="au-input au-input--full valid" aria-invalid="false">
                                                                    <option value="">Table Fields Option</option>
                                                                    <option value="name">Name</option>
                                                                    <option value="deal_unit">Deal Unit</option>
                                                                    <option value="demo">Demo</option>
                                                                    <option value="ae">AE</option>
                                                                </select>
                                                            </div>         
                                                        </div>
                                                    </div>
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
                                                <div class="row">
                                                    <div class="new-campaign-table-box table-responsive">
                                                        <table  class="table custom-table table-borderless table-striped dataTable no-footer"  style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Field Name</th>
                                                                    <th>Field value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="table-field-list">
                                                                <tr class="tr-shadow">
                                                                    <th class="new-campaign-id">1100000004</th>
                                                                    <td class="new-campaign-name">New I3 all Electric</td>
                                                                </tr>
                                                            </tbody>
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