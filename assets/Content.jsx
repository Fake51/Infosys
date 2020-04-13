import React, { PureComponent } from "react";
import { Switch, Route, withRouter } from "react-router-dom";
import { connect } from "react-redux";
import Routes from "./routes";
import Participant from "./Participant/Participant";
import styles from "./Content.scss";
import UnauthorizedUser from "./UnauthorizedUser";

class Content extends PureComponent {
  render() {
    return (
      <div>
        { this.props.user ?
          <Switch>
            <Route exact path={Routes.Home}>
              Home
            </Route>
            <Route path={Routes.Participant.base}>
              <Participant />
            </Route>
          </Switch> :
          <UnauthorizedUser />
        }
      </div>
    );
  }
}

const mapStateToProps = state => {
  const { user } = state;

  return { user };
};

export default withRouter(connect(mapStateToProps)(Content));
