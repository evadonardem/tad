import React, { Component } from 'react';
import { Button, Card } from 'react-bootstrap';
import cookie from 'react-cookies';
import AttendanceLogOverridesAddEditModal from './AttendanceLogOverridesAddEditModal';
import CommonDeleteModal from './CommonDeleteModal';

export default class AttendanceLogOverrides extends Component {
    constructor(props) {
        super(props);        
        this.handleCloseAttendanceLogOverrideModal = this.handleCloseAttendanceLogOverrideModal.bind(this);
        this.handleSubmitAttendanceLogOverrideModal = this.handleSubmitAttendanceLogOverrideModal.bind(this);
        this.handleAddAttendanceLogOverride = this.handleAddAttendanceLogOverride.bind(this);

        this.handleCloseDeleteAttendanceLogOverrideModal = this.handleCloseDeleteAttendanceLogOverrideModal.bind(this);
        this.handleSubmitDeleteAttendanceLogOverrideModal = this.handleSubmitDeleteAttendanceLogOverrideModal.bind(this);

        this.state = {
            attendanceLogOverrideId: '',
            attendanceLogOverrideDate: '',
            attendanceLogOverrideRole: '',
            attendanceLogOverrideExpectedTimeIn: '',
            attendanceLogOverrideExpectedTimeOut: '',
            attendanceLogOverrideLogTimeIn: '',
            attendanceLogOverrideLogTimeOut: '',
            attendanceLogOverrideReason: '',
            isShowAddEditAttendanceLogOverrideModal: false,
            isEditOverride: false,
            isDeleteOverride: false,
        };
    }

    componentDidMount() {
        const self = this;
        const token = cookie.load('token');

        const exportButtons = window.exportButtonsBase;
        const exportFilename = 'Attendance Log Overrides';
        const exportTitle = 'Attendance Log Overrides';
        exportButtons[0].filename = exportFilename;
        exportButtons[1].filename = exportFilename;
        exportButtons[1].title = exportTitle;

        $(this.refs.attendanceLogOverridesList).DataTable({
            ajax: apiBaseUrl + '/override/attendance-logs?token=' + token,
            ordering: false,
            searching: false,
            buttons: exportButtons,
            responsive: true,
            columns: [
                { 'data': 'log_date'},
                { 'data': 'role_id' },
                { 'data': 'expected_time_in' },
                { 'data': 'expected_time_out' },
                { 'data': 'log_time_in' },
                { 'data': 'log_time_out' },
                { 'data': 'reason' },
                {
                    'data': null,
                    'render': function (data, type, row) {
                        const actionButtons = $('<div/>');
                        const editButton = $('<button type="button" class="btn btn-primary edit-override" data-id="' + row.id +
                            '" data-log-date="' + row.log_date + '" data-role="' + row.role_id +
                            '" data-expected-time-in="' + row.expected_time_in + '" data-expected-time-out="' + row.expected_time_out +
                            '" data-log-time-in="' + row.log_time_in + '" data-log-time-out="' + row.log_time_out +
                            '" data-reason="' + row.reason + '"/>')
                            .html('<i class="fa fa-edit"></i>');
                        const deleteButton = $('<button type="button" class="btn btn-warning delete-override" data-id="' + row.id +
                            '" data-log-date="' + row.log_date + '" data-role="' + row.role_id + '"/>')
                            .html('<i class="fa fa-trash"></i>');
                        actionButtons.append(editButton).append('&nbsp;').append(deleteButton);
                        
                        return actionButtons.html();
                    }
                },
            ]
        });

        $('.edit-override').off();
        $('.delete-override').off();
        $(document).on('click', '.edit-override', function(e) {
            const attendanceLogOverrideId = $(e.currentTarget).data('id');
            const attendanceLogOverrideDate = $(e.currentTarget).data('log-date');
            const attendanceLogOverrideRole = $(e.currentTarget).data('role');
            const attendanceLogOverrideExpectedTimeIn = $(e.currentTarget).data('expected-time-in');
            const attendanceLogOverrideExpectedTimeOut = $(e.currentTarget).data('expected-time-out');
            const attendanceLogOverrideLogTimeIn = $(e.currentTarget).data('log-time-in');
            const attendanceLogOverrideLogTimeOut = $(e.currentTarget).data('log-time-out');
            const attendanceLogOverrideReason = $(e.currentTarget).data('reason');

            self.setState({
                isShowAddEditAttendanceLogOverrideModal: true,
                isEditOverride: true,
                attendanceLogOverrideId,
                attendanceLogOverrideDate,
                attendanceLogOverrideRole: { label: attendanceLogOverrideRole, value: attendanceLogOverrideRole },
                attendanceLogOverrideExpectedTimeIn,
                attendanceLogOverrideExpectedTimeOut,
                attendanceLogOverrideLogTimeIn,
                attendanceLogOverrideLogTimeOut,
                attendanceLogOverrideReason,
            });
        });
        $(document).on('click', '.delete-override', function(e) {
            const attendanceLogOverrideId = $(e.currentTarget).data('id');
            const attendanceLogOverrideDate = $(e.currentTarget).data('log-date');
            const attendanceLogOverrideRole = $(e.currentTarget).data('role');
            self.setState({
                isDeleteOverride: true,
                attendanceLogOverrideId,
                attendanceLogOverrideDate,
                attendanceLogOverrideRole: { label: attendanceLogOverrideRole, value: attendanceLogOverrideRole },
            });
        });
    }

    componentWillUnmount() {
        $('.data-table-wrapper')
            .find('table')
            .DataTable()
            .destroy(true);
    }

    handleCloseAttendanceLogOverrideModal(e) {
        const self = this;
        self.setState({
            attendanceLogOverrideId: '',
            isShowAddEditAttendanceLogOverrideModal: false,
        });
    }

    handleSubmitAttendanceLogOverrideModal(e) {
        const self = this;
        const table = $('.data-table-wrapper').find('table').DataTable();
        self.setState({
            attendanceLogOverrideId: '',
            isShowAddEditAttendanceLogOverrideModal: false,
        });
        table.ajax.reload(null, false);
    }

    handleAddAttendanceLogOverride(e) {
        const self = this;
        self.setState({
            attendanceLogOverrideId: '',
            attendanceLogOverrideDate: '',
            attendanceLogOverrideRole: '',
            attendanceLogOverrideExpectedTimeIn: '',
            attendanceLogOverrideExpectedTimeOut: '',
            attendanceLogOverrideLogTimeIn: '',
            attendanceLogOverrideLogTimeOut: '',
            attendanceLogOverrideReason: '',
            isShowAddEditAttendanceLogOverrideModal: true,
            isEditOverride: false,
            isDeleteOverride: false,
        });
    }

    handleCloseDeleteAttendanceLogOverrideModal(e) {
        const self = this;
        self.setState({ isDeleteOverride: false });
    }

    handleSubmitDeleteAttendanceLogOverrideModal(e) {
        const self = this;
        const token = cookie.load('token');
        const { attendanceLogOverrideId } = self.state;
        const table = $('.data-table-wrapper').find('table').DataTable();
        axios
            .delete(`${apiBaseUrl}/override/attendance-logs/${attendanceLogOverrideId}?token=${token}`)
            .then(() => {
                self.setState({
                    attendanceLogOverrideId: '',
                    attendanceLogOverrideDate: '',
                    attendanceLogOverrideRole: {},
                    isDeleteOverride: false,
                });
                table.ajax.reload(null, false);
            })
            .catch(() => {
                location.reload();
            });
    }

    render() {
        const {
            attendanceLogOverrideId,
            attendanceLogOverrideDate,
            attendanceLogOverrideRole,
            attendanceLogOverrideExpectedTimeIn,
            attendanceLogOverrideExpectedTimeOut,
            attendanceLogOverrideLogTimeIn,
            attendanceLogOverrideLogTimeOut,
            attendanceLogOverrideReason,
            isShowAddEditAttendanceLogOverrideModal,
            isDeleteOverride,
            isEditOverride,
        } = this.state;

        const convertTime12to24 = (time12h) => {
            const [time, modifier] = time12h.split(' ');
            
            let [hours, minutes] = time.split(':');
            
            if (hours === '12') {
                hours = '00';
            }
            
            if (modifier === 'PM') {
                hours = parseInt(hours, 10) + 12;
            }
            
            return `${hours}:${minutes}`;
        }

        return (
            <div className="container-fluid my-4">
                <h1><i className="fa fa-calendar-plus-o"></i> Attendance Log Overrides</h1>
                <hr className="my-4"/>
                <div className="row">
                    <div className="col-md-12 pull-right">
                        <Button variant='primary' onClick={this.handleAddAttendanceLogOverride}>
                            <i className="fa fa-plus"></i> Add Override
                        </Button>
                    </div>
                </div>
                <hr className="my-4"/>
                <div className="row">
                    <div className="col-md-12">
                        <Card>
                            <Card.Body>
                                <table ref="attendanceLogOverridesList" className="table table-striped nowrap" style={{width: 100+'%'}}>
                                    <thead>
                                        <tr>
                                            <th scope="col">Log Date</th>
                                            <th scope="col">Role</th>
                                            <th scope="col">Expected Time-in</th>
                                            <th scope="col">Expected Time-out</th>
                                            <th scope="col">Log Time-in</th>
                                            <th scope="col">Log Time-out</th>
                                            <th scope="col">Reason</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </Card.Body>
                        </Card>
                    </div>
                </div>
                <AttendanceLogOverridesAddEditModal
                    isShow={isShowAddEditAttendanceLogOverrideModal}
                    isEdit={isEditOverride}
                    overrideId={attendanceLogOverrideId}
                    overrideDate={attendanceLogOverrideDate}
                    overrideRole={attendanceLogOverrideRole}
                    overrideExpectedTimeIn={attendanceLogOverrideExpectedTimeIn !== 'N/A' &&
                        attendanceLogOverrideExpectedTimeIn
                        ? convertTime12to24(attendanceLogOverrideExpectedTimeIn) : null}
                    overrideExpectedTimeOut={attendanceLogOverrideExpectedTimeOut !== 'N/A' &&
                        attendanceLogOverrideExpectedTimeOut
                        ? convertTime12to24(attendanceLogOverrideExpectedTimeOut) : null}
                    overrideLogTimeIn={attendanceLogOverrideLogTimeIn !== 'N/A' &&
                        attendanceLogOverrideLogTimeIn
                        ? convertTime12to24(attendanceLogOverrideLogTimeIn) : null}
                    overrideLogTimeOut={attendanceLogOverrideLogTimeOut !== 'N/A' &&
                        attendanceLogOverrideLogTimeOut
                        ? convertTime12to24(attendanceLogOverrideLogTimeOut) : null}
                    overrideReason={attendanceLogOverrideReason}
                    handleClose={this.handleCloseAttendanceLogOverrideModal}
                    handleSubmit={this.handleSubmitAttendanceLogOverrideModal}/>

                <CommonDeleteModal
                    isShow={isDeleteOverride}
                    headerTitle="Delete Attendance Log Override"
                    bodyText={`Delete ${attendanceLogOverrideDate} attendance log override for ${attendanceLogOverrideRole ? attendanceLogOverrideRole.value : ''}?`}
                    handleClose={this.handleCloseDeleteAttendanceLogOverrideModal}
                    handleSubmit={this.handleSubmitDeleteAttendanceLogOverrideModal}/>
            </div>
        );
    }
}