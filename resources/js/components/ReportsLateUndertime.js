import React, { Component } from 'react';
import { Button } from 'react-bootstrap';
import CommonSearchFilters from './CommonSearchFilters';
import ReportsLateUndertimeSearchResult from './ReportsLateUndertimeSearchResult';

export default class ReportsLateUndertime extends Component {
    constructor(props) {
        super(props);
        this.handleSearchSubmit = this.handleSearchSubmit.bind(this);
        this.handleSearchResultErrors = this.handleSearchResultErrors.bind(this);

        this.state = {
            roleId: '',
            biometricId: '',
            biometricName: '',
            startDate: '',
            endDate: '',
            searchErrors: {},
        };
    }

    handleSearchSubmit(e) {
        e.preventDefault();
        const roleId = $(e.currentTarget).find('[name="role_id"]').val();
        const biometricId = $(e.currentTarget).find('[name="biometric_id"]').val();
        const biometricName = $(e.currentTarget).find('[name="biometric_name"]').val();
        const startDate = $(e.currentTarget).find('[name="start_date"]').val();
        const endDate = $(e.currentTarget).find('[name="end_date"]').val();
        this.setState({ roleId, biometricId, biometricName, startDate, endDate, searchErrors: {} });
    }

    handleSearchResultErrors(searchErrors) {
        this.setState({ startDate: '', endDate: '', searchErrors });
    }

    render() {
        const {
            roleId,
            biometricId,
            biometricName,
            startDate,
            endDate,
            searchErrors,
        } = this.state;

        return (
            <div className="container-fluid my-4">
                <h1><i className="fa fa-calendar"></i> Late / Under Time Report</h1>

                <hr className="my-4"/>

                <div className="row">
                    <div className="col-md-3">
                        <CommonSearchFilters
                            showRolesFilter
                            handleSubmit={this.handleSearchSubmit}
                            searchErrors={searchErrors}/>
                    </div>
                    <div className="col-md-9">
                        <ReportsLateUndertimeSearchResult
                            roleId={roleId}
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
