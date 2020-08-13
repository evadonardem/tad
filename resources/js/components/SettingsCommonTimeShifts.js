import React, { Component } from 'react';
import { Jumbotron, Button, Card } from 'react-bootstrap';
import cookie from 'react-cookies';
import AddCommonTimeShiftModal from './AddCommonTimeShiftModal';
import CommonDeleteModal from './CommonDeleteModal';

export default class SettingsCommonTimeShifts extends Component {
    constructor(props) {
        super(props);
        this.handleAddCommonTimeShift = this.handleAddCommonTimeShift.bind(this);
        this.handleCloseAddCommonTimeShiftModal = this.handleCloseAddCommonTimeShiftModal.bind(this);
        this.handleSubmitAddCommonTimeShiftModal = this.handleSubmitAddCommonTimeShiftModal.bind(this);
        this.handleCloseDeleteCommonTimeShiftModal =  this.handleCloseDeleteCommonTimeShiftModal.bind(this);
        this.handleSubmitDeleteCommonTimeShiftModal = this.handleSubmitDeleteCommonTimeShiftModal.bind(this);

        this.state = {
            showAddCommonTimeShiftModal: false,
            isErrorAddCommonTimeShift: false,
            errorHeaderTitleAddCommonTimeShift: '',
            errorBodyTextAddCommonTimeShift: '',
        };

        this.state = {
            showDeleteCommonTimeShiftModal: false,
            commonTimeShiftId: null,
            commonTimeShiftRoleId: null,
            commonTimeShiftEffectivityDate: null,
        }
    }

    componentDidMount() {
        const token = cookie.load('token');
        const self = this;

        const exportButtons = window.exportButtonsBase;
        const exportFilename = 'Common-Time-Shifts';
        const exportTitle = 'Common Time Shifts';
        exportButtons[0].filename = exportFilename;
        exportButtons[1].filename = exportFilename;
        exportButtons[1].title = exportTitle;

        $(this.refs.commonTimeShiftsList).DataTable({
            ajax: apiBaseUrl + '/settings/common-time-shifts?token=' + token,
            ordering: false,
            buttons: exportButtons,
            columns: [
                { data: 'role_id' },
                { data: 'effectivity_date' },
                { data: 'expected_time_in' },
                { data: 'expected_time_out' },
                {
                    data: null,
                    render: function (data, type, row) {
                        const deleteBtn = (row.effectivity_date && !row.is_locked)
                            ? '<a href="#" class="delete btn btn-warning" data-common-time-shift-id="' + row.id + '" data-role-id="' + row.role_id + '" data-effectivity-date="' + row.effectivity_date + '"><i class="fa fa-trash"></i></a>'
                            : null;

                        return deleteBtn;
                    }
                },
            ]
        });

        $(document).on('click', '.data-table-wrapper .delete', function(e) {
            e.preventDefault();

            const commonTimeShiftId = e.currentTarget.getAttribute('data-common-time-shift-id');
            const commonTimeShiftRoleId = e.currentTarget.getAttribute('data-role-id');
            const commonTimeShiftEffectivityDate = e.currentTarget.getAttribute('data-effectivity-date');
            self.setState({
                showDeleteCommonTimeShiftModal: true,
                commonTimeShiftId,
                commonTimeShiftRoleId,
                commonTimeShiftEffectivityDate,
            });``
        });
    }

    componentWillUnmount() {
        $('.data-table-wrapper')
            .find('table')
            .DataTable()
            .destroy(true);

        $('.data-table-wrapper .delete').off();
    }

    handleAddCommonTimeShift(e) {
        const self = this;
        self.setState({ showAddCommonTimeShiftModal: true });
    }

    handleCloseAddCommonTimeShiftModal(e) {
        const self = this;
        self.setState({
            showAddCommonTimeShiftModal: false,
            isErrorAddCommonTimeShift: false,
            isDeleteCommonTimeShiftError: false,
            deleteCommonTimeShiftErrorHeaderTitle: '',
            deleteCommonTimeShiftErrorBodyText: '',
        });
    }

    handleSubmitAddCommonTimeShiftModal(e) {
        e.preventDefault();

        const self = this;
        const token = cookie.load('token');

        const table = $('.data-table-wrapper').find('table').DataTable();
        const form = e.currentTarget;
        const data = $(form).serialize();
        const modal = $('#addCommonTimeShiftModal');
        const actionEndPoint = apiBaseUrl + '/settings/common-time-shifts?token=' + token;

        axios.post(actionEndPoint, data)
            .then((response) => {
                table.ajax.reload(null, false);
                self.setState({
                    showAddCommonTimeShiftModal: false,
                    isErrorAddCommonTimeShift: false,
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

    handleCloseDeleteCommonTimeShiftModal() {
        const self = this;
        self.setState({ showDeleteCommonTimeShiftModal: false });
    }

    handleSubmitDeleteCommonTimeShiftModal() {
        const self = this;
        const token = cookie.load('token');
        const {
            commonTimeShiftId,
            commonTimeShiftRoleId,
            commonTimeShiftEffectivityDate,
        } = self.state;
        const table = $('.data-table-wrapper').find('table').DataTable();

        axios.delete(apiBaseUrl + '/settings/common-time-shifts/' + commonTimeShiftId + '?token=' + token)
            .then((response) => {
                table.ajax.reload(null, false);
                self.setState({
                    showDeleteCommonTimeShiftModal: false,
                });
            })
            .catch((error) => {
                self.setState({
                    isDeleteCommonTimeShiftError: true,
                    deleteCommonTimeShiftErrorHeaderTitle: 'Oh snap! Common time shift cannot be deleted!',
                    deleteCommonTimeShiftErrorBodyText: `Common time shift for ${commonTimeShiftRoleId} effective on ${commonTimeShiftEffectivityDate} is in effect.`,
                });
            });
    }

    render() {
        const {
            showAddCommonTimeShiftModal,
            isErrorAddCommonTimeShift,
            errorHeaderTitleAddCommonTimeShift,
            errorBodyTextAddCommonTimeShift
        } = this.state;

        const {
            showDeleteCommonTimeShiftModal,
            commonTimeShiftRoleId,
            commonTimeShiftEffectivityDate,
            isDeleteCommonTimeShiftError,
            deleteCommonTimeShiftErrorHeaderTitle,
            deleteCommonTimeShiftErrorBodyText,
        } = this.state;

        return (
            <div className="container-fluid my-4">
                <h1><i className="fa fa-clock-o"></i> Common Time Shifts</h1>

                <hr className="my-4"/>

                <div className="row">
                    <div className="col-md-12 pull-right">
                        <Button variant='primary' onClick={this.handleAddCommonTimeShift}>
                            <i className="fa fa-plus"></i> Add Common Time Shift
                        </Button>
                    </div>
                </div>

                <hr className="my-4"/>

                <div className="row">
                    <div className="col-md-12">
                        <Card>
                            <Card.Body>
                                <table ref="commonTimeShiftsList" className="table table-striped" style={{width: 100+'%'}}>
                                    <thead>
                                        <tr>
                                            <th scope="col">Role</th>
                                            <th scope="col">Effectivity Date</th>
                                            <th scope="col">Expected Time-in</th>
                                            <th scope="col">Expected Time-out</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </Card.Body>
                        </Card>
                    </div>
                </div>

                <AddCommonTimeShiftModal
                    isShow={showAddCommonTimeShiftModal}
                    handleClose={this.handleCloseAddCommonTimeShiftModal}
                    handleSubmit={this.handleSubmitAddCommonTimeShiftModal}
                    isError={isErrorAddCommonTimeShift}
                    errorHeaderTitle={errorHeaderTitleAddCommonTimeShift}
                    errorBodyText={errorBodyTextAddCommonTimeShift}/>

                <CommonDeleteModal
                    isShow={showDeleteCommonTimeShiftModal}
                    headerTitle="Delete Common Time Shift"
                    bodyText={`Are you sure to delete common time shift for ${commonTimeShiftRoleId} effectivity date on ${commonTimeShiftEffectivityDate}?`}
                    handleClose={this.handleCloseDeleteCommonTimeShiftModal}
                    handleSubmit={this.handleSubmitDeleteCommonTimeShiftModal}
                    isDeleteError={isDeleteCommonTimeShiftError}
                    deleteErrorHeaderTitle={deleteCommonTimeShiftErrorHeaderTitle}
                    deleteErrorBodyText={deleteCommonTimeShiftErrorBodyText}/>

            </div>
        );
    }
}
