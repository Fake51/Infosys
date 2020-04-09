import React, { PureComponent } from "react";

class Field extends PureComponent {
  constructor(props) {
    super(props);
    this.props = props;
  }

  render() {
    return <span><input type="checkbox" value="1" name={this.props.name} data-displayname={this.props.displayName} defaultChecked={this.props.checked} className="Participant_Search_inputFieldContainer_field" onClick={this.props.onFieldClick} />{this.props.displayName}</span>
  }
}

export default Field;
