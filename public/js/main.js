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

// copy text to clipboard function 
function copyText() {
  // Get the text field
  var copyText = document.getElementById("pageUrl");
//    // Copy the text inside the text field
  navigator.clipboard.writeText(copyText.href);
}



