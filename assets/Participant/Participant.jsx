import React, { PureComponent } from "react";
import { Switch, Route } from "react-router-dom";
import Routes from "../routes";
import Search from "./Search";

class Participant extends PureComponent {
  render() {
    return (
      <Switch>
        <Route path={Routes.Participant.Search}>
          <Search />
        </Route>
      </Switch>
    );
  }
}

export default Participant;
