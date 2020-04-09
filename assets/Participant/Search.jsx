import React, { PureComponent } from "react";
import DataTable from "react-data-table-component";
import { connect } from "react-redux";
import { createJsonAction } from "../api-action";
import FieldGroup from "./FieldGroup";
import styles from "./Search.scss";

// todo get available fields from api
// style

let chosenFields = [ "participant__id" ];

let columns = [
  {
    name: "Id",
    selector: "participant__id",
    sortable: true
  }
];

class ParticipantSearch extends PureComponent {
  constructor(props) {
    super(props);
    this.props = props;

    this.handleSearch = this.handleSearch.bind(this);
    this.handleFieldGroupsToggle = this.handleFieldGroupsToggle.bind(this);
    this.handleFieldClick = this.handleFieldClick.bind(this);

    if (!this.props.searchMeta || this.props.searchMeta.length === 0) {
      this.props.fetchSchema();
    }
  }

  runSearch(term) {
    const { search } = this.props;

    search(term, chosenFields);
  }

  handleSearch(e) {
    if (!e) {
      return;
    }

    if (e.keyCode) {
      if (e.keyCode === 13) {
        this.runSearch(e.target.value);
      }
    } else {
      this.runSearch(e.target.previousSibling.value);
    }
  }

  handleFieldClick(e) {
    const fieldName = e.target.getAttribute("name");

    if (!e.target.checked) {
      chosenFields = chosenFields.filter(name => name !== fieldName);
      columns = columns.filter(column => column.selector !== fieldName);
    } else {
      chosenFields.push(fieldName);
      columns.push({
        name: e.target.getAttribute("data-displayname"),
        sortable: true,
        selector: fieldName
      });
    }
  }

  handleFieldGroupsToggle(e) {
    const parent = e.target.parentElement;
    const classes = parent.getAttribute('class');

    if (classes.search(/Inactive/) > -1) {
      parent.setAttribute('class', classes.replace(styles.Participant_Search_inputFieldContainerInactive, ''));
    } else {
      parent.setAttribute('class', `${classes} ${styles.Participant_Search_inputFieldContainerInactive}`);
    }
  }

  render() {
    const fieldGroups = this.props.searchMeta.filter(fieldGroup => fieldGroup && fieldGroup.fields.length > 0);
console.log(columns);
    return (
      <div>
        <div className="Participant_Search_input">
          <input className="Participant_Search_inputTerms" onKeyUp={this.handleSearch} />
          <button type="submit" className="Participant_Search_inputGo" onClick={this.handleSearch}>Search</button>
          <div className={`Participant_Search_inputFieldContainer ${styles.Participant_Search_inputFieldContainerInactive}`}>
            <button type="button" className="Participant_Search_inputFieldContainer_toggle" onClick={this.handleFieldGroupsToggle}>Fields</button>
            {fieldGroups.map(group => <FieldGroup key={group.name} onFieldClick={this.handleFieldClick} name={group.name} fields={group.fields} />)}
          </div>
        </div>
        {this.props.searchResult.length > 0 ? <DataTable title="Arnold Movies" columns={columns} data={this.props.searchResult} /> : <span>No results from search to display</span>}
      </div>
    );
  }
}

const mapStateToProps = state => {
  const { participant: { searchResult, searchMeta } } = state;

  return { searchResult, searchMeta };
};
const mapDispatchToProps = dispatch => {
  return {
    search: (terms, fields) => dispatch(createJsonAction({
      endpoint: "/api/v1/participants",
      method: "POST",
      body: JSON.stringify({terms, fields})
    }, "PARTICIPANT_SEARCH")),
    fetchSchema: () => dispatch(createJsonAction({
      endpoint: "/api/v1/participants",
      method: "OPTIONS"
    }, "PARTICIPANT_SEARCH_META"))
  };
};

export default connect(mapStateToProps, mapDispatchToProps)(ParticipantSearch);
