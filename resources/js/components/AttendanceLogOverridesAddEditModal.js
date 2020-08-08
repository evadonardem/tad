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
            overrideDate: null,
            role: null,
            isOverrideExpected: null,
            overrideExpectedType: null,
            overrideExpectedTimeIn: null,
            overrideExpectedTimeOut: null,
            isOverrideLog: false,
            overrideLogType: null,
            overrideLogTimeIn: null,
            overrideLogTimeOut: null,
            overrideLogExceptUsers: null,
            overrideReason: null,
            errors: {},
        };
    }

    handleClose(e) {
        const self = this;
        const { handleClose } = self.props;
        self.setState({           
            overrideDate: null,
            role: null,
            isOverrideExpected: null,
            overrideExpectedType: null,
            overrideExpectedTimeIn: null,
            overrideExpectedTimeOut: null,
            isOverrideLog: null,
            overrideLogType: null,
            overrideLogTimeIn: null,
            overrideLogTimeOut: null,
            overrideLogExceptUsers: null,
            overrideReason: null,
            errors: {},
        });
        handleClose(e);
    }

    handleSubmit(e) {
        e.preventDefault();
        const self = this;
        const token = cookie.load('token');

        const { overrideId, isEdit, handleSubmit } = self.props;
        
        let params = null;

        if (isEdit) {
            const isOverrideExpected = document.querySelector('[name="do_override_expected"]:checked') ? true : false;
            const overrideExpectedType = (isOverrideExpected) ? document.querySelector('[name="override_expected"]:checked').value : null;
            let overrideExpectedTimeIn = null;
            let overrideExpectedTimeOut = null;
            if (overrideExpectedType === 'time_in_and_out') {
                overrideExpectedTimeIn = document.querySelector('[name="override_expected_time_in"]').value;
                overrideExpectedTimeOut = document.querySelector('[name="override_expected_time_out"]').value;
            } else if (overrideExpectedType === 'time_in_only') {
                overrideExpectedTimeIn = document.querySelector('[name="override_expected_time_in"]').value;
            } else if (overrideExpectedType === 'time_out_only') {
                overrideExpectedTimeOut = document.querySelector('[name="override_expected_time_out"]').value;
            }

            const isOverrideLog = document.querySelector('[name="do_override_log"]:checked') ? true : false;
            const overrideLogType = (isOverrideLog) ? document.querySelector('[name="override_log"]:checked').value : null;
            let overrideLogTimeIn = null;
            let overrideLogTimeOut = null;
            if (overrideLogType === 'time_in_and_out') {
                overrideLogTimeIn = document.querySelector('[name="override_log_time_in"]').value;
                overrideLogTimeOut = document.querySelector('[name="override_log_time_out"]').value;
            } else if (overrideLogType === 'time_in_only') {
                overrideLogTimeIn = document.querySelector('[name="override_log_time_in"]').value;
            } else if (overrideLogType === 'time_out_only') {
                overrideLogTimeOut = document.querySelector('[name="override_log_time_out"]').value;
            }

            const overrideReason = document.querySelector('[name="override_reason"]').value;

            params = {
                do_override_expected: isOverrideExpected,
                override_expected: overrideExpectedType,
                override_expected_time_in: overrideExpectedTimeIn,
                override_expected_time_out: overrideExpectedTimeOut,
                do_override_log: isOverrideLog,
                override_log: overrideLogType,
                override_log_time_in: overrideLogTimeIn,
                override_log_time_out: overrideLogTimeOut,
                override_reason: overrideReason,
            };
        } else {
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

            params = {
                override_date: overrideDate,
                role: role ? role.value : null,
                do_override_expected: isOverrideExpected,
                override_expected: overrideExpectedType ?? 'time_in_and_out',
                override_expected_time_in: overrideExpectedTimeIn,
                override_expected_time_out: overrideExpectedTimeOut,
                do_override_log: isOverrideLog,
                override_log: overrideLogType ?? 'time_in_and_out',
                override_log_time_in: overrideLogTimeIn,
                override_log_time_out: overrideLogTimeOut,
                override_log_except_users: exceptUsers,
                override_reason: overrideReason,
            };
        }

        axios[isEdit ? 'patch' : 'post'](
                `${apiBaseUrl}/override/attendance-logs${(isEdit ? `/${overrideId}` : '')}?token=${token}`,
                params
            ).then((response) => {
                self.setState({
                    overrideDate: null,
                    role: null,
                    isOverrideExpected: null,
                    overrideExpectedType: null,
                    overrideExpectedTimeIn: null,
                    overrideExpectedTimeOut: null,
                    isOverrideLog: null,
                    overrideLogType: null,
                    overrideLogTimeIn: null,
                    overrideLogTimeOut: null,
                    overrideLogExceptUsers: null,
                    overrideReason: null,
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
            self.setState({ isOverrideExpected: true, overrideExpectedTimeIn: value });
        } else if (name === 'override_expected_time_out') {
            self.setState({ isOverrideExpected: true, overrideExpectedTimeOut: value });
        } else if (name === 'override_log_time_in') {
            self.setState({ isOverrideLog: true, overrideLogTimeIn: value });
        } else if (name === 'override_log_time_out') {
            self.setState({ isOverrideLog: true, overrideLogTimeOut: value });
        } else if (name === 'override_reason') {
            self.setState({ overrideReason: value });
        }
    }

    handleChangeRole(e) {
        const self = this;
        self.setState({ role: e, overrideLogExceptUsers: null });
    }

    handleChangeOverrideExpected(e) {
        const self = this;
        const isOverrideExpected = $(e.currentTarget).is(':checked');
        self.setState({
            isOverrideExpected,
        });
    }

    handleChangeOverrideExpectedType(e) {
        const self = this;
        const overrideExpectedType = $(e.currentTarget).val();
        self.setState({
            isOverrideExpected: true,
            overrideExpectedType,
        });
    }

    handleChangeOverrideLog(e) {
        const self = this;
        const isOverrideLog = $(e.currentTarget).is(':checked');
        self.setState({
            isOverrideLog,
        });
    }

    handleChangeOverrideLogType(e) {
        const self = this;
        const overrideLogType = $(e.currentTarget).val();
        self.setState({
            isOverrideLog: true,
            overrideLogType,
        });
    }

    handleChangeExceptUser(e) {
        const self = this;
        self.setState({
            overrideLogExceptUsers: e,
        });
    }

    render() {
        const {
            isShow,
            isEdit,
            overrideDate,
            overrideRole,
            overrideExpectedTimeIn,
            overrideExpectedTimeOut,
            overrideLogTimeIn,
            overrideLogTimeOut,
            overrideReason,
        } = this.props;

        const {
            role,
            isOverrideExpected: updatedIsOverrideExpected,
            overrideExpectedType: updatedOverrideExpectedType,
            isOverrideLog: updatedIsOverrideLog,
            overrideLogType: updatedOverrideLogType,
            overrideLogExceptUsers,
            errors,
        } = this.state;

        let userFilters = {};
        if (role) {
            userFilters.role_id = role.value;
        }

        const isOverrideExpected = updatedIsOverrideExpected ?? (overrideExpectedTimeIn || overrideExpectedTimeOut);
        
        let overrideExpectedType = null;
        if (overrideExpectedTimeIn && overrideExpectedTimeOut) {
            overrideExpectedType = 'time_in_and_out';
        } else if (overrideExpectedTimeIn) {
            overrideExpectedType = 'time_in_only';
        } else if (overrideExpectedTimeOut) {
            overrideExpectedType = 'time_out_only';
        } else {
            overrideExpectedType = 'time_in_and_out';
        }
        overrideExpectedType = updatedOverrideExpectedType ?? overrideExpectedType;

        const isOverrideLog = updatedIsOverrideLog ?? (overrideLogTimeIn || overrideLogTimeOut);
        
        let overrideLogType = null;
        if (overrideLogTimeIn && overrideLogTimeOut) {
            overrideLogType = 'time_in_and_out';
        } else if (overrideLogTimeIn) {
            overrideLogType = 'time_in_only';
        } else if (overrideLogTimeOut) {
            overrideLogType = 'time_out_only';
        } else {
            overrideLogType = 'time_in_and_out';
        }
        overrideLogType = updatedOverrideLogType ?? overrideLogType;

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
                                defaultValue={overrideDate}
                                isInvalid={errors && errors.override_date}
                                onChange={this.handleChangeText}
                                readOnly={isEdit}></Form.Control>
                            <Form.Control.Feedback type="invalid">
                                { errors && errors.override_date && errors.override_date[0] }
                            </Form.Control.Feedback>
                        </Form.Group>
                        <CommonDropdownSelectSingleRoles
                            name="role"
                            isDisabled={isEdit}
                            selectedRole={role || overrideRole}
                            errorMessage={ errors && errors.role && errors.role[0] }
                            handleChange={this.handleChangeRole}/>
                        <Form.Group controlId="overrideExpected">
                            <Form.Check
                                type="checkbox"
                                label="Override Expected"
                                name="do_override_expected"
                                defaultChecked={isOverrideExpected}
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
                                        checked={overrideExpectedType === 'time_in_and_out'}
                                        onChange={this.handleChangeOverrideExpectedType}></Form.Check>
                                    <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                                    <Form.Check
                                        type="radio"
                                        inline
                                        id="override_expected_time_in_only"
                                        label="Time-in only"
                                        name="override_expected"
                                        value="time_in_only"
                                        checked={overrideExpectedType === 'time_in_only'}
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
                                    defaultValue={overrideExpectedTimeIn}
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
                                    defaultValue={overrideExpectedTimeOut}
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
                                defaultChecked={isOverrideLog}
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
                                    defaultValue={overrideLogTimeIn}
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
                                    defaultValue={overrideLogTimeOut}
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
                            isOverrideLog && (role || overrideRole) &&
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
                                defaultValue={overrideReason}
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
