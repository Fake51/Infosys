import React, { PureComponent } from "react";
import { connect } from "react-redux";

class Login extends PureComponent {
  render() {
    return <div>Login form</div>;
  }
}

export default connect()(Login);
