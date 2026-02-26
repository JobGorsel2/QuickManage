function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('#img').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
}

$("#imgInp").change(function() {
    readURL(this);
});


function showFile(input) {
    let file = input.files[0];
    document.getElementById("file_name").innerHTML= file.name;

}


// update page add paramters
var formfield = document.getElementById('formfield');
var formfield2 = document.getElementById('formfield2');
var x = []
function addField() {

  var newField = document.createElement("input");
  newField.setAttribute('type','text');
  newField.setAttribute('name','param');
  newField.setAttribute('class','param_field');

  newField.setAttribute('placeholder','Parameter...');
  formfield.appendChild(newField);
  x.push(newField);
  console.log(x)
}

function removeField(){
    var input_tags = formfield.getElementsByTagName('input');
    if(input_tags.length > 0) {
      formfield.removeChild(input_tags[(input_tags.length) - 1]);
      
    }
  }

// copy text to clipboard function 
function copyText() {
  // Get the text field
  var copyText = document.getElementById("pageUrl");
//    // Copy the text inside the text field
  navigator.clipboard.writeText(copyText.href);
}



