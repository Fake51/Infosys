import React, { PureComponent } from "react";
import styles from "./HeaderBar.scss";

class HeaderBar extends PureComponent {
  constructor(props) {
    super(props);
    this.props = props;

    this.handleNavToggle = this.handleNavToggle.bind(this);
  }

  navToggleState = false;

  handleNavToggle() {
    const body = document.querySelector("body");

    if (this.navToggleState) {
      body.className = "";
      this.navToggleState = false;
    } else {
      body.className = "navigationClosed";
      this.navToggleState = true;
    }
  }

  render() {
    return (
      <div className={styles.headerBar}>
        Infosys
        <a onClick={this.handleNavToggle}>X</a>
      </div>
    );
  }
}

export default HeaderBar;
