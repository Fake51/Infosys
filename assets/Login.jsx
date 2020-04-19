import React, { PureComponent } from "react";
import { connect } from "react-redux";
import { createJsonAction } from "./api-action";

class Login extends PureComponent {
  constructor(props) {
    super(props);
    this.props = props;

    this.handleLoginClick = this.handleLoginClick.bind(this);
  }

  handleLoginClick(e) {
    e.preventDefault();
    e.stopPropagation();

    this.props.login(
      document.getElementById("Login_Email").value,
      document.getElementById("Login_Password").value
    );
  }

  render() {
    return (
      <div>
        <label htmlFor="Login_Email">Email: <input type="text" name="email" id="Login_Email" /></label>
        <label htmlFor="Login_Password">Password: <input type="password" name="password" id="Login_Password" /></label>
        <button type="submit" onClick={this.handleLoginClick}>Login</button>
      </div>
    );
  }
}

const mapDispatchToProps = dispatch => {
  return {
    login: (email, password) =>
      dispatch(
        createJsonAction(
          {
            endpoint: "/login",
            method: "POST",
            body: JSON.stringify({ email, password })
          },
          "USER_LOGIN"
        )
      )
  }
};

const mapStateToProps = state => {
  const { user } = state;

  return { user };
};

export default connect(mapStateToProps, mapDispatchToProps)(Login);
