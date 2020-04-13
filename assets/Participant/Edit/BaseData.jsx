import React, { PureComponent } from "react";
import { Switch, Route, withRouter } from "react-router-dom";
import { connect } from "react-redux";

class BaseData extends PureComponent {
  render() {
    return <span>data here</span>;
  }
}

export default BaseData;
