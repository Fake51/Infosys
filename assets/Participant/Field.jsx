import React, { PureComponent } from "react";
import PropTypes from "prop-types";

class Field extends PureComponent {
  constructor(props) {
    super(props);
    this.props = props;
  }

  render() {
    const { name, displayName, checked, onFieldClick } = this.props;

    return (
      <span>
        <input
          type="checkbox"
          value="1"
          name={name}
          data-displayname={displayName}
          defaultChecked={checked}
          className="Participant_Search_inputFieldContainer_field"
          onClick={onFieldClick}
        />
        {displayName}
      </span>
    );
  }
}

Field.propTypes = {
  name: PropTypes.string,
  displayName: PropTypes.string,
  checked: PropTypes.bool,
  onFieldClick: PropTypes.func
};

export default Field;
