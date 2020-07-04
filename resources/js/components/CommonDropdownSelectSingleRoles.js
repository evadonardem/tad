import React, { Component } from 'react';
import { Form } from 'react-bootstrap';
import cookie from 'react-cookies';
import Select from 'react-select';

export default class CommonDropdownSelectSingleRoles extends Component {
    constructor(props) {
        super(props);
        this.state = {
            options: []
        };
    }

    componentDidMount() {
        const token = cookie.load('token');
        const self = this;

        if (token) {
            axios.get(apiBaseUrl + '/settings/roles?token=' + token, {})
                .then((response) => {
                    const { data: roles } = response.data;
                    const options = roles.map((role) => { return { value: role.id, label: role.id } });
                    self.setState({ options });
                })
                .catch((error) => {
                    location.reload();
                });
        }
    }

    render() {
        const { name, selectedRole, handleChange } = this.props;
        const { options } = this.state;

        return (
            <Form.Group>
                <Form.Label>Select Role:</Form.Label>
                {
                    options &&
                    <Select name={name} isClearable options={options} value={selectedRole} onChange={handleChange}/>
                }
                <div className="invalid-feedback d-block"></div>
            </Form.Group>
        );
    }
}
