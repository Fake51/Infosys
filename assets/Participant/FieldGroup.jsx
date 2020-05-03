import React, { PureComponent } from "react";
import PropTypes from "prop-types";
import Field from "./Field";
import styles from "./Search.scss";

class FieldGroup extends PureComponent {
  constructor(props) {
    super(props);
    this.props = props;
  }

  render() {
    const { fields, onFieldClick, name } = this.props;
    const removableFields = fields.filter(field => field.removable);

    return (
      <div className={styles.Participant_Search_inputFieldContainer_Group}>
        <h4>{name}</h4>
        {removableFields.map(field => (
          <Field
            key={field.name}
            name={field.name}
            displayName={field.displayName}
            checked={field.checked}
            onFieldClick={onFieldClick}
          />
        ))}
      </div>
    );
  }
}

FieldGroup.propTypes = {
  fields: PropTypes.shape([]),
  onFieldClick: PropTypes.func,
  name: PropTypes.string
};

export default FieldGroup;
