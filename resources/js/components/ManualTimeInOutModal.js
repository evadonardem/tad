import React, { Component } from 'react';
import { Button, Modal, Form } from 'react-bootstrap';

export default class ManualTimeInOutModal extends Component {
    constructor(props) {
        super(props);
        this.handleClose = this.handleClose.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleClose(e) {
        const { handleClose } = this.props;
        handleClose(e);
    }

    handleSubmit(e) {
        const { handleSubmit } = this.props;
        handleSubmit(e);
    }

    render() {
        const {
            isShow,
            biometricId,
            name,
            logDate,
        } = this.props;
        
        return (
            <Modal
                id="manualTimeInOutModal"
                show={isShow}
                onHide={this.handleClose}
                centered
                backdrop='static'
                keyboard={false}>
                <Form onSubmit={this.handleSubmit}>
                    <Modal.Header closeButton>
                        <Modal.Title>Adjustment Late/Under Time</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <Form.Group>
                            <Form.Label>User:</Form.Label>
                            <Form.Control
                                type="text"
                                defaultValue={`${biometricId} ${name}`}
                                readOnly></Form.Control>
                        </Form.Group>
                        <Form.Group>
                            <Form.Label>Date:</Form.Label>
                            <Form.Control
                                type="date"
                                name="log_date"
                                defaultValue={logDate}
                                readOnly></Form.Control>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                        <Form.Group>
                            <Form.Label>Time-In:</Form.Label>
                            <Form.Control
                                type="time"
                                name="time_in"
                                defaultValue=""></Form.Control>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                        <Form.Group>
                            <Form.Label>Time-Out:</Form.Label>
                            <Form.Control
                                type="time"
                                name="time_out"
                                defaultValue=""></Form.Control>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                        <Form.Group>
                            <Form.Label>Reason:</Form.Label>
                            <Form.Control
                                as="textarea"
                                name="reason"
                                defaultValue=""></Form.Control>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                    </Modal.Body>
                    <Modal.Footer>
                        <Button variant="primary" type="submit">
                            Save
                        </Button>
                        <Button variant="secondary" onClick={this.handleClose}>
                            Cancel
                        </Button>
                    </Modal.Footer>
                </Form>
            </Modal>
        );
    }
}
