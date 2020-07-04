import React, { Component } from 'react';
import { Card, Jumbotron } from 'react-bootstrap';
import cookie from 'react-cookies';
import sanitize from 'sanitize-filename';

export default class ReportsLateUndertimeSearchResult extends Component {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        const self = this;
        const { handleSearchResultErrors } = self.props;

        const exportButtons = window.exportButtonsBase;
        exportButtons[0].filename = () => { return this.initExportFilename(); };
        exportButtons[0].footer = true;
        exportButtons[1].filename = () => { return this.initExportFilename(); };
        exportButtons[1].footer = true;
        exportButtons[1].orientation = 'landscape';
        exportButtons[1].title = () => { return this.initExportTitle(); };

        const dataTable = $(this.refs.attendanceLogsSearchResult).DataTable({
            ajax: {
                error: function (xhr, error, code) {
                    if (code === 'Unauthorized') {
                        location.reload();
                    }

                    const { status, responseJSON } = xhr;
                    if (status == 422) {
                        const { errors } = responseJSON;
                        handleSearchResultErrors(errors);
                    }

                    dataTable.clear().draw();
                }
            },
            ordering: false,
            paging: false,
            scrollCollapse: true,
            scrollY: "300px",
            scrollX: true,
            searching: false,
            buttons: exportButtons,
            columns: [
                { 'data': 'biometric_id' },
                { 'data': 'name' },
                { 'data': 'display_date' },
                { 'data': 'expected_time_in' },
                { 'data': 'expected_time_out' },
                { 'data': 'time_in' },
                { 'data': 'time_out' },
                { 'data': 'late', 'className': 'text-right' },
                { 'data': 'undertime', 'className': 'text-right' },
                { 'data': 'adjustment', 'className': 'text-right' },
                { 'data': 'total_late_undertime', 'className': 'text-right' },
                { 'data': 'reason' },
                {
                    'data': null,
                    'render': function (data, type, row) {
                        var manualTimeInOutBtn = !row.is_adjusted
                            ? '<a href="#" ' +
                                    'class="adjustment-late-undertime btn btn-warning" ' +
                                    'data-toggle="modal" '+
                                    'data-target="#adjustmentLateUndertimeModal" ' +
                                    'data-date="' + row.date + '" ' +
                                    'data-biometric-id="' + row.biometric_id + '" ' +
                                    'data-name="' + row.name + '" ' +
                                    'data-late="' + row.late + '" ' +
                                    'data-undertime="' + row.undertime + '" ' +
                                    'data-total-late-undertime="' + row.total_late_undertime + '">' +
                                    '<i class="fa fa-clock-o"></i>' +
                                '</a>'
                            : null;

                        return manualTimeInOutBtn;
                    }
                }
            ],
            columnDefs: [
                { 'orderable': false, 'targets': [0, 1] }
            ],
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api(), data;
    
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
    
                // Total late over all pages
                const totalLate = api
                    .column( 7 )
                    .data()
                    .map( function(time) {
                      return TADHelper.timeToSeconds(time);
                    })
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Total under time over all pages
                const totalUndertime = api
                    .column( 8 )
                    .data()
                    .map( function(time) {
                      return TADHelper.timeToSeconds(time);
                    })
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Total adjustment over all pages
                const totalAdjustment = api
                    .column( 9 )
                    .data()
                    .map( function(time) {
                      return TADHelper.timeToSeconds(time);
                    })
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Total late / undertime over all pages
                const totalLateUndertime = api
                    .column( 10 )
                    .data()
                    .map( function(time) {
                      return TADHelper.timeToSeconds(time);
                    })
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Update footer
                $( api.column( 7 ).footer() ).html(
                  TADHelper.formatTimeDisplay(totalLate)
                );
                $( api.column( 8 ).footer() ).html(
                  TADHelper.formatTimeDisplay(totalUndertime)
                );
                $( api.column( 9 ).footer() ).html(
                  TADHelper.formatTimeDisplay(totalAdjustment)
                );
                $( api.column( 10 ).footer() ).html(
                  TADHelper.formatTimeDisplay(totalLateUndertime)
                );
              }
            
        });

    }

    componentWillUnmount() {
        $('.data-table-wrapper')
            .find('table')
            .DataTable()
            .destroy(true);
    }

    initExportTitle()
    {
        const {
            roleId,
            biometricId,
            biometricName,
            startDate,
            endDate
        } = this.props;

        const user = `User: ${ biometricId ? `${biometricId} ${biometricName}` : 'All' }` +
            `${(roleId && !biometricId) ? ` (${roleId})` : '' }`;
        const label = `${user}${user ? ' ' : ''}From: ${startDate} To: ${endDate}`;
        
        return `Attendance Logs ${label}`;
    }

    initExportFilename()
    {
        return sanitize(this.initExportTitle());
    }

    render() {
        const {
            roleId,
            biometricId,
            biometricName,
            startDate,
            endDate,
        } = this.props;
        
        const dataTable = $('.data-table-wrapper')
            .find('table')
            .DataTable();

        if (startDate && endDate) {
            const token = cookie.load('token');
            let filters = 'show=with_lates_or_undertime_only&type=' + (biometricId ? 'individual' : 'group') + '&start_date=' + startDate + '&end_date=' + endDate + (biometricId ? '&biometric_id=' + biometricId : '');
            if (roleId) {
                filters += `&role_id=${roleId}`;
            }
            dataTable.ajax.url(apiBaseUrl + '/reports/late-undertime?token=' + token + '&' + filters);
            dataTable.ajax.reload();
        } else {
            dataTable.clear().draw();
        }

        const hideTable = !startDate || !endDate;

        return (
            <div>
                {
                    (hideTable) &&
                    <Jumbotron>
                        <p className="text-center">
                            <i className="fa fa-5x fa-info-circle"/><br/>
                            Start by filtering records to generate report.
                        </p>
                    </Jumbotron>
                }

                <Card style={{ display: (hideTable ? 'none' : '') }}>
                    <Card.Header>
                        <h4><i className="fa fa-search"/> Search Result</h4>
                        <p>
                            <span className="badge badge-pill badge-primary">
                                User: { biometricId ? `${biometricId} ${biometricName}` : 'All' }
                                {(roleId && !biometricId) ? ` (${roleId})` : '' }
                            </span>&nbsp;
                            <span className="badge badge-pill badge-secondary">
                                From: {startDate} To: {endDate}
                            </span>
                        </p>
                    </Card.Header>
                    <Card.Body>
                        <table ref="attendanceLogsSearchResult" className="table table-striped" style={{width: 100+'%'}}>
                            <thead>
                                <tr>
                                    <th scope="col">Biometric ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Expected Time-in</th>
                                    <th scope="col">Expected Time-out</th>
                                    <th scope="col">Time-in</th>
                                    <th scope="col">Time-out</th>
                                    <th scope="col">Late (HH:MM:SS)</th>
                                    <th scope="col">Under Time (HH:MM:SS)</th>
                                    <th scope="col">Adjustment (HH:MM:SS)</th>
                                    <th scope="col">Total (HH:MM:SS)</th>
                                    <th scope="col">Remarks</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total:</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </Card.Body>
                </Card>
            </div>
        );
    }
}
