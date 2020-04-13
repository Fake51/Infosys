import React, { PureComponent } from "react";

class UnauthorizedUser extends PureComponent {
  render() {
    return <div>You have no access to this feature</div>;
  }
}

export default UnauthorizedUser;
