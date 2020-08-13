import React, { Component } from 'react';
import cookie from 'react-cookies';
import ReactDOM from 'react-dom';
import { HashRouter } from 'react-router-dom';
import Root from './Root';
import Login from './Login';

export default class App extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoggedIn: false,
            signedInUser: null
        };
        this.logIn = this.logIn.bind(this);
    }

    componentDidMount() {
        const token = cookie.load('token');
        const self = this;

        if (token) {
            axios.post(apiBaseUrl + '/me?token=' + token, {})
            .then((response) => {
                const { data } = response;
                const { name: signedInUser } = data;

                self.setState({ isLoggedIn: true, signedInUser });
            })
            .catch((error) => {
                self.setState({ isLoggedIn: false, signedInUser: null });
            });
        }
    }

    logIn(biometricId, password) {
        const self = this;
        axios.post(apiBaseUrl + '/login', { biometric_id: biometricId, password })
            .then((response) => {
                const { data } = response;
                const { token } = data;
                cookie.save('token', token);
                
                axios.post(apiBaseUrl + '/me?token=' + token, {})
                    .then((response) => {
                        const { data } = response;
                        const { name: signedInUser } = data;
                        self.setState({ isLoggedIn: true, signedInUser });
                    })
                    .catch((error) => {
                        self.setState({ isLoggedIn: false, signedInUser: null });
                    });
            })
            .catch((error) => {
                self.setState({ isLoggedIn: false, signedInUser: null });
            });
    }

    render() {
        const { isLoggedIn, signedInUser } = this.state;
        return (
            <div>
                { isLoggedIn && 
                <HashRouter>
                    <Root signedInUser={signedInUser}></Root>
                </HashRouter> }

                { !isLoggedIn && 
                    <Login logIn={this.logIn} /> }
            </div>
        );
    }
}

if (document.getElementById('app')) {
    ReactDOM.render(<App />, document.getElementById('app'));
}
