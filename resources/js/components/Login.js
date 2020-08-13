import React, { Component } from 'react';

export default class Login extends Component {
    constructor(props) {
        super(props);
        this.handleBiometricIdChange = this.handleBiometricIdChange.bind(this);
        this.handlePasswordChange = this.handlePasswordChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);

        this.state = {
            biometricId: null,
            password: null,
        };
    }

    handleBiometricIdChange(e) {
        this.setState({ biometricId: e.target.value });
    }

    handlePasswordChange(e) {
        this.setState({ password: e.target.value });
    }

    handleSubmit(e) {
        e.preventDefault();
        const { logIn } = this.props;
        logIn(this.state.biometricId, this.state.password);
    }

    render() {  
        return (
            <div className="row">
                <div className="col-md-4 offset-md-4 text-center">
                    <form onSubmit={this.handleSubmit}>
                        <h1 className="h3 mb-3 font-weight-normal">
                            <i className="fa fa-5x fa-user"></i><br/>
                            {appName}
                        </h1>
                        <p>Please sign in</p>
                        <div className="form-group">
                            <label htmlFor="inputBiometricId" className="sr-only">Biometric ID</label>
                            <input
                                type="text"
                                id="inputBiometricId"
                                className="form-control"
                                placeholder="Biometric ID"
                                autoFocus
                                onChange={this.handleBiometricIdChange}
                            />
                        </div>
                        <div className="form-group">
                            <label htmlFor="inputPassword" className="sr-only">Password</label>
                            <input
                                type="password"
                                id="inputPassword"
                                className="form-control"
                                placeholder="Password"
                                autoComplete="false"
                                onChange={this.handlePasswordChange}
                            />
                        </div>
                        <button className="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
                    </form>
                </div>
            </div>            
        );
    }
}