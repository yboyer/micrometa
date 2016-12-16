var linkedElements = {
  // metadatadeluxe.pbworks.com for metadata correlation
  'Titre': ['XMP:Title', 'IPTC:ObjectName'],
  'Synopsis': ['XMP:Headline', 'IPTC:Headline'],
  'Description': ['XMP:Description', 'IPTC:Caption-Abstract', 'EXIF:ImageDescription'],
  'Auteur': ['XMP:Creator', 'IPTC:By-line', 'EXIF:Artist'],
  'Mots-clé': ['XMP:Subject', 'IPTC:Keywords'],
  'Ville': ['XMP:City', 'IPTC:City'],
  'Département': ['XMP:State', 'IPTC:Provice-State'],
  'Pays': ['XMP:Country', 'IPTC:Country'],
  'Copyrights': ['XMP:Rights', 'IPTC:CopyrightNotice', 'EXIF:Copyright'],
  'Date de création': ['XMP:CreateDate', 'IPTC:DateCreated', 'EXIF:DateTimeOriginal'],

  'Crédit': ['XMP:Credit', 'IPTC:Credit'],
  'Source': ['XMP:Source', 'IPTC:Source']
};

// Catch form submition
document.forms[0].onsubmit = function(e) {
  e.preventDefault();

  return checkForm();
};


function checkForm() {

  // List of object array
  var incorrectValues = [];

  for (var label in linkedElements) {
    if (linkedElements.hasOwnProperty(label)) {
      var data = {};

      // Retrieve inputs
      for (var i = linkedElements[label].length - 1; i >= 0; i--) {
        // Check if the tag exists on the input list
        var element = document.querySelector('#form_' + linkedElements[label][i].replace(':', '---'));
        if (element) {
          data[linkedElements[label][i].split(':')[0]] = element;
        }
      }

      // Checks if value of inputs are the same
      // If not it adds the group into the incorrectValues array
      if (Object.keys(data).length > 1) {
        var lastKey = undefined;

        for (var categ in data) {
          if (data.hasOwnProperty(categ)) {
            if (lastKey === undefined) {
              lastKey = data[categ].value;
            }

            if (data[categ].value !== lastKey) {
              incorrectValues.push({
                label: label,
                data: data
              });
            }

            lastKey = data[categ].value;
          }
        }
      }
    }
  }

  // If there is some differences between inputs it creates the difftable
  if (incorrectValues.length !== 0) {
    createDiffTable(incorrectValues);
    document.body.scrollTop = document.documentElement.scrollTop = 0;
    return false;
  }

  return true;
}

/**
 * Creates the diff table
 */
function createDiffTable(data) {
  var difftable = document.querySelector('#difftable');

  // Retrieve the list of header (<th>) to display
  var headers = [];
  for (var d = data.length - 1; d >= 0; d--) {
    var keys = Object.keys(data[d].data);
    for (var k = keys.length - 1; k >= 0; k--) {
      if (!~headers.indexOf(keys[k])) {
        headers.push(keys[k]);
      }
    }
  }
  headers.sort();

  // Make the table
  var html = '<hr>';
  html += '<label>Gestion de différences</label>';
  html += '<table>';
  html += '  <thead>';
  for (var h = 0; h < headers.length; h++) {
    html += '    <th>' + headers[h] + '</th>';
  }
  html += '  </thead>';
  html += '  <tbody>';
  html += '  </tbody>';
  html += '</table>';
  html += '<button id="diffsubmit">VALIDER</button>';
  html += '<hr>';
  difftable.innerHTML = html;

  // Appends the label line and the diff buttons
  for (var d = 0; d < data.length; d++) {
    // Label line
    var label = document.createElement('tr');
    label.innerHTML = '<td colspan="3"><label>' + data[d].label + '</label></td>';
    difftable.querySelector('tbody').appendChild(label);

    // Diff line
    var line = document.createElement('tr');
    line.classList.add('buttons');

    // Diff buttons
    for (var h = 0; h < headers.length; h++) {
      var element = data[d].data[headers[h]];

      var td = document.createElement('td');
      td.classList.add('line');

      if (element) {
        var button = document.createElement('button');
        button.textContent = element.value;

        button.inputRef = element;
        button.onclick = uncheckLine;

        td.appendChild(button);
      }

      line.appendChild(td);
    }

    difftable.querySelector('tbody').appendChild(line);
  }

  difftable.querySelector('#diffsubmit').onclick = validateDiff;
}

/**
 * Add a radio buttons style for the diff buttons (only one button can be checked)
 */
function uncheckLine() {
  var checkedButtons = this.parentNode.parentNode.querySelectorAll('button.checked');
  for (var c = checkedButtons.length - 1; c >= 0; c--) {
    if (checkedButtons[c] !== this) {
      checkedButtons[c].classList.remove('checked');
    }
  }

  this.classList.toggle('checked');
}


/**
 * Replace inputs values by the correct one
 */
function validateDiff() {
  var lines = document.querySelectorAll('.buttons');

  for (var l = lines.length - 1; l >= 0; l--) {
    var correctButton = lines[l].querySelector('button.checked');

    // If there is a checked button on the line
    if (correctButton) {
      var buttonsToModify = lines[l].querySelectorAll('button:not(.checked)');

      // Replace input contents
      for (var b = buttonsToModify.length - 1; b >= 0; b--) {
        console.log(buttonsToModify[b].inputRef, buttonsToModify[b].inputRef.value, correctButton.textContent);
        buttonsToModify[b].inputRef.value = correctButton.textContent;
      }
    }
  }

  if (checkForm()) {
    document.forms[0].submit();
  }
}
