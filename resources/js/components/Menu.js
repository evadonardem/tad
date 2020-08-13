import axios from 'axios';
import cookie from 'react-cookies';
import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { Route, Switch } from 'react-router-dom';
import { Link } from 'react-router-dom';
import {
    Nav,
    Navbar,
    NavDropdown
} from 'react-bootstrap';
import Dashboard from './Dashboard';
import AttendanceLogs from './AttendanceLogs';
import Reports from './Reports';
import Users from './Users';
import Settings from './Settings';
import SettingsCommonTimeShifts from './SettingsCommonTimeShifts';
import SettingsUserRoles from './SettingsUserRoles';
import ReportsLateUndertime from './ReportsLateUndertime';
import ReportsAbsencesNoTimeInOut from './ReportsAbsencesNoTimeInOut';
import AttendanceLogOverrides from './AttendanceLogOverrides';

export default class Menu extends Component {
    constructor(props) {
        super(props);
        this.state = {
            brand: '',
            links: [],
        };

        this.handleLogout = this.handleLogout.bind(this);
    }

    componentDidMount() {
        const token = cookie.load('token');
        const self = this;
        axios.get(apiBaseUrl + '/navigation-menu?token=' + token)
            .then((response) => {
                const { data } = response.data;
                const { brand, links } = data;
                
                self.setState({ brand, links });
            }).catch((error) => {
                location.href = appBaseUrl + '/login';
            });
    }

    handleLogout(e) {
        const token = cookie.load('token');
        axios.post(apiBaseUrl + '/logout?token=' + token, { })
            .then((response) => {
                location.reload();
            }).catch((error) => {
                location.reload();
            });
    }

    render() {
        const { brand, links } = this.state;
        const { signedInUser } = this.props;
        let menuIndex = 0;
        let submenuIndex = 0;
        let routeIndex = 0;
        return (
            <div>
                <Navbar bg="dark" expand="lg" variant="dark">
                    <Navbar.Brand href="#">{brand && brand}</Navbar.Brand>
                    <Navbar.Toggle aria-controls="basic-navbar-nav"/>
                    <Navbar.Collapse id="basic-navbar-nav">
                        <Nav className="mr-auto">
                            { links && links.map((link) => {
                                    if (link.links) {
                                        const dropdownItems = link.links && link.links.map((dropdownLink) =>
                                        <NavDropdown.Item
                                            key={'menu-' + menuIndex + '-dropdown-item-' + submenuIndex++}
                                            href={dropdownLink.url}>
                                            {dropdownLink.label}
                                        </NavDropdown.Item>);
                                        return (
                                            <NavDropdown key={'menu-' + menuIndex + '-dropdown'} title={link.label} id="collapsible-nav-dropdown">
                                                {dropdownItems}
                                            </NavDropdown>
                                        );
                                    } else {
                                        return (<Link key={'menu-' + menuIndex++} className={'nav-link'} to={link.to}>
                                            <i className={link.icon}></i> {link.label}
                                        </Link>);
                                    }
                                }
                            ) }
                        </Nav>
                        <Nav>
                            <NavDropdown title={signedInUser}>
                                <NavDropdown.Item
                                    href="#">
                                    <i className="fa fa-key"> Change password</i>
                                </NavDropdown.Item>
                                <NavDropdown.Item
                                    href="#" onClick={this.handleLogout}>
                                    <i className="fa fa-sign-out"> Sign-out</i>
                                </NavDropdown.Item>
                            </NavDropdown>
                        </Nav>
                    </Navbar.Collapse>
                </Navbar>
                <Switch>
                    <Route key={'route-default'} exact path={'/'} component={Dashboard}></Route>
                    { links && links.map((link) => {
                        let routeToComponent = null;
                        switch(link.to) {
                            case '/dashboard':
                                routeToComponent = <Dashboard />;
                                break;
                            case '/attendance-logs':
                                routeToComponent = <AttendanceLogs />;
                                break;
                            case '/attendance-log-overrides':
                                routeToComponent = <AttendanceLogOverrides />;
                                break;
                            case '/reports':
                                routeToComponent = <Reports />;
                                break;
                            case '/users':
                                routeToComponent = <Users />;
                                break;
                            case '/settings':
                                routeToComponent = <Settings />;
                                break;
                            default:
                                routeToComponent = <Dashboard />
                        }
                        
                        return (<Route key={'route-' + routeIndex++} path={link.to} component={() => routeToComponent}>
                        </Route>); }
                    )}
                    <Route key={'route-default'} path={'/reports-late-undertime'} component={ReportsLateUndertime}></Route>
                    <Route key={'route-default'} path={'/reports-absences-no-time-in-out'} component={ReportsAbsencesNoTimeInOut}></Route>
                    <Route key={'route-default'} path={'/settings-common-time-shifts'} component={SettingsCommonTimeShifts}></Route>
                    <Route key={'route-default'} path={'/settings-user-roles'} component={SettingsUserRoles}></Route>
                </Switch>
            </div>
        );
    }
}

if (document.getElementById('menu')) {
    ReactDOM.render(<Menu />, document.getElementById('menu'));
}
