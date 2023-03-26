"using strict";

class InfosysTextPreprocessor {
  // Preprocess text
  static process_text(text) {
    text = jQuery('<div>'+text+'</div>').text(); //strip any HTML
    let match;
    let regex = /\[(\w+)(?:=([^\]]+))?\](.*?)\[\/\1\]/s;
    for (match = text.match(regex); match; match = text.match(regex)) {
      text = this.processMatch(text, match);
    }
    text = text.replaceAll("\n", "<br>");
    
    return text;
  }

  static processMatch(text, match) {
    switch (match[1]) {
      case "email":
        return text.replace(match[0], '<a href="mailto:'+match[3]+'">'+match[3]+'</a>');

      case "b":
        return text.replace(match[0], '<strong>'+match[3]+'</strong>');

      case "url":
        let url = match[2].replaceAll('"','\\"');
        return text.replace(match[0], '<a href="'+url+'" target="_blank">'+match[3]+'</a>');

      case "color":
        let [bgcolor, color] = match[2].replaceAll(/[^\w#,]/g, "").split(",",2) ;
        bgcolor = bgcolor ?? "white";
        color = color ?? "black";
        return text.replace(match[0], '<span style="background-color:'+bgcolor+';color:'+color+';">'+match[3]+'</span>');

      default:
        console.log("Unknown token", match);
        return text.replace(match[0], match[3]);
    }
  }
}