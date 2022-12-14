@extends('master')
@section('title')
    Agent report
@endsection
@section('css')
    <style type="text/css">
        .bootstrap-select {
            margin: 0;
        }

        .toolbar-filter {
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 5px
        }

        .form-control,
        .btn {
            padding: 3px 6px
        }

        .panel-body {
            padding: 10px
        }

        .form-horizontal .control-label {
            padding-top: 3px;
        }

        .panel {
            margin-bottom: 10px
        }

        .fixed-table-container {
            border-radius: 0;
            -webkit-border-radius: 0;
            -moz-border-radius: 0;
        }

        @media (min-width: 992px) {
            #page-content {
                padding: 5px 5px 0;
            }
        }


    </style>
    <script src="{{asset('assets/js/jquery-1.11.3.min.js')}}"></script>
    <link href="{!! asset('backend/plugins_v2/bootstrap-table/bootstrap-table.min.css') !!}" rel="stylesheet">

    <link href="{{ asset('assets/nifty/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/nifty/css/nifty.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/nifty/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <script src="{!! asset('backend/plugins_v2/bootstrap-table/bootstrap-table.min.js') !!}"></script>
    <script src="{!! asset('backend/plugins_v2/bootstrap-table/locale/bootstrap-table-vi-VN.min.js') !!}"></script>

    <link href="{{ asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <style type="text/css">
        .bootstrap-table .table>thead>tr>th{
            vertical-align: middle!important;
            text-align: center!important;
        }
    </style>
@endsection

@section('content')
    <!--CONTENT CONTAINER-->
    <div id="content-container">
        <!--Page Title-->
        <div id="page-title">
            <h1 class="page-header text-overflow">Agent report</h1>
        </div>
        <!--End page title-->
        <div id="page-content">

            <div class="panel panel-primary panel-colorful">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form method="POST" class="form-horizontal" id="frmFilter" onSubmit="return false;"  style="min-height: 100px;display: flex;justify-content: space-evenly;">
                                @csrf
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">V??ng mi???n</label>
                                        <div class="col-sm-6">
                                            <select id="region" class="custom_filter form-control" name="region">
                                                <option value="">--T???t c???--</option>
                                                <option value="hcm">H??? Chi Minh</option>
                                                <option value="hni">H?? N???i</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Th???i gian</label>
                                        <div class="col-sm-6">
                                            <input placeholder="Ng??y (T??? - ?????n)" id="range_date" class="form-control tooltips date-range" data-placement="top" data-original-title="T??m ki???m theo ng??y t???o" readonly="" name="range_date" type="text" value="{{ $dateRange }}" autocomplete="off">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Agent h???i tr???</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" id="staff_text" name="staff_text" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <button id="filter-form" class="btn btn-default" type="submit" title="T??m ki???m">T??m ki???m</button>
                                        <button id="btnExportExcel" class="btn btn-default" type="button" title="Export">Xu???t Excel</button>
                                    </div>
                                </div>
                            </form>

                            <form   id="formExportExcel" action="{{url('/agent-report')}}" method="post">
                                @csrf()
                                <input type="hidden" id="region_id" name="region" value="">
                                <input type="hidden" id="range_date_id" name="range_date"  value="{{ $dateRange }}">
                                <input type="hidden" id="staff_text_id" name="staff_text" >
                                <input type="hidden" name="export" value="export">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-body">
                    <table id="table-data" class="add-niftycheck" data-toggle="table" data-locale="vi-VN"
                        data-toolbar="#table-toolbar" data-url='{{url('admin/agent-report-v2')}}'
                        data-show-refresh="false" data-show-columns="true" data-pagination="true"
                        data-side-pagination="server" data-page-size="25" data-query-params="queryParams" >
                        <thead style="background: #5fa2dd; color: white;">
                            <tr>
                                <th scope="col" rowspan="2" data-field="stt"  >STT</th>
                                <th scope="col" rowspan="2" data-field="username" data-sortable="true"  data-formatter="detailFormatter">Nh??n vi??n</th>
                                <th scope="col" rowspan="2" data-field="time_login" data-sortable="true">Th???i gian login</th>
                                <th scope="col" rowspan="2" data-field="time_logout" data-sortable="true">Th???i gian logout</th>
                                <th scope="col" colspan="3">Th???i gian Talking</th>
                                <th scope="col" rowspan="2" data-field="total_time_ready" data-sortable="true">Th???i gian <br/> Ready</th>
                                <th scope="col" rowspan="2" data-field="total_time_not_ready" data-sortable="true">Th???i gian <br/> Not Ready</th>
                                <th scope="col" rowspan="2" data-field="total_update_inside" data-sortable="true">S??? l??n NR <br/> Update Inside</th>
                                <th scope="col" rowspan="2" data-field="total_restroom" data-sortable="true">S??? l???n NR <br/> Rest Room</th>
                                <th scope="col" rowspan="2" data-field="total_unanswered" data-sortable="true">S??? l???n <br/> Unanswered</th>
                                <th scope="col" rowspan="2" data-field="total_outbound" data-sortable="true">S??? l???n <br/> Outbound</th>
                                <th scope="col" rowspan="2" data-field="inbound" >T???ng cu???c g???i <br/> Inbound</th>
                                <th scope="col" rowspan="2" data-field="outbound" >T???ng cu???c g???i <br/> Outbound</th>
                                <th scope="col" rowspan="2" data-field="totalInboundOutbound" >T???ng cu???c g???i</th>
                                <th scope="col" rowspan="2" data-field="onphone_late" data-sortable="true">Onphone tr???</th>
                            </tr>
                            <tr>
                                <th scope="col" rowspan="1" data-field="total_time_answered" data-sortable="true">Inbound</th>
                                <th scope="col" rowspan="1" data-field="total_time_outbound" data-sortable="true">Outbound</th>
                                <th scope="col" rowspan="1" data-field="totalTimeInOut" >T???ng</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <script type="text/javascript">

                var $table = $('#table-data');
                var btnExportExcel = $('#btnExportExcel');

                @if(!empty($msg))
                alert("{{$msg}}");
                @endif

                $('#frmFilter').submit(function() {
                    return formGo();
                });

                $(document).ready(function() {

                    $table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function() {
                        $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);
                        $delete.prop('disabled', !$table.bootstrapTable('getSelections').length);
                    }).on('load-success.bs.table', function() {
                        var tooltip = $('.add-tooltip');
                        if (tooltip.length) tooltip.tooltip();
                    });

                    // select_filter
                    // $('.custom_filter').chosen({width:'100%'});
                    {{--  $('.custom_filter').on('change', function(evt, params) {
                        $table.bootstrapTable('refresh');
                    });  --}}
                });


                function detailFormatter(value, row, index) {
                    var date =  $('#frmFilter #range_date').val();
                    var removeBtn = [
                        `<a  onclick="showReportDetail(this, '${value}', '${date}' );return false;"`,
                        '>'+value + '</a>',
                    ].join('');

                    return [removeBtn].join(' ');
                }
                function showReportDetail(el, username, date){

                    var d = new Date();
                    var n = 'md' + d.getTime();
                    builModal(n, 'Recent State History - ' + username, '<p class="text-center">Loading...</p>', '', 0, (function(){
                        $.ajax({
                            type: "POST",
                            url: baseUrl+"/report/detail-agent",
                            data: {'username': username, 'date': date ,'_token': '{{csrf_token()}}' },
                            cache:false,
                            dataType: 'html',
                            beforeSend: function() {
                            },
                            success: function(res){
                                builModal(n, 'Ola', res, '', 0, (function(){
                                    $(document).ready((function(){
                                    }));
                                }));
                            }
                        });
                    }));
                }

                function queryParams(params) {
                    $('#frmFilter :input').each(function(i, e) {
                        var name = $(e).attr('name');
                        if (typeof name !== typeof undefined && name !== false) {
                            if ($(e).attr('type') != 'radio' || $(e).is(':checked')) {
                                params[name] = $(e).val();

                            }
                        }
                    });

                    return params;
                }

                function formatFiledJson(value, row, index) {
                    if (value) {
                        var obj = jQuery.parseJSON(value);
                        var name = '';
                        $.each(obj, function(key, value) {
                            name += ', ' + value;
                        });
                        return name.substring(1);
                    }
                    return '';
                }

                function formGo() {
                    var rangeDateValue  = $('#range_date').val();
                    var rangeDateArray  =  rangeDateValue.split(' - ');
                    var start = moment(rangeDateArray[0], 'DD/MM/YYYY'),
                        end = moment(rangeDateArray[1], 'DD/MM/YYYY');
                    if (  end < start || start.format('MM-YYYY') != end.format('MM-YYYY') ) {
                        return alert('Ng??y k???t th??c ph???i l???n h??n ng??y b???t ?????u, v?? ph???i c??ng 1 th??ng.');
                    }
                    $table.bootstrapTable('destroy').bootstrapTable().bootstrapTable('selectPage', 1);
                    return false;
                }

                btnExportExcel.click((e) =>{
                    $('#region_id').val($('#frmFilter #region').val());
                    $('#range_date_id').val($('#frmFilter #range_date').val());
                    $('#staff_text_id').val($('#frmFilter #staff_text').val());
                    $('#formExportExcel').submit();
                });

            </script>
        </div>
    </div>
    <!--END CONTENT CONTAINER-->
@endsection

@section('js')

    <script src="{{ asset('assets/plugins/bootstrap-daterangepicker/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap-daterangepicker/date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        var d = new Date();
        var time = d.getTime();

        var today = new Date();
        var beginOfLastMonth = moment().subtract(1, 'months');
        var options = {
            autoApply: true,
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: "Xong",
                cancelLabel: "H???y",
                fromLabel: "T???",
                toLabel: "?????n",
                customRangeLabel: "T??y ch???nh",
                weekLabel: "W",
                daysOfWeek: [
                    "CN",
                    "T2",
                    "T3",
                    "T4",
                    "T5",
                    "T6",
                    "T7"
                ],
                monthNames: [
                    "Th??ng 1",
                    "Th??ng 2",
                    "Th??ng 3",
                    "Th??ng 4",
                    "Th??ng 5",
                    "Th??ng 6",
                    "Th??ng 7",
                    "Th??ng 8",
                    "Th??ng 9",
                    "Th??ng 10",
                    "Th??ng 11",
                    "Th??ng 12"
                ],
                firstDay: 1,
            },
            ranges: {
                "H??m nay": [
                    today,
                    today
                ],
                "H??m qua": [
                    moment().subtract(1, 'day'),
                    moment().subtract(1, 'day')
                ],
                "7 ng??y g???n ????y": [
                    moment().subtract('days', 7),
                    today
                ],
                "30 ng??y g???n ????y": [
                    moment().subtract(30, 'days'),
                    today
                ],
                "Th??ng n??y": [
                    moment().format("01/MM/YYYY"),
                    moment().daysInMonth() + moment().format("/MM/YYYY")
                ],
                "Th??ng tr?????c": [
                    beginOfLastMonth.format("01/MM/YYYY"),
                    moment(beginOfLastMonth).endOf('month')
                ]
            },
            alwaysShowCalendars: true,
            maxDate: today,
            opens: 'right',
            //autoUpdateInput: false,
            };

        $(document).ready(function () {
            $('.date-range').daterangepicker(options, function(start, end) {
                $('.date-range').val(moment(start).format('DD/MM/YYYY') + ' - ' + moment(end).format('DD/MM/YYYY'));
            });
        });
    </script>
@endsection
