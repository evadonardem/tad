import React, { Component } from 'react';
import { Alert, Button, ButtonGroup, Modal, Form } from 'react-bootstrap';
import CommonDropdownSelectSingleRoles from './CommonDropdownSelectSingleRoles';

export default class AddCommonTimeShiftModal extends Component {
    constructor(props) {
        super(props);
        this.handleChangeRole = this.handleChangeRole.bind(this);

        this.state = {
            selectedRole: ''
        }
    }

    handleChangeRole(e) {
        this.setState({ selectedRole: e });
    }

    render() {
        const {
            isShow,
            handleClose,
            handleSubmit,
            isError,
            errorHeaderTitle,
            errorBodyText
        } = this.props;
        const { selectedRole } = this.state;

        return (
            <Modal
                id="addCommonTimeShiftModal"
                show={isShow}
                onHide={handleClose}
                centered
                backdrop='static'
                keyboard={false}>
                <Form onSubmit={handleSubmit}>
                    {
                        !isError &&
                        <Modal.Header closeButton>
                            <Modal.Title>Add Common Time Shift</Modal.Title>
                        </Modal.Header>
                    }
                    <Modal.Body>
                        {
                            !isError &&
                            <div>
                                <Form.Group>
                                    <Form.Label>Effectivity Date:</Form.Label>
                                    <Form.Control type="date" name="effectivity_date"></Form.Control>
                                    <div className="invalid-feedback"></div>
                                </Form.Group>
                                <Form.Group>
                                    <Form.Label>Expected Time-in:</Form.Label>
                                    <Form.Control type="time" name="expected_time_in"></Form.Control>
                                    <div className="invalid-feedback"></div>
                                </Form.Group>
                                <Form.Group>
                                    <Form.Label>Expected Time-out:</Form.Label>
                                    <Form.Control type="time" name="expected_time_out"></Form.Control>
                                    <div className="invalid-feedback"></div>
                                </Form.Group>
                                <CommonDropdownSelectSingleRoles name="role_id" selectedRole={selectedRole} handleChange={this.handleChangeRole}/>
                            </div>
                        }
                        {
                            isError &&
                            <Alert variant="warning">
                                <Alert.Heading><i className="fa fa-warning"></i> {errorHeaderTitle}</Alert.Heading>
                                <p>{errorBodyText}</p>
                                <Button variant="warning" onClick={handleClose}>Close</Button>
                            </Alert>
                        }
                    </Modal.Body>
                    {
                        !isError &&
                        <Modal.Footer>
                            <ButtonGroup>
                                <Button variant="primary" type="submit">
                                    Save
                                </Button>
                                <Button variant="secondary" onClick={handleClose}>
                                    Cancel
                                </Button>
                            </ButtonGroup>
                        </Modal.Footer>
                    }
                </Form>
            </Modal>
        );
    }
}
