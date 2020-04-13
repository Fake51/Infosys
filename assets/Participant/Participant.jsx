import React, { PureComponent } from "react";
import { Switch, Route } from "react-router-dom";
import Routes from "../routes";
import Search from "./Search";
import Edit from "./Edit";

class Participant extends PureComponent {
  render() {
    return (
      <Switch>
        <Route path={Routes.Participant.Create}>
          <Edit />
        </Route>
        <Route path={Routes.Participant.Edit}>
          <Edit />
        </Route>
        <Route path={Routes.Participant.Search}>
          <Search />
        </Route>
      </Switch>
    );
  }
}

export default Participant;
