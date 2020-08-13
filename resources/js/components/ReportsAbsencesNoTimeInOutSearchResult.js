import React, { Component } from 'react';
import { Card, Jumbotron } from 'react-bootstrap';
import cookie from 'react-cookies';
import sanitize from 'sanitize-filename';
import ManualTimeInOutModal from './ManualTimeInOutModal';

export default class ReportsAbsencesNoTimeInOutSearchResult extends Component {
    constructor(props) {
        super(props);
        this.handleCloseManualTimeInOutModal = this.handleCloseManualTimeInOutModal.bind(this);
        this.handleSubmitManualTimeInOutModal = this.handleSubmitManualTimeInOutModal.bind(this);
        this.state = {
            isShowManualTimeInOutModal: false,
            manualTimeInOutBiometricId: '',
            manualTimeInOutName: '',
            manualTimeInOutLogDate: '',
        };
    }

    handleCloseManualTimeInOutModal(e) {
        const self = this;
        self.setState({
            isShowManualTimeInOutModal: false,
            manualTimeInOutBiometricId: '',
            manualTimeInOutName: '',
            manualTimeInOutLogDate: '',
        });
    }

    handleSubmitManualTimeInOutModal(e) {
        e.preventDefault();
        const self = this;
        const token = cookie.load('token');
        const table = $('.data-table-wrapper').find('table').DataTable();
        const modal = $('#manualTimeInOutModal');
        const {
            manualTimeInOutBiometricId: biometric_id,
            manualTimeInOutName: name,
            manualTimeInOutLogDate: log_date,
        } = this.state;
        const time_in = $(e.currentTarget).find('[name=time_in]').val();
        const time_out = $(e.currentTarget).find('[name=time_out]').val();
        const reason = $(e.currentTarget).find('[name=reason]').val();
        const actionEndPoint = apiBaseUrl + '/override/manual-attendance-logs?token=' + token;

        axios
            .post(actionEndPoint, {
                biometric_id,
                log_date,
                time_in,
                time_out,
                reason,
            })
            .then((response) => {
                table.ajax.reload(null, false);
                self.setState({
                    isShowManualTimeInOutModal: false,
                    manualTimeInOutBiometricId: '',
                    manualTimeInOutName: '',
                    manualTimeInOutLogDate: '',
                });
            })
            .catch((error) => {
                if (error.response) {
                    const { response } = error;
                    const { data } = response;
                    const { errors } = data;
                    for (const key in errors) {
                        $('[name=' + key + ']', modal)
                            .addClass('is-invalid')
                            .closest('.form-group')
                            .find('.invalid-feedback')
                            .text(errors[key][0]);
                    }
               }
            });
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
                { 'data': 'total_late_undertime', 'className': 'text-right' },
                { 'data': 'reason' },
                {
                    'data': null,
                    'render': function (data, type, row) {
                        var manualTimeInOutBtn = !row.time_in && !row.time_out
                            ? '<a href="#" class="btn btn-warning manual-time-in-out" data-toggle="modal" data-target="#manualTimeInOutModal" data-date="' + row.date + '" data-biometric-id="' + row.biometric_id + '" data-name="' + row.name + '"><i class="fa fa-clock-o"></i></a>'
                            : null;

                        return manualTimeInOutBtn;
                    }
                },
            ],
        });

        $(document).on('click', '.manual-time-in-out', function(e) {
            e.preventDefault();
            const manualTimeInOutBiometricId = $(e.currentTarget).data('biometric-id'); 
            const manualTimeInOutName = $(e.currentTarget).data('name');
            const manualTimeInOutLogDate = $(e.currentTarget).data('date');
            self.setState({
                isShowManualTimeInOutModal: true,
                manualTimeInOutBiometricId,
                manualTimeInOutName,
                manualTimeInOutLogDate,
            });
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
        
        return `Absences (No Time-In/Out) Report --- ${label}`;
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
        const {
            isShowManualTimeInOutModal,
            manualTimeInOutBiometricId,
            manualTimeInOutName,
            manualTimeInOutLogDate,
        } = this.state;
        
        const dataTable = $('.data-table-wrapper')
            .find('table')
            .DataTable();

        if (startDate && endDate) {
            const token = cookie.load('token');
            let filters = 'show=with_lates_or_undertime_only&type=' + (biometricId ? 'individual' : 'group') + '&start_date=' + startDate + '&end_date=' + endDate + (biometricId ? '&biometric_id=' + biometricId : '');
            if (roleId) {
                filters += `&role_id=${roleId}`;
            }
            dataTable.ajax.url(apiBaseUrl + '/reports/absences?token=' + token + '&' + filters);
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
                                    <th scope="col">Total (HH:MM:SS)</th>
                                    <th scope="col">Reason</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>            
                        </table>
                    </Card.Body>
                </Card>
                <ManualTimeInOutModal
                    isShow={isShowManualTimeInOutModal}
                    biometricId={manualTimeInOutBiometricId}
                    name={manualTimeInOutName}
                    logDate={manualTimeInOutLogDate}
                    handleClose={this.handleCloseManualTimeInOutModal}
                    handleSubmit={this.handleSubmitManualTimeInOutModal}></ManualTimeInOutModal>
            </div>
        );
    }
}
