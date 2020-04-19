import React, { PureComponent } from "react";
import { connect } from "react-redux";
import Login from "./Login";
import Content from "./Content";

class ContentWrapper extends PureComponent {
  render() {
    const { user } = this.props;

    return user.token ? <Content /> : <Login />;
  }
}

const mapStateToProps = state => {
  const { user } = state;

  return { user };
};

export default connect(mapStateToProps)(ContentWrapper);
