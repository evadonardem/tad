import React, { Component } from 'react';
import cookie from 'react-cookies';
import { v4 as uuidv4 } from 'uuid';
import { Button, ButtonGroup, Modal, Form } from 'react-bootstrap';
import CommonDropdownSelectSingleRoles from './CommonDropdownSelectSingleRoles';
import CommonDropdownSelectSingleUsers from './CommonDropdownSelectSingleUsers';

export default class AttendanceLogOverridesAddEditModal extends Component {
    constructor(props) {
        super(props);
        this.handleClose = this.handleClose.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleChangeText = this.handleChangeText.bind(this);
        this.handleChangeRole = this.handleChangeRole.bind(this);
        this.handleChangeOverrideExpected = this.handleChangeOverrideExpected.bind(this);
        this.handleChangeOverrideExpectedType = this.handleChangeOverrideExpectedType.bind(this);
        this.handleChangeOverrideLog = this.handleChangeOverrideLog.bind(this);
        this.handleChangeOverrideLogType = this.handleChangeOverrideLogType.bind(this);
        this.handleChangeExceptUser = this.handleChangeExceptUser.bind(this);
        this.state = {
            overrideDate: '',
            role: null,
            isOverrideExpected: false,
            overrideExpectedType: 'time_in_and_out',
            overrideExpectedTimeIn: '',
            overrideExpectedTimeOut: '',
            isOverrideLog: false,
            overrideLogType: 'time_in_and_out',
            overrideLogTimeIn: '',
            overrideLogTimeOut: '',
            overrideLogExceptUsers: null,
            overrideReason: '',
            errors: {},
        };
    }

    handleClose(e) {
        const self = this;
        const { handleClose } = self.props;
        self.setState({           
            overrideDate: '',
            role: null,
            isOverrideExpected: false,
            overrideExpectedType: 'time_in_and_out',
            overrideExpectedTimeIn: '',
            overrideExpectedTimeOut: '',
            isOverrideLog: false,
            overrideLogType: 'time_in_and_out',
            overrideLogTimeIn: '',
            overrideLogTimeOut: '',
            overrideLogExceptUsers: null,
            overrideReason: '',
            errors: {},
        });
        handleClose(e);
    }

    handleSubmit(e) {
        e.preventDefault();
        const self = this;
        const token = cookie.load('token');

        const { handleSubmit } = self.props;
        const {
            overrideDate,
            role,
            isOverrideExpected,
            overrideExpectedType,
            overrideExpectedTimeIn,
            overrideExpectedTimeOut,
            isOverrideLog,
            overrideLogType,
            overrideLogTimeIn,
            overrideLogTimeOut,
            overrideLogExceptUsers,
            overrideReason,
        } = self.state;

        let exceptUsers = [];
        if (overrideLogExceptUsers) {
            exceptUsers = overrideLogExceptUsers.map((item) => item.value);
        }

        axios
            .post(`${apiBaseUrl}/override/attendance-logs?token=${token}`, {
                override_date: overrideDate,
                role: role ? role.value : null,
                do_override_expected: isOverrideExpected,
                override_expected: overrideExpectedType,
                override_expected_time_in: overrideExpectedTimeIn,
                override_expected_time_out: overrideExpectedTimeOut,
                do_override_log: isOverrideLog,
                override_log: overrideLogType,
                override_log_time_in: overrideLogTimeIn,
                override_log_time_out: overrideLogTimeOut,
                override_log_except_users: exceptUsers,
                override_reason: overrideReason,
            })
            .then((response) => {
                self.setState({
                    overrideDate: '',
                    role: null,
                    isOverrideExpected: false,
                    overrideExpectedType: 'time_in_and_out',
                    overrideExpectedTimeIn: '',
                    overrideExpectedTimeOut: '',
                    isOverrideLog: false,
                    overrideLogType: 'time_in_and_out',
                    overrideLogTimeIn: '',
                    overrideLogTimeOut: '',
                    overrideLogExceptUsers: null,
                    overrideReason: '',
                    errors: {},
                });
                handleSubmit(e);
            })
            .catch((error) => {
                if (error.response.status === 422) {
                    const { errors } = error.response.data;
                    self.setState({ errors });
                }
            });
    }

    handleChangeText(e) {
        const self = this;
        const name = e.currentTarget.getAttribute('name');
        const value = e.currentTarget.value;

        if (name === 'override_date') {
            self.setState({ overrideDate: value });
        } else if (name === 'override_expected_time_in') {
            self.setState({ overrideExpectedTimeIn: value });
        } else if (name === 'override_expected_time_out') {
            self.setState({ overrideExpectedTimeOut: value });
        } else if (name === 'override_log_time_in') {
            self.setState({ overrideLogTimeIn: value });
        } else if (name === 'override_log_time_out') {
            self.setState({ overrideLogTimeOut: value });
        } else if (name === 'override_reason') {
            self.setState({ overrideReason: value });
        }
    }

    handleChangeRole(e) {
        const self = this;
        self.setState({ role: e });
    }

    handleChangeOverrideExpected(e) {
        const self = this;
        const isOverrideExpected = $(e.currentTarget).is(':checked');
        self.setState({ isOverrideExpected });
    }

    handleChangeOverrideExpectedType(e) {
        const self = this;
        const overrideExpectedType = $(e.currentTarget).val();
        self.setState({
            overrideExpectedType,
        });
    }

    handleChangeOverrideLog(e) {
        const self = this;
        const isOverrideLog = $(e.currentTarget).is(':checked');
        self.setState({ isOverrideLog });
    }

    handleChangeOverrideLogType(e) {
        const self = this;
        const overrideLogType = $(e.currentTarget).val();
        self.setState({
            overrideLogType,
        });
    }

    handleChangeExceptUser(e) {
        const self = this;
        self.setState({ overrideLogExceptUsers: e });
    }

    render() {
        const {
            isShow,
            isEdit,
        } = this.props;

        const {
            role,
            isOverrideExpected,
            overrideExpectedType,
            isOverrideLog,
            overrideLogType,
            overrideLogExceptUsers,
            errors,
        } = this.state;

        let userFilters = {};
        if (role) {
            userFilters.role_id = role.value;
        } 

        return (
            <Modal
                id="attendanceLogOverridesAddEditModal"
                show={isShow}
                onHide={this.handleClose}
                centered
                backdrop='static'
                keyboard={false}>
                <Form onSubmit={this.handleSubmit}>
                    <Modal.Header closeButton>
                        <Modal.Title>{`${!isEdit ? 'Add' : 'Edit'} Override`}</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <Form.Group>
                            <Form.Label>Date:</Form.Label>
                            <Form.Control
                                type="date"
                                name="override_date"
                                defaultValue=""
                                isInvalid={errors && errors.override_date}
                                onChange={this.handleChangeText}></Form.Control>
                            <Form.Control.Feedback type="invalid">
                                { errors && errors.override_date && errors.override_date[0] }
                            </Form.Control.Feedback>
                        </Form.Group>
                        <CommonDropdownSelectSingleRoles
                            name="role"
                            selectedRole={role}
                            errorMessage={ errors && errors.role && errors.role[0] }
                            handleChange={this.handleChangeRole}/>
                        <Form.Group controlId="overrideExpected">
                            <Form.Check
                                type="checkbox"
                                label="Override Expected"
                                name="do_override_expected"
                                onChange={this.handleChangeOverrideExpected}></Form.Check>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                        {
                            isOverrideExpected &&
                            <div>
                                <Form.Group>
                                    <Form.Check
                                        type="radio"
                                        inline
                                        id="override_expected_time_in_and_out"
                                        label="Time-in and out"
                                        name="override_expected"
                                        value="time_in_and_out"
                                        checked={overrideExpectedType==='time_in_and_out'}
                                        onChange={this.handleChangeOverrideExpectedType}></Form.Check>
                                    <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                                    <Form.Check
                                        type="radio"
                                        inline
                                        id="override_expected_time_in_only"
                                        label="Time-in only"
                                        name="override_expected"
                                        value="time_in_only"
                                        checked={overrideExpectedType==='time_in_only'}
                                        onChange={this.handleChangeOverrideExpectedType}></Form.Check>
                                    <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                                    <Form.Check
                                        type="radio"
                                        inline
                                        id="override_expected_time_out_only"
                                        label="Time-out only"
                                        name="override_expected"
                                        value="time_out_only"
                                        checked={overrideExpectedType==='time_out_only'}
                                        onChange={this.handleChangeOverrideExpectedType}></Form.Check>
                                    <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                                </Form.Group>
                            </div>
                        }
                        {
                            isOverrideExpected && 
                            (overrideExpectedType === 'time_in_and_out' || overrideExpectedType === 'time_in_only') &&
                            <Form.Group>
                                <Form.Label>Expected Time-in:</Form.Label>
                                <Form.Control
                                    type="time"
                                    name="override_expected_time_in"
                                    defaultValue=""
                                    isInvalid={errors && errors.override_expected_time_in}
                                    onChange={this.handleChangeText}></Form.Control>
                                <Form.Control.Feedback type="invalid">
                                    {
                                        errors &&
                                        errors.override_expected_time_in &&
                                        errors.override_expected_time_in[0]
                                    }
                                </Form.Control.Feedback>
                            </Form.Group>
                        }
                        {
                            isOverrideExpected &&
                            (overrideExpectedType === 'time_in_and_out' || overrideExpectedType === 'time_out_only') &&
                            <Form.Group>
                                <Form.Label>Expected Time-out:</Form.Label>
                                <Form.Control
                                    type="time"
                                    name="override_expected_time_out"
                                    defaultValue=""
                                    isInvalid={errors && errors.override_expected_time_out}
                                    onChange={this.handleChangeText}></Form.Control>
                                <Form.Control.Feedback type="invalid">
                                    {
                                        errors &&
                                        errors.override_expected_time_out &&
                                        errors.override_expected_time_out[0]
                                    }
                                </Form.Control.Feedback>
                            </Form.Group>
                        }
                        
                        <Form.Group controlId="overrideLog">
                            <Form.Check
                                type="checkbox"
                                label="Override Log"
                                name="do_override_log"
                                onChange={this.handleChangeOverrideLog}></Form.Check>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                        {
                            isOverrideLog &&
                            <div>
                                <Form.Group>
                                    <Form.Check
                                        type="radio"
                                        inline
                                        id="override_log_time_in_and_out"
                                        label="Time-in and out"
                                        name="override_log"
                                        value="time_in_and_out"
                                        checked={overrideLogType==='time_in_and_out'}
                                        onChange={this.handleChangeOverrideLogType}></Form.Check>
                                    <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                                    <Form.Check
                                        type="radio"
                                        inline
                                        id="override_log_time_in_only"
                                        label="Time-in only"
                                        name="override_log"
                                        value="time_in_only"
                                        checked={overrideLogType==='time_in_only'}
                                        onChange={this.handleChangeOverrideLogType}></Form.Check>
                                    <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                                    <Form.Check
                                        type="radio"
                                        inline
                                        id="override_log_time_out_only"
                                        label="Time-out only"
                                        name="override_log"
                                        value="time_out_only"
                                        checked={overrideLogType==='time_out_only'}
                                        onChange={this.handleChangeOverrideLogType}></Form.Check>
                                    <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                                </Form.Group>
                            </div>
                        }
                        {
                            isOverrideLog && 
                            (overrideLogType === 'time_in_and_out' || overrideLogType === 'time_in_only') &&
                            <Form.Group>
                                <Form.Label>Log Time-in:</Form.Label>
                                <Form.Control
                                    type="time"
                                    name="override_log_time_in"
                                    defaultValue=""
                                    isInvalid={errors && errors.override_log_time_in}
                                    onChange={this.handleChangeText}></Form.Control>
                                <Form.Control.Feedback type="invalid">
                                    {
                                        errors &&
                                        errors.override_log_time_in &&
                                        errors.override_log_time_in[0]
                                    }
                                </Form.Control.Feedback>
                            </Form.Group>
                        }
                        {
                            isOverrideLog &&
                            (overrideLogType === 'time_in_and_out' || overrideLogType === 'time_out_only') &&
                            <Form.Group>
                                <Form.Label>Log Time-out:</Form.Label>
                                <Form.Control
                                    type="time"
                                    name="override_log_time_out"
                                    defaultValue=""
                                    isInvalid={errors && errors.override_log_time_out}
                                    onChange={this.handleChangeText}></Form.Control>
                                <Form.Control.Feedback type="invalid">
                                    {
                                        errors &&
                                        errors.override_log_time_out &&
                                        errors.override_log_time_out[0]
                                    }
                                </Form.Control.Feedback>
                            </Form.Group>
                        }
                        {
                            isOverrideLog && role &&
                            <CommonDropdownSelectSingleUsers
                                key={uuidv4()}
                                label="Except users:"
                                name="override_log_except_users[]"
                                selectedUser={overrideLogExceptUsers}
                                isMulti
                                filters={userFilters}
                                handleChange={this.handleChangeExceptUser}/>
                        }

                        <Form.Group>
                            <Form.Label>Reason:</Form.Label>
                            <Form.Control
                                as="textarea"
                                name="override_reason"
                                defaultValue=""
                                isInvalid={ errors && errors.override_reason }
                                onChange={this.handleChangeText}></Form.Control>
                            <Form.Control.Feedback type="invalid">
                                { errors && errors.override_reason && errors.override_reason[0] }
                            </Form.Control.Feedback>
                        </Form.Group>
                    </Modal.Body>
                     <Modal.Footer>
                        <ButtonGroup>
                            <Button variant="primary" type="submit">
                                { !isEdit ? 'Save' : 'Update' }
                            </Button>
                            <Button variant="secondary" onClick={this.handleClose}>
                                Cancel
                            </Button>
                        </ButtonGroup>                        
                    </Modal.Footer>
                </Form>
            </Modal>
        );
    }
}
