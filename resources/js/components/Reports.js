import React, { Component } from 'react';
import { Jumbotron, Button } from 'react-bootstrap';
import { Link } from 'react-router-dom';

export default class Reports extends Component {
    render() {   
        return (
            <div className="container-fluid my-4">
                <h1><i className="fa fa-files-o"></i> Reports</h1>
                <hr className="my-4"/>
                <div className="row">
                    <div className="col-md-6">
                        <Jumbotron>
                            <h1 className="text-center">
                                <i className="fa fa-file-text"></i><br/>
                                Late &amp; Under Time<br/>
                                Report
                            </h1>
                            <hr className="my-4"/>
                            <p className="lead text-center">
                                <Link to={'reports-late-undertime'}>
                                    <Button variant="primary" size="lg">Continue &raquo;</Button>
                                </Link>
                            </p>
                        </Jumbotron>
                    </div>
                    <div className="col-md-6">
                        <Jumbotron>
                            <h1 className="text-center">
                                <i className="fa fa-file-text-o"></i><br/>
                                Absences<br/>
                                No Time-In/Out Report
                            </h1>
                            <hr className="my-4"/>
                            <p className="lead text-center">
                                <Link to={'reports-absences-no-time-in-out'}>
                                    <Button variant="primary" size="lg">Continue &raquo;</Button>
                                </Link>
                            </p>
                        </Jumbotron>
                    </div>
                </div>
            </div>
        );
    }
}