import React, { PureComponent } from "react";
import { Connect } from "react-redux";
import styles from "./HeaderBar.scss";


class HeaderBar extends PureComponent {
  navToggleState = false;

  constructor(props) {
    super(props);
    this.props = props;

    this.handleNavToggle = this.handleNavToggle.bind(this);
  }

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
        <span onClick={this.handleNavToggle}>X</span>
      </div>
    );
  }
}

export default HeaderBar;
