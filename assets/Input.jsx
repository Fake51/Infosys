import React, { PureComponent } from "react";
import PropTypes from "prop-types";

class Input extends PureComponent {
  render() {
    const { name, value, readOnly, label } = this.props;

    return (
      <span>
        <label htmlFor={`${name}_edit`}>{label}:</label>
        {readOnly ? (
          <span id={`${name}_edit`}>{value}</span>
        ) : (
          <input id={`${name}_edit`} value={value} name={name} />
        )}
      </span>
    );
  }
}

Input.propTypes = {
  name: PropTypes.string,
  value: PropTypes.string,
  size: PropTypes.number,
  readOnly: PropTypes.bool
};

export default Input;
