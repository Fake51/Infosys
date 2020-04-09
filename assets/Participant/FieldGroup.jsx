import React, { PureComponent } from "react";
import Field from "./Field";
import styles from "./Search.scss";

class FieldGroup extends PureComponent {
  constructor(props) {
    super(props);
    this.props = props;
  }

  render() {
    const fields = this.props.fields.filter(field => field.removable)

    return <div className={styles.Participant_Search_inputFieldContainer_Group}>
      <h4>{this.props.name}</h4>
      {fields.map(field => <Field key={field.name} name={field.name} displayName={field.displayName} checked={field.checked} onFieldClick={this.props.onFieldClick} />)}
    </div>
  }
}

export default FieldGroup;
