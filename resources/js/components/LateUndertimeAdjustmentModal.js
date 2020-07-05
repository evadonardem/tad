import React, { Component } from 'react';
import { Alert, Button, Modal, Form } from 'react-bootstrap';

export default class LateUndertimeAdjustmentModal extends Component {
    constructor(props) {
        super(props);
        this.handleClose = this.handleClose.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleChangeAdjustment = this.handleChangeAdjustment.bind(this);
        this.state = {
            updatedTotalLateUndertime: '',
        }
    }

    handleClose(e) {
        const { handleClose } = this.props;
        this.setState({ updatedTotalLateUndertime: '' });
        handleClose(e);
    }

    handleSubmit(e) {
        const { handleSubmit } = this.props;
        this.setState({ updatedTotalLateUndertime: '' });
        handleSubmit(e);
    }

    handleChangeAdjustment(e) {
        const { late, undertime } = this.props;
        const lateInSeconds = TADHelper.timeToSeconds(late);
        const undertimeInSeconds = TADHelper.timeToSeconds(undertime);
        const adjustmentInSeconds = TADHelper.timeToSeconds($(e.currentTarget).val());
        const totalLateUndertimeInSeconds = +lateInSeconds
            + +undertimeInSeconds
            - +adjustmentInSeconds;
        this.setState({
            updatedTotalLateUndertime: TADHelper.formatTimeDisplay(totalLateUndertimeInSeconds),
        });
    }

    render() {
        const {
            isShow,
            logDate,
            late,
            undertime,
            totalLateUndertime,
        } = this.props;
        const { updatedTotalLateUndertime } = this.state;

        return (
            <Modal
                id="adjustmentLateUndertimeModal"
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
                            <Form.Label>Date:</Form.Label>
                            <Form.Control
                                type="date"
                                name="log_date"
                                defaultValue={logDate}
                                readOnly></Form.Control>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                        <Form.Group>
                            <Form.Label>Late (HH:MM:SS):</Form.Label>
                            <Form.Control
                                type="text"
                                name="late"
                                defaultValue={late}
                                readOnly></Form.Control>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                        <Form.Group>
                            <Form.Label>Under Time (HH:MM:SS):</Form.Label>
                            <Form.Control
                                type="text"
                                name="undertime"
                                defaultValue={undertime}
                                readOnly></Form.Control>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                        <Form.Group>
                            <Form.Label>Adjustment (HH:MM:SS):</Form.Label>
                            <Form.Control
                                type="text"
                                name="adjustment"
                                onChange={this.handleChangeAdjustment}></Form.Control>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                        <Form.Group>
                            <Form.Label>Total (HH:MM:SS):</Form.Label>
                            <Form.Control
                                type="text"
                                name="total_late_undertime"
                                defaultValue={
                                    updatedTotalLateUndertime
                                        ? updatedTotalLateUndertime
                                        : totalLateUndertime
                                    }
                                readOnly></Form.Control>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                        <Form.Group>
                            <Form.Label>Reason:</Form.Label>
                            <Form.Control
                                as="textarea"
                                name="reason"></Form.Control>
                            <Form.Control.Feedback type="invalid"></Form.Control.Feedback>
                        </Form.Group>
                    </Modal.Body>
                    <Modal.Footer>
                        <Button variant="secondary" onClick={this.handleClose}>
                            Cancel
                        </Button>
                        <Button variant="primary" type="submit">
                            Save
                        </Button>
                    </Modal.Footer>
                </Form>
            </Modal>
        );
    }
}
