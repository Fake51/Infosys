import React, { PureComponent } from "react";
import { Link } from "react-router-dom";
import Routes from "./routes";
import "./Navigation.scss";

class Navigation extends PureComponent {
  render() {
    return (
      <nav className="navigation">
        <ul>
          <li>
            <Link to={Routes.Home}>Dashboard</Link>
          </li>
          <li>
            <span>Participant</span>
            <ul>
              <li>
                <Link to={Routes.Participant.Create}>create</Link>
              </li>
              <li>
                <Link to={Routes.Participant.Search}>search</Link>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
    );
  }
}

export default Navigation;
