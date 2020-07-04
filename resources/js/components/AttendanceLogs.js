import React, { Component } from 'react';
import { Button } from 'react-bootstrap';
import CommonSearchFilters from './CommonSearchFilters';
import AttendanceLogsSearchResult from './AttendanceLogsSearchResult';

export default class AttendanceLogs extends Component {
    constructor(props) {
        super(props);
        this.handleSearchSubmit = this.handleSearchSubmit.bind(this);
        this.handleSearchResultErrors = this.handleSearchResultErrors.bind(this);

        this.state = {
            biometricId: '',
            biometricName: '',
            startDate: '',
            endDate: '',
            searchErrors: {},
        };
    }

    handleSearchSubmit(e) {
        e.preventDefault();
        const biometricId = $(e.currentTarget).find('[name="biometric_id"]').val();
        const biometricName = $(e.currentTarget).find('[name="biometric_name"]').val();
        const startDate = $(e.currentTarget).find('[name="start_date"]').val();
        const endDate = $(e.currentTarget).find('[name="end_date"]').val();
        this.setState({ biometricId, biometricName, startDate, endDate });
    }

    handleSearchResultErrors(searchErrors) {
        this.setState({ startDate: '', endDate: '', searchErrors });
    }

    render() {
        const {
            biometricId,
            biometricName,
            startDate,
            endDate,
            searchErrors,
        } = this.state;

        return (
            <div className="container-fluid my-4">
                <h1><i className="fa fa-calendar"></i> Attendance Logs</h1>

                <hr className="my-4"/>

                <div className="row">
                    <div className="col-md-3">
                        <CommonSearchFilters
                            handleSubmit={this.handleSearchSubmit}
                            searchErrors={searchErrors}/>
                    </div>
                    <div className="col-md-9">
                        <AttendanceLogsSearchResult
                            biometricId={biometricId}
                            biometricName={biometricName}
                            startDate={startDate}
                            endDate={endDate}
                            handleSearchResultErrors={this.handleSearchResultErrors}/>
                    </div>
                </div>
            </div>
        );
    }
}
