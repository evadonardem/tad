import React, { Component } from 'react';
import { Alert, Button, ButtonGroup, Modal } from 'react-bootstrap';

export default class CommonDeleteModal extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        const {
            isShow,
            headerTitle,
            bodyText,
            handleClose,
            handleSubmit,
            isDeleteError,
            deleteErrorHeaderTitle,
            deleteErrorBodyText
        } = this.props;

        return (
            <Modal
                show={isShow}
                onHide={handleClose}
                centered
                backdrop='static'
                keyboard={false}>
                {
                    !isDeleteError &&
                    <Modal.Header closeButton>
                        <Modal.Title>{headerTitle}</Modal.Title>
                    </Modal.Header>
                }                
                <Modal.Body>
                    { !isDeleteError && <p>{bodyText}</p> }
                    {
                        isDeleteError && 
                        <Alert variant="warning">
                            <Alert.Heading><i className="fa fa-warning"></i> {deleteErrorHeaderTitle}</Alert.Heading>
                            <p>{deleteErrorBodyText}</p>
                            <Button variant="warning" onClick={handleClose}>Close</Button>
                        </Alert>
                    }
                </Modal.Body>
                {
                    !isDeleteError &&
                    <Modal.Footer>
                        <ButtonGroup>
                            <Button variant="primary" onClick={handleSubmit}>
                                Yes
                            </Button>
                            <Button variant="secondary" onClick={handleClose}>
                                Cancel
                            </Button>
                        </ButtonGroup>
                    </Modal.Footer>
                }
            </Modal>
        );
    }
}