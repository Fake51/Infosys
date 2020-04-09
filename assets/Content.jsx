import React, { PureComponent } from "react";
import { Switch, Route } from "react-router-dom";
import Routes from "./routes";
import Participant from "./Participant/Participant";

class Content extends PureComponent {
  render() {
    return (
      <div>
        <Switch>
          <Route exact path={Routes.Home}>
            Home
          </Route>
          <Route path={Routes.Participant.base}>
            <Participant />
          </Route>
        </Switch>
      </div>
    );
  }
}

export default Content;
